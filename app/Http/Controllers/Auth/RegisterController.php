<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Username\NotBlacklisted;
use App\Rules\Username\NotDeletedUsername;
use App\Rules\Recipient\NotLocalRecipient;
use App\Rules\Recipient\RegisterUniqueRecipient;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Validate Turnstile captcha separately first to prevent username enumeration
        if (! App::environment('testing') && config('mailflusher.turnstile.secret_key')) {
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('mailflusher.turnstile.secret_key'),
                'response' => $data['cf-turnstile-response'] ?? '',
                'remoteip' => request()->ip(),
            ]);

            if (! $turnstileResponse->json('success')) {
                $validator = Validator::make([], []);
                $validator->errors()->add('captcha', 'Human verification failed. Please try again.');

                return $validator;
            }
        }

        return Validator::make($data, [
            'username' => [
                'bail',
                'required',
                'regex:/^[a-zA-Z0-9]*$/',
                'max:20',
                'unique:usernames,username',
                new NotBlacklisted,
                new NotDeletedUsername,
            ],
            'email' => [
                'bail',
                'required',
                'string',
                'ascii',
                App::environment(['local', 'testing']) ? 'email:rfc' : 'email:rfc,dns',
                'max:254',
                'confirmed',
                new RegisterUniqueRecipient,
                new NotLocalRecipient,
            ],
            'password' => ['required', Password::defaults()],
        ], [
            'username.regex' => 'Your username can only contain letters and numbers.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return createUser($data['username'], $data['email'], $data['password']);
    }
}
