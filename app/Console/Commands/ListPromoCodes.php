<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Illuminate\Console\Command;

class ListPromoCodes extends Command
{
    protected $signature = 'mailflusher:promo-list
                            {--active : Only show active, non-expired, non-exhausted codes}';

    protected $description = 'List promo codes and their redemption counts';

    public function handle(): int
    {
        $query = PromoCode::query()->orderByDesc('created_at');

        if ($this->option('active')) {
            $query->where('active', true)
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
        }

        $codes = $query->get();

        if ($codes->isEmpty()) {
            $this->warn('No promo codes found.');

            return self::SUCCESS;
        }

        $rows = $codes->map(fn (PromoCode $code) => [
            $code->code,
            $code->plan,
            $code->duration_days.'d',
            $code->redemption_count.' / '.($code->max_redemptions ?? '∞'),
            $code->expires_at?->toDateString() ?? 'never',
            $code->active ? 'yes' : 'no',
            $code->notes ?? '',
        ])->all();

        $this->table(
            ['Code', 'Plan', 'Duration', 'Redeemed', 'Expires', 'Active', 'Notes'],
            $rows,
        );

        return self::SUCCESS;
    }
}
