<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MailFlusher') }} vs SimpleLogin — Which Email Alias Service is Right for You?</title>
    <meta name="description" content="An honest comparison of {{ config('app.name', 'MailFlusher') }} and SimpleLogin. Pricing, features, jurisdiction, and who each service is best for.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/vs/simplelogin">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#19216C">

    @vite('resources/css/landing.css')
</head>
<body class="bg-white antialiased text-grey-900">

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

    {{-- Hero --}}
    <section class="bg-indigo-600 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-cyan-200 text-sm font-bold uppercase tracking-wide mb-3">Comparison</p>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-4">
                {{ config('app.name', 'MailFlusher') }} vs SimpleLogin
            </h1>
            <p class="text-lg text-white/90 max-w-2xl mx-auto">
                Both services let you create email aliases to protect your real address. Here's an honest comparison so you can pick the one that fits you.
            </p>
        </div>
    </section>

    {{-- Quick verdict --}}
    <section class="py-12 bg-grey-50 border-b border-grey-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-indigo-200 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-indigo-700 mb-3">Pick {{ config('app.name', 'MailFlusher') }} if…</h2>
                    <ul class="text-sm text-grey-700 space-y-2 list-disc pl-5">
                        <li>You want a service hosted entirely in the EU, outside US legal reach.</li>
                        <li>You prefer monthly billing over annual lock-in.</li>
                        <li>You want a powerful alias feature set (rules, custom domains, GPG, full API) without the Proton ecosystem tie-in.</li>
                        <li>You like knowing exactly who runs the service and where.</li>
                    </ul>
                </div>
                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-grey-700 mb-3">Pick SimpleLogin if…</h2>
                    <ul class="text-sm text-grey-700 space-y-2 list-disc pl-5">
                        <li>You already use Proton Mail / Proton Pass and want everything in one account.</li>
                        <li>You want polished browser extensions for Chrome, Firefox, Safari, and mobile apps.</li>
                        <li>You prefer a larger, more established brand with full-time staff.</li>
                        <li>You're fine with annual billing (~$30/year) and don't mind a Swiss-hosted service.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature comparison table --}}
    <section class="py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-2">Side-by-side feature comparison</h2>
            <p class="text-grey-600 mb-8">Current as of {{ date('F Y') }}. Always double-check competitor sites for up-to-date pricing.</p>

            <div class="overflow-x-auto bg-white border border-grey-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-grey-50 border-b border-grey-200">
                        <tr>
                            <th class="text-left px-6 py-4 font-semibold text-grey-700"></th>
                            <th class="text-center px-6 py-4 font-semibold text-indigo-700">{{ config('app.name', 'MailFlusher') }}</th>
                            <th class="text-center px-6 py-4 font-semibold text-grey-600">SimpleLogin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Free plan aliases</td>
                            <td class="text-center px-6 py-3">10</td>
                            <td class="text-center px-6 py-3">10</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Paid entry tier</td>
                            <td class="text-center px-6 py-3">€1/month</td>
                            <td class="text-center px-6 py-3">~$30/year ($2.50/mo)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Unlimited aliases plan</td>
                            <td class="text-center px-6 py-3">Pro — €5/month</td>
                            <td class="text-center px-6 py-3">Premium — ~$30/year</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Reply from alias</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Standard & Pro</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">All plans</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Custom domains</td>
                            <td class="text-center px-6 py-3">Pro plan</td>
                            <td class="text-center px-6 py-3">Premium</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">GPG / PGP encryption</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Filtering rules</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard & Pro)</td>
                            <td class="text-center px-6 py-3 text-grey-500">Limited</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Open source</td>
                            <td class="text-center px-6 py-3 text-grey-500">Not currently</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Self-hostable</td>
                            <td class="text-center px-6 py-3 text-grey-500">No (hosted only)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Developer API</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Server location</td>
                            <td class="text-center px-6 py-3">Sweden (EU)</td>
                            <td class="text-center px-6 py-3">Switzerland / Germany</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Owned by</td>
                            <td class="text-center px-6 py-3">Independent (Sweden)</td>
                            <td class="text-center px-6 py-3">Proton AG (Switzerland)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Subject to US CLOUD Act</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">No</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Browser extensions</td>
                            <td class="text-center px-6 py-3 text-grey-500">Chrome & Firefox — coming soon</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Chrome, Firefox, Safari</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Mobile app</td>
                            <td class="text-center px-6 py-3 text-grey-500">Coming soon</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">iOS & Android</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Burner / auto-expiring aliases</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (time & email-count limits)</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Leak attribution / data-sale detection</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Tracker / pixel stripping</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Pixels + link-proxy (opt-in)</td>
                            <td class="text-center px-6 py-3 text-grey-500">Tracker removal (limited)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Ghost Inbox (E2E encrypted storage)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Pro)</td>
                            <td class="text-center px-6 py-3 text-grey-500">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Outbound webhooks</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard+)</td>
                            <td class="text-center px-6 py-3 text-grey-500">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">One-click importer from competitors</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-grey-500">No</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Where SimpleLogin wins --}}
    <section class="py-12 bg-grey-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where SimpleLogin is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">Ecosystem.</strong> SimpleLogin is owned by Proton, so if you already use Proton Mail or Proton Pass, everything lives under one account. {{ config('app.name', 'MailFlusher') }} is independent — there's no bundled mailbox.</p>
                <p><strong class="text-grey-900">Native apps and extensions — today.</strong> Official browser extensions for Chrome, Firefox, and Safari, plus iOS and Android apps shipping now. We have Chrome and Firefox extensions and a mobile app in development, but they're not released yet — alias creation currently happens in the web UI or via API.</p>
                <p><strong class="text-grey-900">Brand recognition.</strong> SimpleLogin has been around longer and has a larger team behind it. If having a well-known brand matters to you, that's a real factor.</p>
            </div>
        </div>
    </section>

    {{-- Where MailFlusher wins --}}
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where {{ config('app.name', 'MailFlusher') }} is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">EU jurisdiction.</strong> We operate entirely from Sweden under GDPR. No US subsidiary, no CLOUD Act exposure. SimpleLogin is also outside the US, but our single-jurisdiction Swedish footprint is simpler to reason about.</p>
                <p><strong class="text-grey-900">Monthly billing.</strong> Our Standard plan is €1/month and Pro is €5/month — month-to-month. SimpleLogin Premium is annual-only at ~$30/year.</p>
                <p><strong class="text-grey-900">Deep power-user feature set.</strong> Advanced filtering rules, per-alias recipient routing, auto-create regex, multiple usernames for compartmentalization — {{ config('app.name', 'MailFlusher') }} was originally based on Addy.io, inheriting its reputation as one of the most customizable alias services on the market, and has been heavily extended since.</p>
                <p><strong class="text-grey-900">Burner aliases, leak attribution, and tracker stripping.</strong> {{ config('app.name', 'MailFlusher') }} ships three privacy features SimpleLogin doesn't offer today: auto-expiring "burner" aliases (by time or email count), per-alias leak detection that flags when an unexpected sender shows up on an alias you only ever gave to one brand, and an opt-in pixel + link-proxy tracker stripper that removes 1×1 tracking images and rewrites UTM/click-tracker parameters before they reach your inbox.</p>
                <p><strong class="text-grey-900">Independent operation.</strong> No parent company, no acquisition risk, no product-bundling pressure. Just email forwarding done well.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 bg-indigo-600">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Try {{ config('app.name', 'MailFlusher') }} free — 10 aliases, no card required</h2>
            <p class="text-white/90 mb-8">If it's not for you, pick SimpleLogin and walk away. We won't make it hard to export your data or cancel.</p>
            @if (config('mailflusher.enable_registration'))
                <a href="{{ config('app.url') }}/register" class="inline-flex items-center justify-center rounded-md bg-cyan-400 hover:bg-cyan-300 text-cyan-900 px-8 py-3 font-bold shadow-lg">
                    Get started for free
                    <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </a>
            @endif
        </div>
    </section>

    <footer class="bg-grey-900 text-grey-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'MailFlusher') }}. Hosted in Sweden, EU. &middot; <a href="/privacy" class="hover:text-white">Privacy</a> &middot; <a href="/terms" class="hover:text-white">Terms</a> &middot; <a href="/help" class="hover:text-white">Help</a></p>
        </div>
    </footer>

</body>
</html>
