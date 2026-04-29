<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateDarkModeRequest;

class DarkModeController extends Controller
{
    public function update(UpdateDarkModeRequest $request)
    {
        if ($request->dark_mode) {
            user()->update(['dark_mode' => true]);
        } else {
            user()->update(['dark_mode' => false]);
        }

        return back()->with(['flash' => $request->dark_mode ? 'Dark Mode Enabled Successfully' : 'Dark Mode Disabled Successfully']);
    }
}
