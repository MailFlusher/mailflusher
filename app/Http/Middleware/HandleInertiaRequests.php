<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'layouts.app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => $request->session()->get('flash', null),
            'user' => function () use ($request) {
                if (! $request->user()) {
                    return;
                }

                $user = $request->user();

                return [
                    'username' => $user->username,
                    'email' => $user->email,
                    'default_recipient_id' => $user->default_recipient_id,
                    'default_username_id' => $user->default_username_id,
                    'darkMode' => $user->dark_mode,
                    'plan' => $user->getActivePlan(),
                    'plan_name' => $user->planConfig()['name'],
                    'can_reply_send' => $user->canReply(),
                    'can_view_failed_deliveries' => $user->canViewFailedDeliveries(),
                    'can_use_blocklist' => $user->canUseBlocklist(),
                    'can_use_catch_all' => $user->canUseCatchAll(),
                    'can_use_rules' => $user->planLimit('rules') !== 0,
                    'can_use_custom_domains' => $user->canUseCustomDomains(),
                    'can_use_link_stripping' => $user->canUseLinkStripping(),
                    'can_use_webhooks' => $user->canUseWebhooks(),
                    'can_use_ghost_inbox' => $user->canUseGhostInbox(),
                    'has_ghost_vault' => $user->hasGhostVault(),
                    'is_admin' => $user->isAdminUser(),
                    'usage' => [
                        'aliases' => [
                            'count' => $user->aliases()->count(),
                            'limit' => $user->planLimit('aliases'),
                        ],
                        'recipients' => [
                            'count' => $user->recipients()->count(),
                            'limit' => $user->planLimit('recipients'),
                        ],
                        'rules' => [
                            'count' => $user->rules()->count(),
                            'limit' => $user->planLimit('rules'),
                        ],
                    ],
                ];
            },
            'errorBags' => function () {
                return collect(optional(Session::get('errors'))->getBags() ?: [])->mapWithKeys(function ($bag, $key) {
                    return [$key => $bag->messages()];
                })->all();
            },
            'version' => config('mailflusher.version'),
            'updateAvailable' => false,
            'usesExternalAuthentication' => usesExternalAuthentication(),
            'enableCustomDomains' => (bool) config('mailflusher.enable_custom_domains') && auth()->check() && auth()->user()->canUseCustomDomains(),
        ]);
    }
}
