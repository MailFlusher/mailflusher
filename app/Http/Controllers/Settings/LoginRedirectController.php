<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Enums\LoginRedirect;
use App\Http\Requests\Settings\UpdateLoginRedirectRequest;

class LoginRedirectController extends Controller
{
    public function update(UpdateLoginRedirectRequest $request)
    {
        user()->login_redirect = LoginRedirect::from($request->redirect);
        user()->save();

        return back()->with(['flash' => 'Login Redirect Updated Successfully']);
    }
}
