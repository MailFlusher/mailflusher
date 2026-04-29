<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateDefaultUsernameRequest;

class DefaultUsernameController extends Controller
{
    public function update(UpdateDefaultUsernameRequest $request)
    {
        $username = user()->usernames()->findOrFail($request->id);

        if (usesExternalAuthentication()) {
            return response('You cannot change default username because you\'re using external authentication', 403);
        }

        // Ensure username can be used to login
        $username->allowLogin();

        user()->update(['default_username_id' => $username->id]);

        return response()->json([
            'success' => true,
        ]);
    }
}
