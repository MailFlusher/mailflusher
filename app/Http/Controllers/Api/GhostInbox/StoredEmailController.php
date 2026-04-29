<?php

namespace App\Http\Controllers\Api\GhostInbox;

use App\Http\Controllers\Controller;
use App\Models\StoredEmail;
use Illuminate\Http\Request;

class StoredEmailController extends Controller
{
    /**
     * List stored emails across all the user's ghost-mode aliases.
     * Returns previews only; ciphertext is not included.
     */
    public function index(Request $request)
    {
        $query = StoredEmail::query()
            ->whereIn('alias_id', user()->aliases()->pluck('id'))
            ->orderByDesc('received_at');

        if ($aliasId = $request->input('alias_id')) {
            $query->where('alias_id', $aliasId);
        }

        return response()->json([
            'data' => $query->limit(200)->get([
                'id', 'alias_id', 'from_preview', 'subject_preview',
                'size_bytes', 'received_at',
            ]),
        ]);
    }

    /**
     * Fetch the encrypted ciphertext for a single stored email. Browser
     * decrypts it locally with the user's unlocked private key.
     */
    public function show(string $id)
    {
        $email = StoredEmail::query()
            ->whereIn('alias_id', user()->aliases()->pluck('id'))
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'id' => $email->id,
            'alias_id' => $email->alias_id,
            'from_preview' => $email->from_preview,
            'subject_preview' => $email->subject_preview,
            'received_at' => $email->received_at,
            'encrypted_payload' => $email->encrypted_payload,
        ]);
    }

    public function destroy(string $id)
    {
        $email = StoredEmail::query()
            ->whereIn('alias_id', user()->aliases()->pluck('id'))
            ->where('id', $id)
            ->firstOrFail();

        $email->delete();

        return response()->noContent();
    }
}
