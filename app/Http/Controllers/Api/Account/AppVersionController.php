<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;

class AppVersionController extends Controller
{
    public function index()
    {
        $version = config('mailflusher.version', '0.0.0');
        $parts = explode('.', $version);

        return response()->json([
            'version' => $version,
            'major' => isset($parts[0]) && $parts[0] !== '' ? (int) $parts[0] : 0,
            'minor' => isset($parts[1]) ? (int) $parts[1] : 0,
            'patch' => isset($parts[2]) ? (int) $parts[2] : 0,
        ]);
    }
}
