<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

use Inertia\Inertia;

class ShowGhostInboxController extends Controller
{
    public function index()
    {
        return Inertia::render('GhostInbox/Index', [
            'canUseGhostInbox' => user()->canUseGhostInbox(),
            'hasGhostVault' => user()->hasGhostVault(),
        ]);
    }
}
