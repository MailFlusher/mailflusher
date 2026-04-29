<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validateWithBag('contact', [
            'name' => 'required|string|max:100',
            'email' => 'required|email:rfc|max:254',
            'message' => 'required|string|max:5000|min:10',
            'cf-turnstile-response' => 'required',
        ]);

        // Verify Turnstile token
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('mailflusher.turnstile.secret_key'),
            'response' => $request->input('cf-turnstile-response'),
            'remoteip' => $request->ip(),
        ]);

        if (! $response->json('success')) {
            return back()->withInput()->withErrors(['turnstile' => 'Captcha verification failed. Please try again.'], 'contact');
        }

        $key = 'contact-form:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Too many messages sent. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        RateLimiter::hit($key, 3600);

        Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to(config('mail.from.address'))
                ->replyTo($validated['email'], $validated['name'])
                ->subject('Contact form: '.$validated['name']);
        });

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Your message has been sent. We will get back to you soon.',
        ]);
    }
}
