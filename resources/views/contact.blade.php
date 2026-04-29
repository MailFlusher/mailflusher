<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Contact Us — {{ config('app.name', 'MailFlusher') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#19216C">

    @vite('resources/css/landing.css')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-grey-50 antialiased text-grey-900">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-grey-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center">
                    <img src="/svg/logo.svg" alt="{{ config('app.name', 'MailFlusher') }}" class="h-8 w-auto mr-3">
                    <span class="text-xl font-bold text-grey-900">{{ config('app.name', 'MailFlusher') }}</span>
                </a>
                <a href="/" class="text-grey-600 hover:text-grey-900 text-sm font-medium">Back to home</a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen py-20">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-center text-grey-900 mb-4">Contact Us</h1>
            <p class="text-center text-grey-600 mb-12">Have a question or need help? Send us a message and we'll get back to you.</p>

            @if (session('flash'))
                <div class="mb-8 p-4 rounded-lg text-sm font-medium {{ session('flash.type') === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                    {{ session('flash.message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}" class="bg-white border border-grey-200 rounded-lg p-8 space-y-6 shadow-sm">
                @csrf

                <div>
                    <label for="contact-name" class="block text-sm font-medium text-grey-700 mb-1">Name</label>
                    <input
                        id="contact-name"
                        name="name"
                        type="text"
                        required
                        maxlength="100"
                        value="{{ old('name') }}"
                        class="appearance-none bg-grey-50 border border-grey-300 rounded w-full p-3 text-grey-700 focus:ring focus:ring-indigo-200 focus:border-indigo-400 {{ $errors->contact->has('name') ? 'border-red-500' : '' }}"
                        placeholder="Your name"
                    >
                    @error('name', 'contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact-email" class="block text-sm font-medium text-grey-700 mb-1">Email</label>
                    <input
                        id="contact-email"
                        name="email"
                        type="email"
                        required
                        maxlength="254"
                        value="{{ old('email') }}"
                        class="appearance-none bg-grey-50 border border-grey-300 rounded w-full p-3 text-grey-700 focus:ring focus:ring-indigo-200 focus:border-indigo-400 {{ $errors->contact->has('email') ? 'border-red-500' : '' }}"
                        placeholder="you@example.com"
                    >
                    @error('email', 'contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact-message" class="block text-sm font-medium text-grey-700 mb-1">Message</label>
                    <textarea
                        id="contact-message"
                        name="message"
                        required
                        rows="6"
                        maxlength="5000"
                        class="appearance-none bg-grey-50 border border-grey-300 rounded w-full p-3 text-grey-700 focus:ring focus:ring-indigo-200 focus:border-indigo-400 {{ $errors->contact->has('message') ? 'border-red-500' : '' }}"
                        placeholder="How can we help?"
                    >{{ old('message') }}</textarea>
                    @error('message', 'contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="cf-turnstile" data-sitekey="{{ config('mailflusher.turnstile.site_key') }}" data-theme="light"></div>
                    @error('cf-turnstile-response', 'contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('turnstile', 'contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-cyan-400 hover:bg-cyan-300 text-cyan-900 px-6 py-3 font-bold shadow-sm">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
