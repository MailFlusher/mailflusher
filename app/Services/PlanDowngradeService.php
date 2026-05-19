<?php

namespace App\Services;

use App\Models\Alias;
use App\Models\Recipient;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanDowngradeService
{
    /**
     * Deactivate items that would exceed the target plan's limits.
     * Oldest items are preserved; excess items by creation order are deactivated.
     * The user's default recipient is never deactivated.
     *
     * @return array{aliases:int,recipients:int,rules:int,domains:int}
     */
    public function downgrade(User $user, string $targetPlan): array
    {
        $limits = config("mailflusher.plans.{$targetPlan}");
        if (! $limits) {
            throw new \InvalidArgumentException("Unknown plan: {$targetPlan}");
        }

        $deactivated = ['aliases' => 0, 'recipients' => 0, 'rules' => 0, 'domains' => 0];

        DB::transaction(function () use ($user, $limits, &$deactivated) {
            $deactivated['aliases'] = $this->deactivateExcess(
                $user->aliases()->where('active', true),
                $limits['aliases'] ?? null,
                Alias::class,
            );

            $deactivated['recipients'] = $this->deactivateExcessRecipients(
                $user,
                $limits['recipients'] ?? null,
            );

            $deactivated['rules'] = $this->deactivateExcess(
                $user->rules()->where('active', true),
                $limits['rules'] ?? null,
                Rule::class,
            );

            if (! ($limits['can_use_custom_domains'] ?? false)) {
                $deactivated['domains'] = $user->domains()
                    ->where('active', true)
                    ->update(['active' => false]);
            }
        });

        return $deactivated;
    }

    /**
     * Helper: keep the oldest $limit items active, deactivate the rest.
     * A null $limit means unlimited (no deactivation needed).
     */
    protected function deactivateExcess($activeQuery, ?int $limit, string $modelClass): int
    {
        if (is_null($limit)) {
            return 0;
        }

        $ids = $activeQuery
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->slice($limit);

        if ($ids->isEmpty()) {
            return 0;
        }

        return $modelClass::whereIn('id', $ids)->update(['active' => false]);
    }

    /**
     * Recipients have stricter preservation rules than aliases/rules: the user's
     * default recipient is always kept active. Excess (newest beyond the limit,
     * excluding the default) are deactivated.
     */
    protected function deactivateExcessRecipients(User $user, ?int $limit): int
    {
        if (is_null($limit)) {
            return 0;
        }

        // Default takes one slot; we have $limit - 1 slots for additional recipients.
        $additionalSlots = max(0, $limit - 1);

        $candidateIds = $user->recipients()
            ->where('active', true)
            ->where('id', '!=', $user->default_recipient_id)
            ->orderBy('created_at', 'asc')
            ->pluck('id');

        $toDeactivate = $candidateIds->slice($additionalSlots);

        if ($toDeactivate->isEmpty()) {
            return 0;
        }

        return Recipient::whereIn('id', $toDeactivate)->update(['active' => false]);
    }
}
