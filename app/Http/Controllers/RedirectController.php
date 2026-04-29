<?php

namespace App\Http\Controllers;

use App\Models\RedirectToken;
use App\Services\TrackerStripper;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function __invoke(string $token, TrackerStripper $stripper): RedirectResponse
    {
        $record = RedirectToken::where('token', $token)->first();

        if (! $record || $record->isExpired()) {
            abort(404);
        }

        // Best-effort click count; never block the redirect if this fails
        try {
            $record->increment('clicks');
        } catch (\Throwable $e) {
            // swallow
        }

        $target = $stripper->stripTrackingParams($record->target_url);

        return redirect()->away($target, 302);
    }
}
