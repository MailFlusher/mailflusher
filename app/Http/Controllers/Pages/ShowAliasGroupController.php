<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

use App\Models\AliasGroup;
use Inertia\Inertia;

class ShowAliasGroupController extends Controller
{
    public function index()
    {
        $groups = AliasGroup::where('user_id', user()->id)
            ->withCount('aliases')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('AliasGroups/Index', [
            'initialGroups' => $groups,
            'palette' => AliasGroup::PALETTE,
        ]);
    }
}
