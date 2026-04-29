<?php

namespace App\Http\Controllers\Api\Leak;

use App\Http\Controllers\Controller;
use App\Models\AliasLeakEvent;
use Illuminate\Http\Request;

class LeakEventController extends Controller
{
    public function index(Request $request)
    {
        $events = AliasLeakEvent::whereIn('alias_id', user()->aliases()->pluck('id'))
            ->orderByDesc('detected_at')
            ->get();

        return response()->json(['data' => $events]);
    }

    public function confirm($id)
    {
        $event = AliasLeakEvent::whereIn('alias_id', user()->aliases()->pluck('id'))
            ->where('id', $id)
            ->firstOrFail();

        $event->confirm();

        return response()->json(['data' => $event->fresh()]);
    }

    public function dismiss($id)
    {
        $event = AliasLeakEvent::whereIn('alias_id', user()->aliases()->pluck('id'))
            ->where('id', $id)
            ->firstOrFail();

        $event->dismiss();

        return response()->json(['data' => $event->fresh()]);
    }
}
