<?php

namespace App\Http\Controllers\Api\Domain;

use App\Http\Controllers\Controller;
use App\Http\Resources\DomainResource;
use Illuminate\Http\Request;

class CatchAllDomainController extends Controller
{
    public function store(Request $request)
    {
        if (! user()->canUseCatchAll()) {
            return response('Catch-all is not available on your current plan. Please upgrade.', 403);
        }

        $request->validate(['id' => 'required|string']);

        $domain = user()->domains()->findOrFail($request->id);

        $domain->enableCatchAll();

        return new DomainResource($domain->load('defaultRecipient')->loadCount('aliases'));
    }

    public function destroy($id)
    {
        $domain = user()->domains()->findOrFail($id);

        $domain->disableCatchAll();

        return response('', 204);
    }
}
