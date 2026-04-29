<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateDefaultAliasFormatRequest;

class DefaultAliasFormatController extends Controller
{
    public function update(UpdateDefaultAliasFormatRequest $request)
    {
        user()->default_alias_format = $request->format;
        user()->save();

        return back()->with(['flash' => 'Default Alias Format Updated Successfully']);
    }
}
