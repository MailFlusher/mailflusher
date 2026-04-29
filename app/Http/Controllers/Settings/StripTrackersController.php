<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateStripTrackersRequest;

class StripTrackersController extends Controller
{
    public function update(UpdateStripTrackersRequest $request)
    {
        user()->update(['strip_trackers' => $request->strip_trackers]);

        return back()->with(['flash' => 'Tracker stripping updated']);
    }
}
