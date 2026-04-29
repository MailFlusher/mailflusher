<?php

namespace App\Http\Controllers\Api\Recipient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipient\IndexRecipientRequest;
use App\Http\Requests\Recipient\ShowRecipientRequest;
use App\Http\Requests\Recipient\StoreRecipientRequest;
use App\Http\Resources\RecipientResource;
use App\Rules\Recipient\NotLocalRecipient;
use App\Rules\Recipient\UniqueRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RecipientController extends Controller
{
    public function index(IndexRecipientRequest $request)
    {
        $recipients = user()->recipients()->latest();

        if ($request->input('filter.alias_count') !== 'false') {
            $recipients->withCount('aliases');
        }

        if ($request->input('filter.verified') === 'true') {
            $recipients->verified();
        }

        if ($request->input('filter.verified') === 'false') {
            $recipients->verified('false');
        }

        return RecipientResource::collection($recipients->get());
    }

    public function show(ShowRecipientRequest $request, $id)
    {
        $recipient = user()->recipients()->findOrFail($id);

        if ($request->input('filter.alias_count') !== 'false') {
            $recipient->loadCount('aliases');
        }

        return new RecipientResource($recipient);
    }

    public function store(StoreRecipientRequest $request)
    {
        if (user()->hasReachedRecipientLimit()) {
            return response('You have reached your recipient limit for your current plan. Please upgrade to add more recipients.', 403);
        }

        $data = ['email' => strtolower($request->email)];

        if (config('mailflusher.auto_verify_new_recipients')) {
            $data['email_verified_at'] = now();
        }

        $recipient = user()->recipients()->create($data);

        if (! config('mailflusher.auto_verify_new_recipients')) {
            $recipient->sendEmailVerificationNotification();
        }

        return new RecipientResource($recipient->refresh()->loadCount('aliases'));
    }

    public function updateEmail(Request $request, $id)
    {
        $recipient = user()->recipients()->findOrFail($id);

        $request->validate([
            'email' => [
                'bail',
                'required',
                'string',
                'ascii',
                App::environment(['local', 'testing']) ? 'email:rfc' : 'email:rfc,dns',
                'max:254',
                new UniqueRecipient,
                new NotLocalRecipient,
            ],
        ]);

        $recipient->update([
            'email' => strtolower($request->email),
            'email_verified_at' => null,
        ]);

        $recipient->sendEmailVerificationNotification();

        // If this is the default recipient, update the user's cached email
        if ($recipient->id === user()->default_recipient_id) {
            user()->load('defaultRecipient');
        }

        return new RecipientResource($recipient->refresh()->loadCount('aliases'));
    }

    public function destroy($id)
    {
        if ($id === user()->default_recipient_id) {
            return response('You cannot delete your default recipient', 403);
        }

        $recipient = user()->recipients()->findOrFail($id);

        $recipient->delete();

        return response('', 204);
    }
}
