<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MailFlusher') }} vs Addy.io — Which Email Alias Service is Right for You?</title>
    <meta name="description" content="An honest comparison of {{ config('app.name', 'MailFlusher') }} and Addy.io (formerly AnonAddy). Pricing, features, hosting location, and who each service is best for.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/vs/addy-io">

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
                {{ config('app.name', 'MailFlusher') }} vs Addy.io
            </h1>
            <p class="text-lg text-white/90 max-w-2xl mx-auto">
                {{ config('app.name', 'MailFlusher') }} originally started from the Addy.io (formerly AnonAddy) codebase and has diverged significantly since. Feature sets overlap, but the services, pricing, jurisdiction, and roadmap are distinct. Here's how they compare today.
            </p>
        </div>
    </section>

    {{-- Full disclosure --}}
    <section class="py-10 bg-amber-50 border-b border-amber-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-amber-600 flex-shrink-0 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <div>
                    <h2 class="text-base font-semibold text-amber-900 mb-2">Full disclosure</h2>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        {{ config('app.name', 'MailFlusher') }} began from the <a href="https://addy.io" class="underline">Addy.io</a> (formerly AnonAddy) codebase — an excellent self-hostable alias service originally created by Will Browning. We credit that lineage openly. Since then we've made substantial modifications on top of it to build our own product: Swedish hosting, our own pricing, a different roadmap (Chrome/Firefox extensions and a mobile app in development), and a different focus. If you'd rather use the original or self-host, Addy.io is great software and we recommend it.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick verdict --}}
    <section class="py-12 bg-grey-50 border-b border-grey-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-indigo-200 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-indigo-700 mb-3">Pick {{ config('app.name', 'MailFlusher') }} if…</h2>
                    <ul class="text-sm text-grey-700 space-y-2 list-disc pl-5">
                        <li>You want a hosted service based in Sweden / EU rather than the UK.</li>
                        <li>You prefer monthly billing over annual.</li>
                        <li>You want a straightforward hosted product — no server setup, no self-host maintenance.</li>
                        <li>You want a simpler 3-tier price ladder (Free, €1, €5).</li>
                        <li>You want official {{ config('app.name', 'MailFlusher') }} browser extensions and a mobile app (in development) rather than relying on third-party Addy.io clients.</li>
                    </ul>
                </div>
                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <h2 class="text-lg font-bold text-grey-700 mb-3">Pick Addy.io if…</h2>
                    <ul class="text-sm text-grey-700 space-y-2 list-disc pl-5">
                        <li>You want the source directly from its original author and maintainer.</li>
                        <li>You're comfortable with annual billing at the power-user tier.</li>
                        <li>You want every niche feature from the upstream release the same day it ships.</li>
                        <li>You want to self-host and run your own instance.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Feature comparison table --}}
    <section class="py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-2">Side-by-side comparison</h2>
            <p class="text-grey-600 mb-8">Current as of {{ date('F Y') }}. Double-check Addy.io's pricing page for the latest.</p>

            <div class="overflow-x-auto bg-white border border-grey-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-grey-50 border-b border-grey-200">
                        <tr>
                            <th class="text-left px-6 py-4 font-semibold text-grey-700"></th>
                            <th class="text-center px-6 py-4 font-semibold text-indigo-700">{{ config('app.name', 'MailFlusher') }}</th>
                            <th class="text-center px-6 py-4 font-semibold text-grey-600">Addy.io</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Free plan aliases</td>
                            <td class="text-center px-6 py-3">10</td>
                            <td class="text-center px-6 py-3">20</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Entry paid tier</td>
                            <td class="text-center px-6 py-3">€1/month</td>
                            <td class="text-center px-6 py-3">~$1/month (Lite)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Top tier</td>
                            <td class="text-center px-6 py-3">Pro — €5/month</td>
                            <td class="text-center px-6 py-3">Pro — ~$3/month</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Billing cadence</td>
                            <td class="text-center px-6 py-3">Monthly</td>
                            <td class="text-center px-6 py-3">Monthly &amp; annual</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Unlimited aliases</td>
                            <td class="text-center px-6 py-3">Pro plan</td>
                            <td class="text-center px-6 py-3">Pro plan</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Reply / send from alias</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Standard & Pro</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">All paid plans</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Custom domains</td>
                            <td class="text-center px-6 py-3">Pro plan</td>
                            <td class="text-center px-6 py-3">Pro plan</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">GPG / PGP encryption</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Filtering rules</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard & Pro)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Works with Addy.io client apps (via custom host URL)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Mostly yes</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Official browser extensions</td>
                            <td class="text-center px-6 py-3 text-grey-500">Coming soon</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Official mobile app</td>
                            <td class="text-center px-6 py-3 text-grey-500">Coming soon</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (iOS & Android)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Hosting location</td>
                            <td class="text-center px-6 py-3">Sweden (EU)</td>
                            <td class="text-center px-6 py-3">United Kingdom</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Governing law</td>
                            <td class="text-center px-6 py-3">EU (GDPR)</td>
                            <td class="text-center px-6 py-3">UK (UK GDPR)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Open source</td>
                            <td class="text-center px-6 py-3 text-grey-500">Not currently</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Self-hostable</td>
                            <td class="text-center px-6 py-3 text-grey-500">No (hosted service only)</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes</td>
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
                            <td class="text-center px-6 py-3 text-grey-500">No</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">Outbound webhooks</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (Standard+)</td>
                            <td class="text-center px-6 py-3 text-grey-500">API polling</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-medium text-grey-700">One-click importer from competitors</td>
                            <td class="text-center px-6 py-3 text-green-600 font-medium">Yes (imports from Addy.io too)</td>
                            <td class="text-center px-6 py-3 text-grey-500">No</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Where Addy.io wins --}}
    <section class="py-12 bg-grey-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where Addy.io is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">More generous free tier.</strong> Addy.io gives 20 aliases on the free plan vs our 10. If you're a light user who wants a lot of aliases for free, Addy.io wins on volume.</p>
                <p><strong class="text-grey-900">Open source and self-hostable.</strong> Addy.io's source is public and the server can be run on your own infrastructure. {{ config('app.name', 'MailFlusher') }} is a hosted service only — we don't currently publish our source or ship a self-host distribution.</p>
                <p><strong class="text-grey-900">Mature clients ecosystem.</strong> Addy.io has official and community-maintained browser extensions, mobile apps, and CLI tools available today. Our own extensions and mobile app are in development but not yet released.</p>
                <p><strong class="text-grey-900">Annual billing option.</strong> Addy.io offers annual billing at a discount; we currently don't.</p>
            </div>
        </div>
    </section>

    {{-- Where MailFlusher wins --}}
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-grey-900 mb-6">Where {{ config('app.name', 'MailFlusher') }} is stronger</h2>
            <div class="space-y-4 text-grey-700">
                <p><strong class="text-grey-900">EU-based hosting and governance.</strong> All servers in Sweden, all processing under EU GDPR. Post-Brexit, Addy.io is under UK data-protection law, which is diverging from the EU regime. If strict EU residency matters to you, we're the cleaner choice.</p>
                <p><strong class="text-grey-900">Simpler pricing.</strong> Three plans, clear limits, monthly euros. No regional pricing confusion, no annual-only tiers to weigh.</p>
                <p><strong class="text-grey-900">Dedicated brand and focus.</strong> We're a hosted-only product built for people who don't want to run their own servers. One clean flow from sign-up to alias to reply.</p>
                <p><strong class="text-grey-900">First-party clients on the roadmap.</strong> Official {{ config('app.name', 'MailFlusher') }}-branded Chrome and Firefox extensions plus a mobile app are in active development. Once shipped they'll be tightly integrated with our service rather than a generic Addy.io client.</p>
                <p><strong class="text-grey-900">Three privacy features Addy.io doesn't ship.</strong><br /><strong>Burner aliases</strong> that auto-deactivate after a time window or email count, ideal for one-shot signups.<br /><strong>Leak attribution</strong> that learns each alias's legitimate sender and flags mail from unrelated domains as a suspected data-sale — useful when you give a unique alias to every brand and want to know which one leaked.<br /><strong>Opt-in tracker stripping</strong> that removes 1×1 marketing pixels and routes links through our redirect proxy that strips UTM/click-tracker parameters.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 bg-indigo-600">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Try {{ config('app.name', 'MailFlusher') }} free — no card required</h2>
            <p class="text-white/90 mb-8">Built on Addy.io foundations, heavily modified, hosted in Sweden. If you'd rather run the original yourself, Addy.io has great self-host docs — either way you're getting excellent software.</p>
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
