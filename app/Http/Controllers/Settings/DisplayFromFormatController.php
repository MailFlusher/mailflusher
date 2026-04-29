<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Enums\DisplayFromFormat;
use App\Http\Requests\Settings\UpdateDisplayFromFormatRequest;

class DisplayFromFormatController extends Controller
{
    public function update(UpdateDisplayFromFormatRequest $request)
    {
        user()->display_from_format = DisplayFromFormat::from($request->format);
        user()->save();

        return back()->with(['flash' => 'Display From Format Updated Successfully']);
    }
}
