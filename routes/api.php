<?php

use App\Http\Controllers\Api\Account\AccountDetailController;
use App\Http\Controllers\Api\Alias\ActiveAliasController;
use App\Http\Controllers\Api\Domain\ActiveDomainController;
use App\Http\Controllers\Api\Rule\ActiveRuleController;
use App\Http\Controllers\Api\Username\ActiveUsernameController;
use App\Http\Controllers\Api\Alias\AliasBulkController;
use App\Http\Controllers\Api\Alias\AliasController;
use App\Http\Controllers\Api\AliasGroup\AliasGroupController;
use App\Http\Controllers\Api\Alias\AliasRecipientController;
use App\Http\Controllers\Api\Recipient\AllowedRecipientController;
use App\Http\Controllers\Api\Account\ApiTokenDetailController;
use App\Http\Controllers\Api\Account\AppVersionController;
use App\Http\Controllers\Api\Recipient\AttachedRecipientOnlyController;
use App\Http\Controllers\Api\Blocklist\BlocklistController;
use App\Http\Controllers\Api\Domain\CatchAllDomainController;
use App\Http\Controllers\Api\Username\CatchAllUsernameController;
use App\Http\Controllers\Api\Account\ChartDataController;
use App\Http\Controllers\Api\Domain\DomainController;
use App\Http\Controllers\Api\Domain\DomainDefaultRecipientController;
use App\Http\Controllers\Api\Domain\DomainOptionController;
use App\Http\Controllers\Api\GhostInbox\GhostVaultController;
use App\Http\Controllers\Api\Import\ImportController;
use App\Http\Controllers\Api\GhostInbox\StoredEmailController;
use App\Http\Controllers\Api\FailedDelivery\DownloadableFailedDeliveryController;
use App\Http\Controllers\Api\Recipient\EncryptedRecipientController;
use App\Http\Controllers\Api\FailedDelivery\FailedDeliveryController;
use App\Http\Controllers\Api\Recipient\InlineEncryptedRecipientController;
use App\Http\Controllers\Api\Leak\LeakEventController;
use App\Http\Controllers\Api\Username\LoginableUsernameController;
use App\Http\Controllers\Api\Alias\PinnedAliasController;
use App\Http\Controllers\Api\Recipient\ProtectedHeadersRecipientController;
use App\Http\Controllers\Api\Recipient\RecipientController;
use App\Http\Controllers\Api\Recipient\RecipientKeyController;
use App\Http\Controllers\Api\Recipient\RemovePgpKeysRecipientController;
use App\Http\Controllers\Api\Recipient\RemovePgpSignaturesRecipientController;
use App\Http\Controllers\Api\Rule\ReorderRuleController;
use App\Http\Controllers\Api\FailedDelivery\ResendableFailedDeliveryController;
use App\Http\Controllers\Api\Rule\RuleController;
use App\Http\Controllers\Api\Username\UsernameController;
use App\Http\Controllers\Api\Webhook\WebhookController;
use App\Http\Controllers\Api\Username\UsernameDefaultRecipientController;
use App\Http\Controllers\Auth\ApiAuthenticationController;
use App\Http\Controllers\Blocklist\BlocklistCheckController;
use App\Http\Controllers\Verification\RecipientVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Bblocklist check for Rspamd (allowed IPs + optional secret only)
Route::get('blocklist-check', [BlocklistCheckController::class, 'check'])
    ->middleware('blocklist.api')
    ->name('blocklist.check');

// API auth routes for mobile apps and browser extension
Route::controller(ApiAuthenticationController::class)->prefix('auth')->group(function () {
    Route::post('/logout', 'logout');
    Route::post('/delete-account', 'destroy');
});

