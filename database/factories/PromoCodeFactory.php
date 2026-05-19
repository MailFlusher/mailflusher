<?php

namespace Database\Factories;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(10)),
            'plan' => 'pro',
            'duration_days' => 30,
            'max_redemptions' => null,
            'redemption_count' => 0,
            'expires_at' => null,
            'active' => true,
            'notes' => null,
        ];
    }
}
