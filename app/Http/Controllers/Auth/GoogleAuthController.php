<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use App\Models\User;
use App\Models\Username;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Ramsey\Uuid\Uuid;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect('/login')->with('status', 'Google authentication failed. Please try again.');
        }

        // Check if user already exists with this Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            Auth::login($user, true);

            return redirect('/');
        }

        // Check if a user exists with a verified recipient matching this Google email
        $recipient = Recipient::where('email', strtolower($googleUser->getEmail()))
            ->whereNotNull('email_verified_at')
            ->first();

        if ($recipient) {
            $recipient->user->update(['google_id' => $googleUser->getId()]);

            Auth::login($recipient->user, true);

            return redirect('/');
        }

        // Registration disabled — cannot create new accounts
        if (! config('mailflusher.enable_registration')) {
            return redirect('/login')->with('status', 'Registration is currently disabled. If you already have an account, please login with your username and password first, then link Google from settings.');
        }

        // Create a new user
        $user = DB::transaction(function () use ($googleUser) {
            $userId = Uuid::uuid4();

            // Generate a username from the Google name or email
            $baseUsername = Str::slug(Str::before($googleUser->getEmail(), '@'), '');
            $username = $baseUsername;
            $counter = 1;

            while (Username::where('username', $username)->exists() || in_array($username, config('mailflusher.blacklist', []))) {
                $username = $baseUsername.$counter;
                $counter++;
            }

            $usernameModel = Username::create([
                'user_id' => $userId,
                'username' => $username,
                'can_login' => true,
            ]);

            $recipient = Recipient::create([
                'user_id' => $userId,
                'email' => strtolower($googleUser->getEmail()),
                'email_verified_at' => now(),
            ]);

            $user = User::create([
                'id' => $userId,
                'default_username_id' => $usernameModel->id,
                'default_recipient_id' => $recipient->id,
                'password' => bcrypt(Str::random(32)),
                'google_id' => $googleUser->getId(),
            ]);

            return $user;
        });

        Auth::login($user, true);

        return redirect('/');
    }
}
