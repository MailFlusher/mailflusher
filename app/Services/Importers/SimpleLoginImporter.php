<?php

namespace App\Services\Importers;

use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class SimpleLoginImporter extends BaseImporter
{
    public function slug(): string
    {
        return 'simplelogin';
    }

    public function name(): string
    {
        return 'SimpleLogin';
    }

    public function dryRun(User $user, string $token): array
    {
        $aliases = $this->fetchAll($token);

        $samples = [];
        foreach (array_slice($aliases, 0, 5) as $a) {
            $samples[] = [
                'email' => $a['email'],
                'description' => $a['note'] ?? $a['name'] ?? null,
                'active' => (bool) ($a['enabled'] ?? true),
            ];
        }

        $limit = $user->planLimit('aliases');
        $existing = $user->aliases()->count();
        $room = is_null($limit) ? PHP_INT_MAX : max(0, $limit - $existing);
        $importable = min(count($aliases), $room);

        return [
            'total' => count($aliases),
            'importable' => $importable,
            'skipped' => max(0, count($aliases) - $importable),
            'samples' => $samples,
        ];
    }

    public function import(User $user, string $token): array
    {
        $aliases = $this->fetchAll($token);

        $imported = 0;
        $skippedOverLimit = 0;
        $errors = [];

        foreach ($aliases as $a) {
            $result = $this->createAlias(
                $user,
                $a['email'],
                $a['note'] ?? $a['name'] ?? null,
                (bool) ($a['enabled'] ?? true),
            );

            if ($result['created']) {
                $imported++;

                continue;
            }

            if ($result['reason'] === 'limit_reached') {
                $skippedOverLimit++;
            }
        }

        return [
            'imported' => $imported,
            'skipped_over_limit' => $skippedOverLimit,
            'errors' => $errors,
        ];
    }

    /**
     * Walks the SimpleLogin pagination until all aliases are collected.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function fetchAll(string $token): array
    {
        $all = [];
        $pageId = 0;
        $maxPages = 50; // safety cap

        while ($maxPages-- > 0) {
            try {
                $response = Http::withHeaders([
                    'Authentication' => $token,
                    'Accept' => 'application/json',
                ])->timeout(15)->get('https://app.simplelogin.io/api/v2/aliases', [
                    'page_id' => $pageId,
                ]);
            } catch (RequestException $e) {
                throw new \RuntimeException('SimpleLogin API request failed: '.$e->getMessage());
            }

            if (! $response->successful()) {
                throw new \RuntimeException('SimpleLogin API returned '.$response->status());
            }

            $page = $response->json('aliases') ?? [];
            if (empty($page)) {
                break;
            }

            $all = array_merge($all, $page);
            $pageId++;
        }

        return $all;
    }
}
