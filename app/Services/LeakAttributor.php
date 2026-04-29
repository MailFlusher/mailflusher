<?php

namespace App\Services;

use App\Models\Alias;
use App\Models\AliasLeakEvent;
use App\Models\AliasSenderObservation;
use App\Services\WebhookDispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class LeakAttributor
{
    /**
     * Record that `$alias` just received an email from `$senderEmail`, and
     * create a leak event if the sender is unexpected.
     *
     * Safe to call on every forward. All work is best-effort; failures here
     * must never block email delivery.
     */
    public function record(Alias $alias, ?string $senderEmail): void
    {
        $senderDomain = $this->extractDomain($senderEmail);

        if (! $senderDomain) {
            return;
        }

        $observation = $this->upsertObservation($alias, $senderDomain);

        $this->maybeLockBaseline($alias, $senderDomain);

        if ($this->shouldCreateLeakEvent($alias, $senderDomain)) {
            $event = AliasLeakEvent::firstOrCreate(
                ['alias_id' => $alias->id, 'sender_domain' => $senderDomain],
                ['detected_at' => now(), 'status' => 'pending'],
            );

            // Only fire webhook when a genuinely new leak event was created.
            if ($event->wasRecentlyCreated) {
                try {
                    app(WebhookDispatcher::class)->dispatch($alias->user, 'alias.leaked', [
                        'alias_id' => $alias->id,
                        'alias_email' => $alias->email,
                        'sender_domain' => $senderDomain,
                        'baseline_sender_domain' => $alias->baseline_sender_domain,
                        'detected_at' => $event->detected_at->toIso8601String(),
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('WebhookDispatcher leaked failed', ['error' => $e->getMessage()]);
                }
            }
        }
    }

    /**
     * Lower-case, strip subdomains, drop angle brackets.
     * Returns null if no domain can be determined.
     */
    public function extractDomain(?string $email): ?string
    {
        if (! $email) {
            return null;
        }

        // Strip e.g. "Brand <hello@brand.com>" to "hello@brand.com"
        if (preg_match('/<([^>]+)>/', $email, $m)) {
            $email = $m[1];
        }

        $at = strrpos($email, '@');
        if ($at === false) {
            return null;
        }

        return strtolower(trim(substr($email, $at + 1)));
    }

    public function isEspDomain(string $domain): bool
    {
        foreach (config('leak_attribution.esp_domains', []) as $esp) {
            if ($domain === $esp || Str::endsWith($domain, '.'.$esp)) {
                return true;
            }
        }

        return false;
    }

    private function upsertObservation(Alias $alias, string $domain): AliasSenderObservation
    {
        $observation = AliasSenderObservation::where('alias_id', $alias->id)
            ->where('sender_domain', $domain)
            ->first();

        if ($observation) {
            $observation->increment('email_count');
            $observation->last_seen_at = now();
            $observation->save();

            return $observation;
        }

        return AliasSenderObservation::create([
            'alias_id' => $alias->id,
            'sender_domain' => $domain,
            'email_count' => 1,
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);
    }

    private function maybeLockBaseline(Alias $alias, string $currentSender): void
    {
        if ($alias->baseline_locked_at) {
            return;
        }

        // Lock as soon as we see our first sender (so the baseline captures
        // the first brand the alias was used for). This is intentionally
        // aggressive: we'd rather miss a legitimate second-sender learning
        // window than miss an early leak.
        if (! $alias->baseline_sender_domain) {
            $alias->baseline_sender_domain = $currentSender;
        }

        $distinctSenders = AliasSenderObservation::where('alias_id', $alias->id)->count();
        $lockAfterSenders = (int) config('leak_attribution.baseline_lock_after_senders', 3);
        $lockAfterDays = (int) config('leak_attribution.baseline_lock_after_days', 14);
        $aliasAge = $alias->created_at ? $alias->created_at->diffInDays(now()) : 0;

        if ($distinctSenders >= $lockAfterSenders || $aliasAge >= $lockAfterDays) {
            $alias->baseline_locked_at = now();
        }

        $alias->save();
    }

    private function shouldCreateLeakEvent(Alias $alias, string $senderDomain): bool
    {
        // No baseline yet — still learning, not a leak.
        if (! $alias->baseline_locked_at || ! $alias->baseline_sender_domain) {
            return false;
        }

        // Same as baseline — expected sender.
        if ($senderDomain === $alias->baseline_sender_domain) {
            return false;
        }

        // Same apex domain (e.g. email.brand.com vs brand.com) — expected.
        if ($this->sharesApex($senderDomain, $alias->baseline_sender_domain)) {
            return false;
        }

        // Known ESP — they pass for many brands, not a leak signal.
        if ($this->isEspDomain($senderDomain)) {
            return false;
        }

        return true;
    }

    private function sharesApex(string $a, string $b): bool
    {
        $apexA = $this->apex($a);
        $apexB = $this->apex($b);

        return $apexA === $apexB;
    }

    private function apex(string $domain): string
    {
        $parts = explode('.', $domain);
        if (count($parts) <= 2) {
            return $domain;
        }

        return implode('.', array_slice($parts, -2));
    }
}
