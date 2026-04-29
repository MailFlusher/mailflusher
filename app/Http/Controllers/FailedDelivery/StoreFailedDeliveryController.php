<?php

namespace App\Http\Controllers\FailedDelivery;

use App\Http\Controllers\Controller;

use App\Http\Requests\FailedDelivery\UpdateStoreFailedDeliveryRequest;

class StoreFailedDeliveryController extends Controller
{
    public function update(UpdateStoreFailedDeliveryRequest $request)
    {
        if ($request->store_failed_deliveries) {
            user()->update(['store_failed_deliveries' => true]);
        } else {
            user()->update(['store_failed_deliveries' => false]);
        }

        return back()->with(['flash' => $request->store_failed_deliveries ? 'Store Failed Deliveries Enabled Successfully' : 'Store Failed Deliveries Disabled Successfully']);
    }
}
