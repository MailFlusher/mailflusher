<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Http\Requests\Settings\UpdateAliasSeparatorRequest;

class AliasSeparatorController extends Controller
{
    public function update(UpdateAliasSeparatorRequest $request)
    {
        user()->alias_separator = $request->separator;
        user()->save();

        return back()->with(['flash' => 'Alias Separator Updated Successfully']);
    }
}
