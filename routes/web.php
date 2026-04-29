<?php

use App\Http\Controllers\Alias\AliasExportController;
use App\Http\Controllers\Alias\AliasImportController;
use App\Http\Controllers\Settings\AliasSeparatorController;
use App\Http\Controllers\Auth\ApiAuthenticationController;
use App\Http\Controllers\Auth\BackupCodeController;
use App\Http\Controllers\Auth\ForgotUsernameController;
use App\Http\Controllers\Auth\PersonalAccessTokenController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\Auth\WebauthnController;
use App\Http\Controllers\Auth\WebauthnEnabledKeyController;
use App\Http\Controllers\Settings\BannerLocationController;
use App\Http\Controllers\Blocklist\BlocklistOneClickController;
use App\Http\Controllers\Settings\BrowserSessionController;
use App\Http\Controllers\Settings\DarkModeController;
use App\Http\Controllers\Alias\DeactivateAliasController;
use App\Http\Controllers\Settings\DefaultAliasDomainController;
use App\Http\Controllers\Settings\DefaultAliasFormatController;
use App\Http\Controllers\Settings\DefaultRecipientController;
use App\Http\Controllers\Settings\DefaultUsernameController;
use App\Http\Controllers\Alias\DeleteAliasController;
use App\Http\Controllers\Settings\DisplayFromFormatController;
use App\Http\Controllers\Verification\DomainVerificationController;
use App\Http\Controllers\Settings\EmailSubjectController;
use App\Http\Controllers\Settings\FromNameController;
use App\Http\Controllers\Settings\ListUnsubscribeBehaviourController;
use App\Http\Controllers\Settings\LoginRedirectController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Alias\SaveAliasLastUsedController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Pages\ShowAliasController;
use App\Http\Controllers\Pages\ShowBlocklistController;
use App\Http\Controllers\Pages\ShowDashboardController;
use App\Http\Controllers\Pages\ShowDomainController;
use App\Http\Controllers\Pages\ShowAliasGroupController;
use App\Http\Controllers\Pages\ShowFailedDeliveryController;
use App\Http\Controllers\Pages\ShowGhostInboxController;
use App\Http\Controllers\Pages\ShowRecipientController;
use App\Http\Controllers\Pages\ShowRuleController;
use App\Http\Controllers\Pages\ShowUsernameController;
use App\Http\Controllers\Settings\SpamWarningBehaviourController;
use App\Http\Controllers\Settings\StripTrackersController;
use App\Http\Controllers\FailedDelivery\StoreFailedDeliveryController;
use App\Http\Controllers\Alias\TestAutoCreateRegexController;
use App\Http\Controllers\Settings\UseReplyToController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\Billing\SubscriptionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Landing page for the public-facing domain (e.g. mailflusher.com)
if ($landingDomain = config('mailflusher.landing_domain')) {
    Route::domain($landingDomain)->group(function () {
        Route::get('/', function () {
            return view('landing', [
                'appUrl' => config('app.url'),
                'plans' => config('mailflusher.plans'),
            ]);
        })->name('landing');

        Route::get('/contact', function () {
            return view('contact');
        })->name('contact');

        Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

        Route::get('/privacy', function () {
            return view('privacy');
        })->name('privacy');

        Route::get('/help', function () {
            return view('help');
        })->name('help');

        Route::get('/terms', function () {
            return view('terms');
        })->name('terms');

        // Tracker-stripping click proxy (public, no auth)
        Route::get('/r/{token}', RedirectController::class)
            ->where('token', '[A-Za-z0-9]{8,16}')
            ->name('redirect.proxy.landing');

        Route::get('/vs/simplelogin', function () {
            return view('compare.simplelogin');
        })->name('compare.simplelogin');

        Route::get('/vs/addy-io', function () {
            return view('compare.addy-io');
        })->name('compare.addy-io');

        Route::get('/vs/firefox-relay', function () {
            return view('compare.firefox-relay');
        })->name('compare.firefox-relay');

        Route::get('/sitemap.xml', function () {
            $domain = config('mailflusher.landing_domain');

            return response()->view('sitemap', ['domain' => $domain], 200, [
                'Content-Type' => 'application/xml',
            ]);
        });

        // Redirect all other paths on the landing domain to the app domain
        Route::fallback(function () {
            return redirect(config('app.url').'/'.ltrim(request()->path(), '/'));
        });
    });
}

// Stripe webhook (no auth, no CSRF)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// Tracker-stripping click proxy (public, no auth)
Route::get('/r/{token}', RedirectController::class)
    ->where('token', '[A-Za-z0-9]{8,16}')
    ->name('redirect.proxy');

