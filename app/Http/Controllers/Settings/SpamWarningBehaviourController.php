<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateSpamWarningBehaviourRequest;

class SpamWarningBehaviourController extends Controller
{
    public function update(UpdateSpamWarningBehaviourRequest $request)
    {
        user()->update(['spam_warning_behaviour' => $request->spam_warning_behaviour]);

        return back()->with(['flash' => 'Spam / DMARC warning preference updated']);
    }
}
