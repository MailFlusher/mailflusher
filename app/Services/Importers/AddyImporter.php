<?php

namespace App\Services\Importers;

use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class AddyImporter extends BaseImporter
{
    public function slug(): string
    {
        return 'addy';
    }

    public function name(): string
    {
        return 'Addy.io';
    }

    public function dryRun(User $user, string $token): array
    {
        $aliases = $this->fetchAll($token);

        $samples = [];
        foreach (array_slice($aliases, 0, 5) as $a) {
            $samples[] = [
                'email' => $a['email'],
                'description' => $a['description'] ?? null,
                'active' => (bool) ($a['active'] ?? true),
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
                $a['description'] ?? null,
                (bool) ($a['active'] ?? true),
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
     * Walks Addy.io's page-number pagination (100 per page).
     *
     * @return array<int,array<string,mixed>>
     */
    protected function fetchAll(string $token): array
    {
        $all = [];
        $page = 1;
        $maxPages = 50;

        while ($maxPages-- > 0) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ])->timeout(15)->get('https://app.addy.io/api/v1/aliases', [
                    'page[number]' => $page,
                    'page[size]' => 100,
                ]);
            } catch (RequestException $e) {
                throw new \RuntimeException('Addy.io API request failed: '.$e->getMessage());
            }

            if (! $response->successful()) {
                throw new \RuntimeException('Addy.io API returned '.$response->status());
            }

            $batch = $response->json('data') ?? [];
            if (empty($batch)) {
                break;
            }

            $all = array_merge($all, $batch);

            $lastPage = $response->json('meta.last_page');
            if ($lastPage !== null && $page >= (int) $lastPage) {
                break;
            }

            $page++;
        }

        return $all;
    }
}