Route::group([
    'middleware' => ['auth:sanctum', 'verified'],
    'prefix' => 'v1',
], function () {
    Route::controller(AliasController::class)->group(function () {
        Route::get('/aliases', 'index');
        Route::get('/aliases/{id}', 'show');
        Route::post('/aliases', 'store');
        Route::patch('/aliases/{id}', 'update');
        Route::patch('/aliases/{id}/restore', 'restore');
        Route::delete('/aliases/{id}', 'destroy');
        Route::delete('/aliases/{id}/forget', 'forget');
    });

    Route::controller(AliasBulkController::class)->group(function () {
        Route::post('/aliases/get/bulk', 'get');
        Route::post('/aliases/activate/bulk', 'activate');
        Route::post('/aliases/deactivate/bulk', 'deactivate');
        Route::post('/aliases/pin/bulk', 'pin');
        Route::post('/aliases/unpin/bulk', 'unpin');
        Route::post('/aliases/delete/bulk', 'delete');
        Route::post('/aliases/restore/bulk', 'restore');
        Route::post('/aliases/forget/bulk', 'forget');
        Route::post('/aliases/recipients/bulk', 'recipients');
        Route::post('/aliases/group/bulk', 'group');
    });

    Route::controller(AliasGroupController::class)->group(function () {
        Route::get('/alias-groups', 'index');
        Route::post('/alias-groups', 'store');
        Route::patch('/alias-groups/{id}', 'update');
        Route::delete('/alias-groups/{id}', 'destroy');
    });

    Route::controller(ActiveAliasController::class)->group(function () {
        Route::post('/active-aliases', 'store');
        Route::delete('/active-aliases/{id}', 'destroy');
    });

    Route::controller(LeakEventController::class)->group(function () {
        Route::get('/leak-events', 'index');
        Route::post('/leak-events/{id}/confirm', 'confirm');
        Route::post('/leak-events/{id}/dismiss', 'dismiss');
    });

    Route::controller(WebhookController::class)->group(function () {
        Route::get('/webhooks', 'index');
        Route::post('/webhooks', 'store');
        Route::patch('/webhooks/{id}', 'update');
        Route::delete('/webhooks/{id}', 'destroy');
        Route::get('/webhooks/{id}/deliveries', 'deliveries');
    });

    Route::controller(ImportController::class)->group(function () {
        Route::post('/import/dry-run', 'dryRun');
        Route::post('/import', 'import');
    });

    Route::controller(GhostVaultController::class)->group(function () {
        Route::get('/ghost-vault', 'show');
        Route::post('/ghost-vault', 'store');
        Route::patch('/ghost-vault/settings', 'updateSettings');
        Route::delete('/ghost-vault', 'destroy');
    });

    Route::controller(StoredEmailController::class)->group(function () {
        Route::get('/ghost-emails', 'index');
        Route::get('/ghost-emails/{id}', 'show');
        Route::delete('/ghost-emails/{id}', 'destroy');
    });

    Route::controller(PinnedAliasController::class)->group(function () {
        Route::post('/pinned-aliases', 'store');
        Route::delete('/pinned-aliases/{id}', 'destroy');
    });

    Route::controller(AttachedRecipientOnlyController::class)->group(function () {
        Route::post('/attached-recipients-only', 'store');
        Route::delete('/attached-recipients-only/{id}', 'destroy');
    });

    Route::post('/alias-recipients', [AliasRecipientController::class, 'store']);

    Route::controller(RecipientController::class)->group(function () {
        Route::get('/recipients', 'index');
        Route::get('/recipients/{id}', 'show');
        Route::post('/recipients', 'store');
        Route::patch('/recipients/{id}/email', 'updateEmail');
        Route::delete('/recipients/{id}', 'destroy');
    });

    Route::post('/recipients/email/resend', [RecipientVerificationController::class, 'resend']);

    Route::controller(RecipientKeyController::class)->group(function () {
        Route::patch('/recipient-keys/{id}', 'update');
        Route::delete('/recipient-keys/{id}', 'destroy');
    });

    Route::controller(EncryptedRecipientController::class)->group(function () {
        Route::post('/encrypted-recipients', 'store');
        Route::delete('/encrypted-recipients/{id}', 'destroy');
    });

    Route::controller(InlineEncryptedRecipientController::class)->group(function () {
        Route::post('/inline-encrypted-recipients', 'store');
        Route::delete('/inline-encrypted-recipients/{id}', 'destroy');
    });

    Route::controller(ProtectedHeadersRecipientController::class)->group(function () {
        Route::post('/protected-headers-recipients', 'store');
        Route::delete('/protected-headers-recipients/{id}', 'destroy');
    });
    Route::controller(RemovePgpKeysRecipientController::class)->group(function () {
        Route::post('/remove-pgp-keys-recipients', 'store');
        Route::delete('/remove-pgp-keys-recipients/{id}', 'destroy');
    });

    Route::controller(RemovePgpSignaturesRecipientController::class)->group(function () {
        Route::post('/remove-pgp-signatures-recipients', 'store');
        Route::delete('/remove-pgp-signatures-recipients/{id}', 'destroy');
    });

    Route::controller(AllowedRecipientController::class)->group(function () {
        Route::post('/allowed-recipients', 'store');
        Route::delete('/allowed-recipients/{id}', 'destroy');
    });

    Route::controller(DomainController::class)->group(function () {
        Route::get('/domains', 'index');
        Route::get('/domains/{id}', 'show');
        Route::post('/domains', 'store');
        Route::patch('/domains/{id}', 'update');
        Route::delete('/domains/{id}', 'destroy');
    });

    Route::patch('/domains/{id}/default-recipient', [DomainDefaultRecipientController::class, 'update']);

    Route::controller(ActiveDomainController::class)->group(function () {
        Route::post('/active-domains', 'store');
        Route::delete('/active-domains/{id}', 'destroy');
    });

    Route::controller(CatchAllDomainController::class)->group(function () {
        Route::post('/catch-all-domains', 'store');
        Route::delete('/catch-all-domains/{id}', 'destroy');
    });

    Route::controller(UsernameController::class)->group(function () {
        Route::get('/usernames', 'index');
        Route::get('/usernames/{id}', 'show');
        Route::post('/usernames', 'store');
        Route::patch('/usernames/{id}', 'update');
        Route::delete('/usernames/{id}', 'destroy');
    });

    Route::patch('/usernames/{id}/default-recipient', [UsernameDefaultRecipientController::class, 'update']);

    Route::controller(ActiveUsernameController::class)->group(function () {
        Route::post('/active-usernames', 'store');
        Route::delete('/active-usernames/{id}', 'destroy');
    });

    Route::controller(CatchAllUsernameController::class)->group(function () {
        Route::post('/catch-all-usernames', 'store');
        Route::delete('/catch-all-usernames/{id}', 'destroy');
    });

    Route::controller(LoginableUsernameController::class)->group(function () {
        Route::post('/loginable-usernames', 'store');
        Route::delete('/loginable-usernames/{id}', 'destroy');
    });

    Route::controller(RuleController::class)->group(function () {
        Route::get('/rules', 'index');
        Route::get('/rules/{id}', 'show');
        Route::post('/rules', 'store');
        Route::patch('/rules/{id}', 'update');
        Route::delete('/rules/{id}', 'destroy');
    });

    Route::post('/reorder-rules', [ReorderRuleController::class, 'store']);

    Route::controller(ActiveRuleController::class)->group(function () {
        Route::post('/active-rules', 'store');
        Route::delete('/active-rules/{id}', 'destroy');
    });

    Route::controller(FailedDeliveryController::class)->group(function () {
        Route::get('/failed-deliveries', 'index');
        Route::get('/failed-deliveries/{id}', 'show');
        Route::delete('/failed-deliveries/{id}', 'destroy');
    });

    Route::get('/failed-deliveries/{id}/download', [DownloadableFailedDeliveryController::class, 'index']);
    Route::post('/failed-deliveries/{id}/resend', [ResendableFailedDeliveryController::class, 'index']);

    Route::controller(BlocklistController::class)->group(function () {
        Route::get('/blocklist', 'index');
        Route::post('/blocklist', 'store');
        Route::post('/blocklist/store/bulk', 'storeBulk');
        Route::post('/blocklist/delete/bulk', 'destroyBulk');
        Route::delete('/blocklist/{id}', 'destroy');
    });

    Route::get('/domain-options', [DomainOptionController::class, 'index']);

    Route::get('/account-details', [AccountDetailController::class, 'index']);

    Route::get('/app-version', [AppVersionController::class, 'index']);

    Route::get('api-token-details', [ApiTokenDetailController::class, 'show']);

    Route::get('/chart-data', [ChartDataController::class, 'index']);
});
