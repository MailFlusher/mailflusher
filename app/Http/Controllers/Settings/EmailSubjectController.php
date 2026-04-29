<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateEmailSubjectRequest;

class EmailSubjectController extends Controller
{
    public function update(UpdateEmailSubjectRequest $request)
    {
        user()->update(['email_subject' => $request->email_subject]);

        return back()->with(['flash' => 'Email Subject Updated Successfully']);
    }
}
