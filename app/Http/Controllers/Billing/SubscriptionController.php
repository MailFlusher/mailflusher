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
        // Plan activation is owned by the Stripe webhook (handleCustomerSubscriptionUpdated).
        // This endpoint is just the user-facing thank-you redirect; doing the work
        // here too would race with the webhook and bypass PlanService audit logging.
        return redirect()->route('subscription.index')
            ->with('flash', 'Thanks! Your subscription is being activated. It may take a moment to show up.');
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