Auth::routes(['verify' => true, 'register' => config('mailflusher.enable_registration')]);

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// API login route needs CSRF middleware so that it can pass it to api/auth/mfa
Route::controller(ApiAuthenticationController::class)->prefix('api/auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/mfa', 'mfa');
});

Route::controller(ForgotUsernameController::class)->group(function () {
    Route::get('/username/reminder', 'show')->name('username.reminder.show');
    Route::post('/username/email', 'sendReminderEmail')->name('username.email');
});

Route::get('/login/2fa', [TwoFactorAuthController::class, 'index'])->name('login.2fa.index')->middleware(['2fa', 'auth']);
Route::post('/login/2fa', [TwoFactorAuthController::class, 'authenticateTwoFactor'])->name('login.2fa')->middleware(['2fa', 'throttle:3,1', 'auth']);

Route::controller(BackupCodeController::class)->group(function () {
    Route::get('/login/backup-code', 'index')->name('login.backup_code.index');
    Route::post('/login/backup-code', 'login')->name('login.backup_code.login');
});

// One-Click unsubscribe to deactivate alias with POST request, no auth required... signed
Route::post('/deactivate-one-click/{alias}', [DeactivateAliasController::class, 'deactivatePost'])->name('deactivate_post');

// One-Click unsubscribe to delete alias with POST request, no auth required... signed
Route::post('/delete-one-click/{alias}', [DeleteAliasController::class, 'deletePost'])->name('delete_post');

// One-Click unsubscribe to block sender email with POST request, no auth required... signed
Route::post('/block-email-one-click/{alias}', [BlocklistOneClickController::class, 'blockEmailPost'])->name('block_email_post');

// One-Click unsubscribe to block sender domain with POST request, no auth required... signed
Route::post('/block-domain-one-click/{alias}', [BlocklistOneClickController::class, 'blockDomainPost'])->name('block_domain_post');

Route::group([
    'middleware' => array_filter(array_merge(
        config('webauthn.middleware', ['web']),
        [
            config('webauthn.auth_middleware', 'auth').':'.config('webauthn.guard', 'web'),
        ]
    )),
    'domain' => config('webauthn.domain', null),
    'prefix' => config('webauthn.prefix', 'webauthn'),
], function () {
    Route::controller(WebauthnController::class)->group(function () {
        Route::get('keys', 'index')->name('webauthn.index');
        Route::get('keys/create', 'create')->name('webauthn.create');
        Route::post('keys', 'store')->name('webauthn.store');
        Route::delete('keys/{id}', 'delete'); // To override delete method and allow route caching
        Route::post('keys/{id}', 'destroy')->name('webauthn.destroy');
    });

    Route::controller(WebauthnEnabledKeyController::class)->group(function () {
        Route::post('enabled-keys', 'store')->name('webauthn.enabled_key.store');
        Route::post('enabled-keys/{id}', 'destroy')->name('webauthn.enabled_key.destroy');
    });
});

Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    Route::get('/', [ShowDashboardController::class, 'index'])->name('dashboard.index');

    Route::controller(ShowAliasController::class)->group(function () {
        Route::get('/aliases', 'index')->name('aliases.index');
        Route::get('/aliases/{id}/edit', 'edit')->name('aliases.edit');
    });

    Route::controller(ShowRecipientController::class)->group(function () {
        Route::get('/recipients', 'index')->name('recipients.index');
        Route::get('/recipients/{id}/edit', 'edit')->name('recipients.edit');
        Route::post('/recipients/alias-count', 'aliasCount')->name('recipients.alias_count');
    });

    Route::controller(ShowDomainController::class)->group(function () {
        Route::get('/domains', 'index')->name('domains.index');
        Route::get('/domains/{id}/edit', 'edit')->name('domains.edit');
    });
    Route::get('/domains/{id}/check-sending', [DomainVerificationController::class, 'checkSending']);

    Route::controller(ShowUsernameController::class)->group(function () {
        Route::get('/usernames', 'index')->name('usernames.index');
        Route::get('/usernames/{id}/edit', 'edit')->name('usernames.edit');
    });

    Route::get('/deactivate/{alias}', [DeactivateAliasController::class, 'deactivate'])->name('deactivate');

    Route::get('/rules', [ShowRuleController::class, 'index'])->name('rules.index');

    Route::get('/failed-deliveries', [ShowFailedDeliveryController::class, 'index'])->name('failed_deliveries.index');

    Route::get('/blocklist', [ShowBlocklistController::class, 'index'])->name('blocklist.index');

    Route::get('/ghost-inbox', [ShowGhostInboxController::class, 'index'])->name('ghost_inbox.index');

    Route::get('/alias-groups', [ShowAliasGroupController::class, 'index'])->name('alias_groups.index');

    Route::post('/test-auto-create-regex', [TestAutoCreateRegexController::class, 'index'])->name('test_auto_create_regex.index');

    // Admin control panel
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    // Subscription management
    Route::controller(SubscriptionController::class)->prefix('subscription')->group(function () {
        Route::get('/', 'index')->name('subscription.index');
        Route::post('/checkout', 'checkout')->name('subscription.checkout');
        Route::get('/success', 'success')->name('subscription.success');
        Route::post('/cancel', 'cancel')->name('subscription.cancel');
        Route::post('/resume', 'resume')->name('subscription.resume');
        Route::get('/portal', 'portal')->name('subscription.portal');
    });
});

