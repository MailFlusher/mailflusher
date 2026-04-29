<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateAccountFromNameRequest;

class FromNameController extends Controller
{
    public function update(UpdateAccountFromNameRequest $request)
    {
        user()->update(['from_name' => $request->from_name]);

        return back()->with(['flash' => 'From Name Updated Successfully']);
    }
}
