<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateDefaultAliasDomainRequest;

class DefaultAliasDomainController extends Controller
{
    public function update(UpdateDefaultAliasDomainRequest $request)
    {
        user()->default_alias_domain = $request->domain;
        user()->save();

        return back()->with(['flash' => 'Default Alias Domain Updated Successfully']);
    }
}
