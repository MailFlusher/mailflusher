@extends('layouts.auth')

@section('content')
    <div class="p-6 bg-indigo-900 min-h-screen flex justify-center items-center">
        <div class="w-full max-w-md">
            <div class="flex flex-col break-words bg-white border-2 rounded-lg shadow-lg overflow-hidden">
                <form class="" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="px-6 py-8 md:p-10">

                        <div class="text-center">
                            <img src="/svg/logo.svg" alt="MailFlusher" class="mx-auto h-48 w-auto mb-4" width="300" height="192">
                            <h1 class="font-bold text-3xl">
                                Create your account
                            </h1>
                        </div>

                        <div class="mx-auto mt-6 w-24 border-b-2 border-grey-200"></div>

                        <div class="mt-8 flex flex-wrap mb-6">
                            <label for="username" class="block text-grey-700 text-sm font-medium leading-6 mb-2">
                                {{ __('Username') }}
                            </label>

                            <div class="table w-full">
                                <input id="username" type="text" class="table-cell relative appearance-none bg-grey-100 rounded-l w-full p-3 text-grey-700 focus:ring{{ $errors->has('username') ? ' border-red-500' : '' }}" name="username" value="{{ old('username') }}" placeholder="mrunknown" required autofocus>
                                <div class="py-3 px-2 table-cell align-middle bg-grey-200 rounded-r text-grey-600">
                                    .{{ config('mailflusher.domain') }}
                                </div>
                            </div>

                            <p class="text-xs mt-1 text-grey-600">This will be your unique subdomain for your aliases<br> e.g. alias@<b>mrunknown</b>.{{ config('mailflusher.domain') }}</p>


                            @if ($errors->has('username'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('username') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-grey-700 text-sm font-medium leading-6 mb-2">
                                Your Real Email Address
                            </label>

                            <input id="email" type="email" class="appearance-none bg-grey-100 rounded w-full p-3 text-grey-700 focus:ring{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ old('email') }}" placeholder="mrunknown@example.com" required>

                            <p class="text-xs mt-1 text-grey-600">This is your recipient where emails will be forwarded</p>

                            @if ($errors->has('email'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('email') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="email-confirm" class="block text-grey-700 text-sm font-medium leading-6 mb-2">
                                Confirm Email Address
                            </label>

                            <input id="email-confirm" type="email" class="appearance-none bg-grey-100 rounded w-full p-3 text-grey-700 focus:ring" name="email_confirmation" value="{{ old('email_confirmation') }}" placeholder="mrunknown@example.com" required>
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="password" class="block text-grey-700 text-sm font-medium leading-6 mb-2">
                                {{ __('Password') }}
                            </label>

                            <input id="password" type="password" class="appearance-none bg-grey-100 rounded w-full p-3 text-grey-700 focus:ring{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password" placeholder="********" required>

                            @if ($errors->has('password'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>

                        @if (config('mailflusher.turnstile.site_key'))
                            <div class="mb-4">
                                <div class="cf-turnstile" data-sitekey="{{ config('mailflusher.turnstile.site_key') }}" data-theme="light"></div>
                                @if ($errors->has('captcha'))
                                    <p class="text-red-500 text-xs italic mt-2">
                                        {{ $errors->first('captcha') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                    </div>

                    <div class="px-6 md:px-10 py-4 bg-grey-50 border-t border-grey-100 flex flex-col gap-3 items-center">
                        <button type="submit" class="bg-indigo-600 w-full hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            {{ __('Register') }}
                        </button>

                        @if (config('services.google.client_id'))
                            <div class="w-full flex items-center gap-3">
                                <div class="flex-1 border-t border-grey-200"></div>
                                <span class="text-xs text-grey-400">or</span>
                                <div class="flex-1 border-t border-grey-200"></div>
                            </div>

                            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-grey-300 hover:bg-grey-50 text-grey-700 font-medium py-3 px-4 rounded transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                Sign up with Google
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            @if (Route::has('register'))
                <p class="w-full text-xs text-center text-indigo-100 mt-6">
                    Already have an account?
                    <a class="text-white hover:text-indigo-50 no-underline" href="{{ route('login') }}">
                        Login
                    </a>
                </p>
            @endif
        </div>
    </div>
@endsection