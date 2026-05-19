<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MailFlusher') }} vs Firefox Relay — Which Email Alias Service is Right for You?</title>
    <meta name="description" content="An honest comparison of {{ config('app.name', 'MailFlusher') }} and Firefox Relay. Pricing, features, jurisdiction, and who each service is best for.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/vs/firefox-relay">

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
                {{ config('app.name', 'MailFlusher') }} vs Firefox Relay
            </h1>
            <p class="text-lg text-white/90 max-w-2xl mx-auto">
                Firefox Relay is a simple alias service from Mozilla. {{ config('app.name', 'MailFlusher') }} is a fuller-featured service hosted in Germany. Here's how they actually compare.
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
                        <li>You want your data under EU law rather than US law.</li>
                        <li>You need features like custom domains, filtering rules, GPG encryption, or a real API.</li>
                        <li>You want predictable reply support without per-thread reply limits.</li>
                        <li>You use a browser other than Firefox.</li>
                    </ul>
                </div>
                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-grey-700 mb-3">Pick Firefox Relay if…</h2>
                    <ul class="text-sm text-grey-700 space-y-2 list-disc pl-5">
                        <li>You're deep in the Firefox / Mozilla ecosystem and want tight browser integration.</li>
                        <li>You only need 5 throwaway aliases and don't care about reply support or rules.</li>
                        <li>You're fine with US-hosted infrastructure and Mozilla's privacy posture.</li>
                        <li>You want the cheapest possible upgrade path ($0.99/mo).</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature comparison table --}}
    <section class="py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-2">Side-by-side comparison</h2>
            <p class="text-grey-600 mb-8">Current as of {{ date('F Y') }}. Firefox Relay's terms and limits change periodically — double-check their site.</p>

            <div class="overflow-x-auto bg-white border border-grey-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-grey-50 border-b border-grey-200">
                        <tr>
                            <th class="text-left px-6 py-4 font-semibold text-grey-700"></th>
                            <th class="text-center px-6 py-4 font-semibold text-indigo-700">{{ config('app.name', 'MailFlusher') }}</th>
                            <th class="text-center px-6 py-4 font-semibold text-grey-600">Firefox Relay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Free plan aliases</td>
                            <td class="text-center px-6 py-3">10</td>
                            <td class="text-center px-6 py-3">5</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Paid entry tier</td>
                            <td class="text-center px-6 py-3">€1/month</td>
                            <td class="text-center px-6 py-3">$0.99/month (annual)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Unlimited aliases</td>
                            <td class="text-center px-6 py-3">Pro — €5/month</td>
                            <td class="text-center px-6 py-3">Premium ($0.99/mo)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Reply from alias</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Standard & Pro, unlimited</td>
                            <td class="text-center px-6 py-3 text-grey-500">Premium only</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Send email from alias</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard & Pro)</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Custom domains</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Pro</td>
                            <td class="text-center px-6 py-3 text-grey-500">Subdomain only (Premium)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">GPG / PGP encryption</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Filtering rules</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard & Pro)</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Multiple recipients / forwarding</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Developer API</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Full REST API</td>
                            <td class="text-center px-6 py-3 text-grey-500">No public API</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Browser integration</td>
                            <td class="text-center px-6 py-3">Bitwarden / 1Password — own extensions coming soon</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Native Firefox extension</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Server location</td>
                            <td class="text-center px-6 py-3">Germany (EU)</td>
                            <td class="text-center px-6 py-3">United States</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Subject to CLOUD Act</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">No</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Open source</td>
                            <td class="text-center px-6 py-3 text-grey-500">Not currently</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (server)</td>
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
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Ghost Inbox (E2E encrypted storage)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Pro)</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Outbound webhooks</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard+)</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">One-click importer from competitors</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-red-500 font-medium">No</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Where Firefox Relay wins --}}
    <section class="py-12 bg-grey-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where Firefox Relay is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">Price.</strong> Premium is $0.99/month when billed annually — the cheapest unlimited-alias plan on the market.</p>
                <p><strong class="text-grey-900">Firefox integration.</strong> If you use Firefox, the extension surfaces Relay buttons in email fields on sign-up forms. That's genuinely convenient.</p>
                <p><strong class="text-grey-900">Mozilla brand.</strong> Mozilla has a trusted privacy reputation and the service is lightweight and simple. If you just need a handful of aliases, it does the job.</p>
            </div>
        </div>
    </section>

    {{-- Where MailFlusher wins --}}
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where {{ config('app.name', 'MailFlusher') }} is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">EU hosting vs US hosting.</strong> Firefox Relay runs on Mozilla's US infrastructure. That means CLOUD Act reach, FISA §702, and US subpoena rules apply. We run from Germany, under EU GDPR — a fundamentally different legal posture.</p>
                <p><strong class="text-grey-900">Actual feature set.</strong> Firefox Relay is deliberately minimal — no rules, no GPG, no multi-recipient forwarding, no custom domains (only a subdomain), no public API, send-from-alias not supported. If any of those matter to you, Relay simply can't do them.</p>
                <p><strong class="text-grey-900">Reply support without limits.</strong> Relay replies are a Premium feature and historically have had per-thread caps. We don't cap replies.</p>
                <p><strong class="text-grey-900">Works outside Firefox.</strong> Our API works with Bitwarden, 1Password, and any HTTP client, and dedicated Chrome and Firefox extensions plus a mobile app are in development. You're not tied to one browser.</p>
                <p><strong class="text-grey-900">Privacy features Relay doesn't offer.</strong> Burner aliases that auto-expire by time or email count; leak attribution that flags when an unexpected sender shows up on an alias that was only ever given to one brand; optional pixel and link-proxy tracker stripping. If you care about email privacy beyond "my real address is hidden", these matter.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 bg-indigo-600">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Try {{ config('app.name', 'MailFlusher') }} free — 10 aliases, no card required</h2>
            <p class="text-white/90 mb-8">If you just need 5 aliases and nothing fancy, Firefox Relay is fine. If you want real email privacy tools and EU hosting, start here.</p>
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
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'MailFlusher') }}. Hosted in Germany, EU. &middot; <a href="/privacy" class="hover:text-white">Privacy</a> &middot; <a href="/terms" class="hover:text-white">Terms</a> &middot; <a href="/help" class="hover:text-white">Help</a></p>
        </div>
    </footer>

</body>
</html>
