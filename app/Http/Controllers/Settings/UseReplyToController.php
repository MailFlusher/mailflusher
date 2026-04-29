<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateUseReplyToRequest;

class UseReplyToController extends Controller
{
    public function update(UpdateUseReplyToRequest $request)
    {
        if ($request->use_reply_to) {
            user()->update(['use_reply_to' => true]);
        } else {
            user()->update(['use_reply_to' => false]);
        }

        return back()->with(['flash' => $request->use_reply_to ? 'Use Reply To Enabled Successfully' : 'Use Reply To Disabled Successfully']);
    }
}
