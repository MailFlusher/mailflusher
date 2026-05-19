<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreatePromoCode extends Command
{
    protected $signature = 'mailflusher:promo-create
                            {--code= : Code string (auto-generated if omitted)}
                            {--plan=pro : Plan key (standard|pro)}
                            {--days=30 : Days the code grants}
                            {--max= : Max total redemptions (unlimited if omitted)}
                            {--expires= : Expiry date YYYY-MM-DD (no expiry if omitted)}
                            {--notes= : Internal note about who/what the code is for}';

    protected $description = 'Create a promo code that users can redeem for plan time';

    public function handle(): int
    {
        $code = $this->option('code') ?: strtoupper(Str::random(10));
        $code = strtoupper(trim($code));

        if (! preg_match('/^[A-Z0-9_-]{3,32}$/', $code)) {
            $this->error('Code must be 3-32 chars, A-Z 0-9 _ - only.');

            return self::FAILURE;
        }

        $plan = $this->option('plan');
        if (! in_array($plan, ['standard', 'pro'], true)) {
            $this->error("Plan must be 'standard' or 'pro'.");

            return self::FAILURE;
        }

        $days = (int) $this->option('days');
        if ($days < 1 || $days > 3650) {
            $this->error('Days must be between 1 and 3650.');

            return self::FAILURE;
        }

        $max = $this->option('max');
        $max = $max === null ? null : (int) $max;
        if ($max !== null && $max < 1) {
            $this->error('--max must be at least 1 if provided.');

            return self::FAILURE;
        }

        $expires = $this->option('expires');
        $expiresAt = null;
        if ($expires) {
            try {
                $expiresAt = Carbon::parse($expires)->endOfDay();
            } catch (\Throwable) {
                $this->error("Could not parse --expires value '{$expires}'.");

                return self::FAILURE;
            }
        }

        if (PromoCode::where('code', $code)->exists()) {
            $this->error("Code {$code} already exists.");

            return self::FAILURE;
        }

        $promo = PromoCode::create([
            'code' => $code,
            'plan' => $plan,
            'duration_days' => $days,
            'max_redemptions' => $max,
            'expires_at' => $expiresAt,
            'active' => true,
            'notes' => $this->option('notes'),
        ]);

        $this->info("Created promo code: {$promo->code}");
        $this->line("  Plan:      {$promo->plan}");
        $this->line("  Duration:  {$promo->duration_days} days");
        $this->line('  Max uses:  '.($promo->max_redemptions ?? 'unlimited'));
        $this->line('  Expires:   '.($promo->expires_at?->toDateString() ?? 'never'));

        return self::SUCCESS;
    }
}
