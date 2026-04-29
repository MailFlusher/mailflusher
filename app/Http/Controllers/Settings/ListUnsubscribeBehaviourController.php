<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use App\Enums\ListUnsubscribeBehaviour;
use App\Http\Requests\Settings\UpdateListUnsubscribeBehaviourRequest;

class ListUnsubscribeBehaviourController extends Controller
{
    public function update(UpdateListUnsubscribeBehaviourRequest $request)
    {
        user()->update([
            'list_unsubscribe_behaviour' => ListUnsubscribeBehaviour::from($request->list_unsubscribe_behaviour),
        ]);

        return back()->with(['flash' => 'List-Unsubscribe behaviour updated successfully']);
    }
}
