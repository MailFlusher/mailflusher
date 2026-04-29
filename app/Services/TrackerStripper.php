<?php

namespace App\Services;

use App\Models\Alias;
use App\Models\RedirectToken;
use Illuminate\Support\Str;

class TrackerStripper
{
    /**
     * Number of trackers / links touched by the last operation.
     * Useful for telemetry and the "trackers removed" banner.
     */
    public int $pixelsRemoved = 0;

    public int $linksRewritten = 0;

    /**
     * Remove tracking pixels from HTML. Returns modified HTML.
     *
     * A "tracking pixel" here is any <img> that:
     * - has width/height ≤ 2 on both axes, or
     * - points at a domain on config('trackers.pixel_domains').
     */
    public function stripPixels(string $html): string
    {
        $this->pixelsRemoved = 0;

        if (trim($html) === '') {
            return $html;
        }

        $doc = $this->loadHtml($html);
        if (! $doc) {
            return $html;
        }

        $imgs = iterator_to_array($doc->getElementsByTagName('img'));
        foreach ($imgs as $img) {
            if ($this->isTrackingPixel($img)) {
                $img->parentNode?->removeChild($img);
                $this->pixelsRemoved++;
            }
        }

        if ($this->pixelsRemoved === 0) {
            return $html;
        }

        return $this->saveHtml($doc);
    }

    /**
     * Strip known tracking query parameters from a URL.
     */
    public function stripTrackingParams(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['query'])) {
            return $url;
        }

        parse_str($parts['query'], $params);
        $tracking = array_flip(config('trackers.tracking_params', []));

        $cleaned = array_filter($params, fn ($_, $key) => ! isset($tracking[strtolower($key)]), ARRAY_FILTER_USE_BOTH);

        $parts['query'] = http_build_query($cleaned);

        return $this->unparseUrl($parts);
    }

    /**
     * Rewrite every rewritable <a href> to go through the /r/{token} proxy.
     * The target URL is remembered and cleaned of tracking params at click-time
     * by RedirectController. Returns modified HTML.
     */
    public function proxyLinks(string $html, ?Alias $alias = null): string
    {
        $this->linksRewritten = 0;

        if (trim($html) === '') {
            return $html;
        }

        $doc = $this->loadHtml($html);
        if (! $doc) {
            return $html;
        }

        $base = rtrim((string) config('app.url'), '/');

        $anchors = iterator_to_array($doc->getElementsByTagName('a'));
        foreach ($anchors as $a) {
            $href = $a->getAttribute('href');
            if (! $this->isRewritable($href)) {
                continue;
            }

            $token = RedirectToken::mint($alias?->id, $href);
            $a->setAttribute('href', $base.'/r/'.$token->token);
            $this->linksRewritten++;
        }

        if ($this->linksRewritten === 0) {
            return $html;
        }

        return $this->saveHtml($doc);
    }

    /**
     * Rewrite <a href> and bare URL mentions in HTML so that every non-mailto,
     * non-anchor, non-unsubscribe link is cleaned of tracking parameters.
     *
     * This is the "safe" mode that does NOT proxy the user's clicks through
     * our server — it just strips tracking params in place.
     */
    public function cleanLinkParams(string $html): string
    {
        $this->linksRewritten = 0;

        if (trim($html) === '') {
            return $html;
        }

        $doc = $this->loadHtml($html);
        if (! $doc) {
            return $html;
        }

        $anchors = iterator_to_array($doc->getElementsByTagName('a'));
        foreach ($anchors as $a) {
            $href = $a->getAttribute('href');
            if (! $this->isRewritable($href)) {
                continue;
            }

            $cleaned = $this->stripTrackingParams($href);
            if ($cleaned !== $href) {
                $a->setAttribute('href', $cleaned);
                $this->linksRewritten++;
            }
        }

        if ($this->linksRewritten === 0) {
            return $html;
        }

        return $this->saveHtml($doc);
    }

    private function isTrackingPixel(\DOMElement $img): bool
    {
        $width = (int) $img->getAttribute('width');
        $height = (int) $img->getAttribute('height');
        if ($width > 0 && $width <= 2 && $height > 0 && $height <= 2) {
            return true;
        }

        $src = $img->getAttribute('src');
        if ($src === '') {
            return false;
        }

        $parts = parse_url($src);
        $host = strtolower($parts['host'] ?? '');
        if ($host === '') {
            return false;
        }

        foreach (config('trackers.pixel_domains', []) as $domain) {
            $domain = strtolower($domain);
            if ($host === $domain || Str::endsWith($host, '.'.$domain)) {
                return true;
            }
        }

        return false;
    }

    private function isRewritable(string $href): bool
    {
        if ($href === '') {
            return false;
        }

        $lower = strtolower($href);
        if (Str::startsWith($lower, ['mailto:', 'tel:', 'javascript:', '#'])) {
            return false;
        }

        // Only rewrite http/https URLs
        return Str::startsWith($lower, ['http://', 'https://']);
    }

    private function loadHtml(string $html): ?\DOMDocument
    {
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $loaded = $doc->loadHTML(
            '<?xml encoding="utf-8"?>'.$html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();

        return $loaded ? $doc : null;
    }

    private function saveHtml(\DOMDocument $doc): string
    {
        $html = $doc->saveHTML();
        // Drop the synthetic xml prolog we used to force UTF-8 parsing
        return str_replace('<?xml encoding="utf-8"?>', '', (string) $html);
    }

    private function unparseUrl(array $parts): string
    {
        $scheme = isset($parts['scheme']) ? $parts['scheme'].'://' : '';
        $user = $parts['user'] ?? '';
        $pass = isset($parts['pass']) ? ':'.$parts['pass'] : '';
        $auth = $user ? $user.$pass.'@' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $scheme.$auth.$host.$port.$path.$query.$fragment;
    }
}
