<?php

namespace App\Http\Controllers\Billing;

use App\Exceptions\PromoCodeException;
use App\Http\Controllers\Controller;
use App\Services\PromoCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function redeem(Request $request, PromoCodeService $service): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
        ]);

        try {
            $redemption = $service->redeem(user(), $validated['code']);
        } catch (PromoCodeException $e) {
            return back()->withErrors(['code' => $e->getMessage()])->withInput();
        }

        return back()->with(
            'flash',
            sprintf(
                'Promo code redeemed — %d days of %s added.',
                $redemption->duration_days,
                config("mailflusher.plans.{$redemption->plan}.name") ?? $redemption->plan,
            ),
        );
    }
}