Route::group([
    'middleware' => ['auth', '2fa'],
    'prefix' => 'settings',
], function () {
    Route::controller(SettingController::class)->group(function () {
        Route::get('/', 'show')->name('settings.show');
        Route::get('/security', 'security')->name('settings.security');
        Route::get('/api', 'api')->name('settings.api');
        Route::get('/webhooks', 'webhooks')->name('settings.webhooks');
        Route::get('/import', 'import')->name('settings.import');
        Route::get('/ghost-inbox', 'ghostInbox')->name('settings.ghost_inbox');
        Route::get('/data', 'data')->name('settings.data');
        Route::get('/account', 'account')->name('settings.account');
        Route::post('/account', 'destroy')->name('account.destroy');
    });

    Route::controller(DefaultRecipientController::class)->group(function () {
        Route::post('/default-recipient', 'update')->name('settings.default_recipient');
        Route::post('/edit-default-recipient', 'edit')->name('settings.edit_default_recipient');
    });

    Route::post('/default-username', [DefaultUsernameController::class, 'update'])->name('settings.default_username');

    Route::post('/default-alias-domain', [DefaultAliasDomainController::class, 'update'])->name('settings.default_alias_domain');

    Route::post('/default-alias-format', [DefaultAliasFormatController::class, 'update'])->name('settings.default_alias_format');

    Route::post('/alias-separator', [AliasSeparatorController::class, 'update'])->name('settings.alias_separator');

    Route::post('/display-from-format', [DisplayFromFormatController::class, 'update'])->name('settings.display_from_format');

    Route::post('/login-redirect', [LoginRedirectController::class, 'update'])->name('settings.login_redirect');

    Route::post('/from-name', [FromNameController::class, 'update'])->name('settings.from_name');

    Route::post('/email-subject', [EmailSubjectController::class, 'update'])->name('settings.email_subject');

    Route::post('/banner-location', [BannerLocationController::class, 'update'])->name('settings.banner_location');

    Route::post('/spam-warning-behaviour', [SpamWarningBehaviourController::class, 'update'])->name('settings.spam_warning_behaviour');

    Route::post('/strip-trackers', [StripTrackersController::class, 'update'])->name('settings.strip_trackers');

    Route::post('/list-unsubscribe-behaviour', [ListUnsubscribeBehaviourController::class, 'update'])->name('settings.list_unsubscribe_behaviour');

    Route::post('/store-failed-deliveries', [StoreFailedDeliveryController::class, 'update'])->name('settings.store_failed_deliveries');

    Route::post('/dark-mode', [DarkModeController::class, 'update'])->name('settings.dark_mode');

    Route::post('/save-alias-last-used', [SaveAliasLastUsedController::class, 'update'])->name('settings.save_alias_last_used');

    Route::post('/use-reply-to', [UseReplyToController::class, 'update'])->name('settings.use_reply_to');

    Route::post('/password', [PasswordController::class, 'update'])->name('settings.password');

    Route::delete('/browser-sessions', [BrowserSessionController::class, 'destroy'])->name('settings.browser_sessions');

    Route::controller(TwoFactorAuthController::class)->group(function () {
        Route::post('/2fa/enable', 'store')->name('settings.2fa_enable');
        Route::post('/2fa/regenerate', 'update')->name('settings.2fa_regenerate');
        Route::post('/2fa/disable', 'destroy')->name('settings.2fa_disable');
    });

    Route::post('/2fa/new-backup-code', [BackupCodeController::class, 'update'])->name('settings.new_backup_code');

    Route::controller(PersonalAccessTokenController::class)->group(function () {
        Route::get('/personal-access-tokens', 'index')->name('personal_access_tokens.index');
        Route::post('/personal-access-tokens', 'store')->name('personal_access_tokens.store');
        Route::delete('/personal-access-tokens/{id}', 'destroy')->name('personal_access_tokens.destroy');
    });

    Route::get('/aliases/export', [AliasExportController::class, 'export'])->name('aliases.export');

    Route::post('/aliases/import', [AliasImportController::class, 'import'])->name('aliases.import');
});
