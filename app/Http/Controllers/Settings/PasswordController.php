<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:3,1')->only('update');
    }

    public function update(UpdatePasswordRequest $request)
    {
        // Log out of other sessions
        Auth::logoutOtherDevices($request->current);

        user()->password = Hash::make($request->password);
        user()->save();

        return back()->with(['flash' => 'Password Updated Successfully']);
    }
}
