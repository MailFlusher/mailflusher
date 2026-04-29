<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = user();
        $plans = config('mailflusher.plans');

        return Inertia::render('Settings/Subscription', [
            'currentPlan' => $user->getActivePlan(),
            'plans' => $plans,
            'hasSubscription' => $user->subscribed('default'),
            'onGracePeriod' => $user->subscribed('default') && $user->subscription('default')?->onGracePeriod(),
            'subscriptionEndsAt' => $user->subscription('default')?->ends_at?->toDateTimeString(),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|in:standard,pro',
        ]);

        $plan = $request->plan;
        $priceId = config("mailflusher.plans.{$plan}.stripe_price_id");

        if (! $priceId) {
            return response('Invalid plan.', 400);
        }

        $checkout = user()
            ->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.index'),
            ]);

        return response()->json(['url' => $checkout->url]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if ($sessionId) {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($session->subscription) {
                $subscription = $stripe->subscriptions->retrieve($session->subscription);
                $priceId = $subscription->items->data[0]->price->id ?? null;

                // Map price ID to plan name
                $planName = 'free';
                foreach (config('mailflusher.plans') as $key => $plan) {
                    if (isset($plan['stripe_price_id']) && $plan['stripe_price_id'] === $priceId) {
                        $planName = $key;
                        break;
                    }
                }

                user()->update(['plan' => $planName, 'plan_expires_at' => null]);
            }
        }

        return redirect()->route('subscription.index')->with('flash', 'Subscription activated successfully!');
    }

    public function cancel()
    {
        $subscription = user()->subscription('default');

        if ($subscription && ! $subscription->onGracePeriod()) {
            $subscription->cancel();

            return back()->with('flash', 'Your subscription has been cancelled. It will remain active until the end of the billing period.');
        }

        return back();
    }

    public function resume()
    {
        $subscription = user()->subscription('default');

        if ($subscription && $subscription->onGracePeriod()) {
            $subscription->resume();

            return back()->with('flash', 'Your subscription has been resumed.');
        }

        return back();
    }

    public function portal()
    {
        return user()->redirectToBillingPortal(route('subscription.index'));
    }
}
