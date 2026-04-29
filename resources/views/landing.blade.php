<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MailFlusher') }} — Anonymous Email Forwarding | Protect Your Email Privacy</title>
    <meta name="description" content="MailFlusher is an anonymous email forwarding service hosted in Sweden, EU. Create unlimited email aliases to protect your real address from spam and data breaches. GDPR compliant with GPG encryption support.">
    <meta name="keywords" content="email forwarding, anonymous email, email alias, email privacy, spam protection, email encryption, GPG email, disposable email, temporary email, email masking, self-hosted email">
    <meta name="author" content="{{ config('app.name', 'MailFlusher') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'MailFlusher') }} — Anonymous Email Forwarding">
    <meta property="og:description" content="Protect your real email address with anonymous forwarding aliases. Create unlimited aliases, reply anonymously, and encrypt with GPG. Hosted in Sweden, EU.">
    <meta property="og:url" content="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}">
    <meta property="og:site_name" content="{{ config('app.name', 'MailFlusher') }}">
    <meta property="og:image" content="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/MailFlusher_logo_big.png">
    <meta property="og:image:width" content="2048">
    <meta property="og:image:height" content="2048">
    <meta property="og:image:type" content="image/png">
    <meta property="og:locale" content="{{ app()->getLocale() }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ config('app.name', 'MailFlusher') }} — Anonymous Email Forwarding">
    <meta name="twitter:description" content="Protect your real email address with anonymous forwarding aliases. Hosted in Sweden, EU. GDPR compliant. GPG encryption.">
    <meta name="twitter:image" content="https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/MailFlusher_logo_big.png">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#19216C">

    {{-- Structured Data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebApplication",
        "name": "{{ config('app.name', 'MailFlusher') }}",
        "applicationCategory": "SecurityApplication",
        "browserRequirements": "Requires a modern web browser",
        "description": "Anonymous email forwarding service hosted in Sweden, EU. Create unlimited email aliases to protect your real address from spam and data breaches. GDPR compliant.",
        "url": "https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}",
        "operatingSystem": "Web",
        "offers": [
            {
                "@@type": "Offer",
                "name": "Free",
                "price": "0",
                "priceCurrency": "EUR",
                "description": "10 aliases, 2 burner aliases, 1 recipient, leak attribution, pixel stripping, importer, 10 MB bandwidth"
            },
            {
                "@@type": "Offer",
                "name": "Standard",
                "price": "1",
                "priceCurrency": "EUR",
                "billingDuration": "P1M",
                "description": "20 aliases, 20 burner aliases, 5 recipients, reply/send, rules, leak attribution, pixel & link stripping, outbound webhooks, importer, 200 MB bandwidth"
            },
            {
                "@@type": "Offer",
                "name": "Pro",
                "price": "5",
                "priceCurrency": "EUR",
                "billingDuration": "P1M",
                "description": "Unlimited aliases, unlimited burner aliases, 30 recipients, custom domains, leak attribution, pixel & link stripping, outbound webhooks, Ghost Inbox (E2E encrypted storage), importer, unlimited bandwidth"
            }
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "What is an email alias?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "An email alias is a forwarding address that redirects emails to your real inbox. You give out the alias instead of your real email. If the alias starts receiving spam, you simply deactivate it."
                }
            },
            {
                "@@type": "Question",
                "name": "Can I reply to emails from an alias?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Yes. When you reply to a forwarded email, the reply is sent through the alias. The recipient will only see the alias address, not your real email."
                }
            },
            {
                "@@type": "Question",
                "name": "What happens if an alias gets spam?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Simply deactivate or delete the alias. All future emails to that alias will be silently discarded or bounced. Your real email address remains unaffected."
                }
            },
            {
                "@@type": "Question",
                "name": "Is my email encrypted?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "You can add your GPG/OpenPGP public key and all forwarded emails will be encrypted before delivery. Attachments are encrypted too. You can even encrypt the email subject."
                }
            },
            {
                "@@type": "Question",
                "name": "Does this work with any email provider?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Yes. Email aliases work with any email provider — Gmail, Outlook, ProtonMail, Tutanota, or any other service. Emails are simply forwarded to whatever address you use."
                }
            },
            {
                "@@type": "Question",
                "name": "What is catch-all?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Catch-all means that any email sent to your username domain will be automatically forwarded to you, even if the alias doesn't exist yet. The alias is created on the fly. Without catch-all, only pre-existing aliases will receive email."
                }
            }
            ,{
                "@@type": "Question",
                "name": "How do I hide my email address online?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Instead of giving out your real email address, create an email alias and use that instead. The alias forwards emails to your real inbox, but the sender never sees your actual email address."
                }
            },
            {
                "@@type": "Question",
                "name": "What's the difference between an email alias and a disposable email?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Disposable email services give you a temporary inbox that expires. Email aliases are permanent forwarding addresses that you control. You can reply from aliases, manage them, enable encryption, and keep them active as long as you want."
                }
            },
            {
                "@@type": "Question",
                "name": "Is email aliasing better than Gmail's plus addressing?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Gmail's plus addressing is easy to strip. With email aliases, you use a completely different email address that can't be traced back to your real one. You can also deactivate individual aliases."
                }
            },
            {
                "@@type": "Question",
                "name": "How do I know who sold my data?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Use a unique alias for every service. If you start receiving spam on one specific alias, you know exactly which company leaked or sold your data."
                }
            },
            {
                "@@type": "Question",
                "name": "Where are your servers located?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "All servers are located in Sweden, within the European Union. Your data is protected by GDPR and Swedish privacy regulations. Data never leaves the EU."
                }
            }
        ]
    }
    </script>

    {{-- Organization Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "{{ config('app.name', 'MailFlusher') }}",
        "url": "https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}",
        "logo": "https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/svg/logo.svg",
        "description": "Anonymous email forwarding service hosted in Sweden, EU. GDPR compliant.",
        "address": {
            "@@type": "PostalAddress",
            "addressCountry": "SE"
        },
        "contactPoint": {
            "@@type": "ContactPoint",
            "contactType": "customer support",
            "url": "https://{{ config('mailflusher.landing_domain', config('mailflusher.domain')) }}/contact"
        }
    }
    </script>

    {{-- Critical CSS inlined for fast LCP --}}
    <style>
        *, ::after, ::before { box-sizing: border-box; border: 0 solid #e4e7eb; }
        html { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.5; font-size: 16px; -webkit-font-smoothing: antialiased; }
        body { margin: 0; }
        img { display: block; max-width: 100%; height: auto; }
        a { color: inherit; text-decoration: inherit; }
        .bg-white { background-color: #fff; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .text-grey-900 { color: #1F2933; }
        .sticky { position: sticky; }
        .top-0 { top: 0; }
        .z-50 { z-index: 50; }
        .bg-white\/90 { background-color: rgba(255,255,255,0.9); }
        .backdrop-blur-md { backdrop-filter: blur(12px); }
        .border-b { border-bottom-width: 1px; }
        .border-grey-100 { border-color: #E4E7EB; }
        .max-w-6xl { max-width: 72rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .h-16 { height: 4rem; }
        .h-8 { height: 2rem; }
        .w-auto { width: auto; }
        .mr-3 { margin-right: 0.75rem; }
        .text-xl { font-size: 1.25rem; }
        .font-bold { font-weight: 700; }
        .space-x-2 > :not(:first-child) { margin-left: 0.5rem; }
        .hidden { display: none; }
        .text-sm { font-size: 0.875rem; }
        .font-medium { font-weight: 500; }
        .py-20 { padding-top: 5rem; padding-bottom: 5rem; }
        .bg-indigo-600 { background-color: #2563eb; }
        .text-center { text-align: center; }
        .text-4xl { font-size: 2.25rem; }
        .text-white { color: #fff; }
        .leading-tight { line-height: 1.25; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-10 { margin-bottom: 2.5rem; }
        .text-lg { font-size: 1.125rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-4 { gap: 1rem; }
        .justify-center { justify-content: center; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-full { border-radius: 9999px; }
        .px-8 { padding-left: 2rem; padding-right: 2rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1); }
        .inline-flex { display: inline-flex; }
        .border-2 { border-width: 2px; }
        .border-white\/60 { border-color: rgba(255,255,255,0.6); }
        @media (min-width: 640px) { .sm\:inline-block { display: inline-block; } .sm\:text-5xl { font-size: 3rem; } .sm\:py-28 { padding-top: 7rem; padding-bottom: 7rem; } .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; } .sm\:flex-row { flex-direction: row; } }
        @media (min-width: 1024px) { .lg\:text-6xl { font-size: 4rem; } .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; } .lg\:h-24 { height: 6rem; } }
        .flex-col { flex-direction: column; }
        .max-w-4xl { max-width: 56rem; }
        .max-w-2xl { max-width: 42rem; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }
    </style>

    {{-- Full CSS --}}
    @vite('resources/css/landing.css')
</head>
<body class="bg-white antialiased text-grey-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2 focus:rounded">Skip to content</a>

    {{-- Navigation --}}
    <nav id="landing-nav" class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-grey-100 transition-shadow duration-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="/svg/logo.svg" alt="{{ config('app.name', 'MailFlusher') }} - Anonymous email forwarding service" class="h-8 w-auto mr-3" width="120" height="32">
                    <span class="text-xl font-bold text-grey-900">{{ config('app.name', 'MailFlusher') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="#how-it-works" data-nav="how-it-works" class="nav-pill hidden sm:inline-block px-3 py-1.5 rounded-full text-sm font-medium text-grey-500 hover:text-grey-900 transition-all duration-200">How it works</a>
                    <a href="#features" data-nav="features" class="nav-pill hidden sm:inline-block px-3 py-1.5 rounded-full text-sm font-medium text-grey-500 hover:text-grey-900 transition-all duration-200">Features</a>
                    <a href="#faq" data-nav="faq" class="nav-pill hidden sm:inline-block px-3 py-1.5 rounded-full text-sm font-medium text-grey-500 hover:text-grey-900 transition-all duration-200">FAQ</a>
                    <a href="#pricing" data-nav="pricing" class="nav-pill hidden sm:inline-block px-3 py-1.5 rounded-full text-sm font-medium text-grey-500 hover:text-grey-900 transition-all duration-200">Pricing</a>
                    <a href="/help" class="hidden sm:inline-block px-3 py-1.5 text-sm font-medium text-grey-500 hover:text-grey-900 transition-colors">Help</a>
                    <a href="/contact" class="hidden sm:inline-block px-3 py-1.5 text-sm font-medium text-grey-500 hover:text-grey-900 transition-colors">Contact</a>
                    <span class="hidden sm:inline-block w-px h-5 bg-grey-200 mx-1"></span>
                    <a href="{{ $appUrl }}/login" class="text-grey-600 hover:text-grey-900 text-sm font-medium px-2">Login</a>
                    @if (config('mailflusher.enable_registration'))
                        <a href="{{ $appUrl }}/register" class="inline-flex items-center justify-center rounded-full bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-1.5 text-sm font-bold shadow-sm transition-colors">
                            Sign Up
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <main id="main-content">
    <section class="bg-indigo-600 py-20 sm:py-28">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-6 mb-6">
                <img src="/svg/icon-logo.svg" alt="{{ config('app.name', 'MailFlusher') }} email privacy shield icon" class="h-16 sm:h-20 lg:h-24 w-auto hidden sm:block" width="96" height="96">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    A different email address for every website
                </h1>
            </div>
            <p class="text-lg sm:text-xl text-white mb-4 max-w-2xl mx-auto">
                Protect your real email address from spam, data breaches, and unwanted marketing. Create unique email aliases that forward to your real inbox — deactivate or delete them anytime without affecting your primary email.
            </p>
            <p class="text-white/90 text-base sm:text-lg font-medium mb-4 max-w-2xl mx-auto">
                100% Swedish. 100% EU. Out of reach of the US CLOUD Act.
            </p>
            <p class="text-white text-sm mb-10">
                <svg class="inline-block h-4 w-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                Hosted in Sweden, EU &middot; GDPR compliant &middot; No tracking
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if (config('mailflusher.enable_registration'))
                    <a href="{{ $appUrl }}/register" class="inline-flex items-center justify-center rounded-md bg-cyan-400 hover:bg-cyan-300 text-cyan-900 px-8 py-3 font-bold shadow-lg text-lg">
                        Get started for free
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                @endif
                <a href="{{ $appUrl }}/login" class="inline-flex items-center justify-center rounded-md border-2 border-white/60 hover:border-white text-white hover:text-white px-8 py-3 font-bold text-lg">
                    Login
                </a>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="py-20 bg-grey-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-grey-900 mb-4">How email aliasing works</h2>
            <p class="text-center text-grey-600 mb-16 max-w-2xl mx-auto">Email aliases act as a privacy shield between your real email address and the outside world. Share aliases instead of your primary email — stay in complete control of who can contact you.</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                {{-- Step 1 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-grey-900 mb-3">1. Create aliases</h3>
                    <p class="text-grey-600">Generate a unique email alias for every website, newsletter, or service you sign up to. Use it anywhere you'd normally give your real email address.</p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-8 w-8 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-grey-900 mb-3">2. Receive emails safely</h3>
                    <p class="text-grey-600">Emails sent to your alias are instantly forwarded to your real inbox. Your real address stays completely hidden from the sender. Optionally encrypt with GPG.</p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-grey-900 mb-3">3. Reply anonymously</h3>
                    <p class="text-grey-600">Reply directly from your email client. The recipient only sees the alias address — your real email is never revealed. Works with Gmail, Outlook, and any email provider.</p>
                </div>

                {{-- Step 4 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-grey-900 mb-3">4. Block spam instantly</h3>
                    <p class="text-grey-600">Getting unwanted emails? Deactivate the alias to silently discard all messages, or delete it to bounce them back. Your real inbox stays clean.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-grey-900 mb-4">Privacy-first email forwarding features</h2>
            <p class="text-center text-grey-600 mb-16 max-w-2xl mx-auto">Everything you need to take control of your inbox, stop spam, and protect your email identity online.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Unlimited email aliases</h3>
                    <p class="text-grey-600 text-sm">Create a unique email address for every website, newsletter, and online service. If one gets compromised in a data breach, the rest stay safe. Identify exactly who sold your data.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Reply and send from aliases</h3>
                    <p class="text-grey-600 text-sm">Respond to emails and send new messages from any alias. The recipient only sees the alias address — your real email stays hidden. Compatible with Gmail, Outlook, Apple Mail, and any standard email client.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">GPG/OpenPGP encryption</h3>
                    <p class="text-grey-600 text-sm">Add your public GPG key and every forwarded email is encrypted end-to-end before delivery — including attachments and optionally the subject line. Zero-knowledge: even we can't read your messages.</p>
                </div>

                {{-- Feature 4 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Stop spam and phishing</h3>
                    <p class="text-grey-600 text-sm">Receiving spam or phishing attempts? Deactivate the alias to silently discard all messages, or delete it to bounce them back to the sender. Block specific senders or entire domains from your blocklist.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Multiple recipients per alias</h3>
                    <p class="text-grey-600 text-sm">Route a single alias to multiple real email addresses simultaneously. Perfect for shared inboxes, team email addresses, or forwarding to both personal and work accounts.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Catch-all and on-the-fly aliases</h3>
                    <p class="text-grey-600 text-sm">With catch-all enabled, aliases are created automatically when they receive their first email. Make up any address on the spot — like shopping@yourusername.{{ config('mailflusher.domain') }} — no setup needed.</p>
                </div>

                {{-- Feature 7 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Smart filtering rules</h3>
                    <p class="text-grey-600 text-sm">Create custom rules to automatically forward, block, or redirect incoming emails based on the sender address, subject line, or which alias received the message. Automate your inbox management.</p>
                </div>

                {{-- Feature 8 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Works with any email provider</h3>
                    <p class="text-grey-600 text-sm">MailFlusher works with Gmail, Outlook, ProtonMail, Tutanota, Yahoo, iCloud, and any other email provider. No migration needed — just start using aliases that forward to your existing inbox.</p>
                </div>

                {{-- Feature 9 --}}
                <div class="border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Developer-friendly API</h3>
                    <p class="text-grey-600 text-sm">Manage aliases, recipients, and domains programmatically with our complete REST API. Generate API keys to build custom integrations, browser extensions, or automate your email workflow.</p>
                </div>

                {{-- Feature 10: Burner aliases (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Burner aliases that self-destruct</h3>
                    <p class="text-grey-600 text-sm">Create aliases that auto-deactivate after a set time (1&nbsp;hour, 24&nbsp;hours, 3&nbsp;days, up to 30&nbsp;days) or a set number of emails (1, 3, or 10). Perfect for one-time signups, downloadables, or giving your email to a site you don't fully trust.</p>
                </div>

                {{-- Feature 11: Leak attribution (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Know who leaked your address</h3>
                    <p class="text-grey-600 text-sm">We learn the legitimate sender for each of your aliases and flag any mail from unrelated domains as a suspected leak. When <em>netflix@mrunknown.{{ config('mailflusher.domain') }}</em> starts getting mail from some marketing network, you'll know Netflix probably sold or leaked your address.</p>
                </div>

                {{-- Feature 12: Tracker stripping (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Tracker and pixel stripping</h3>
                    <p class="text-grey-600 text-sm">Mailchimp, HubSpot, SendGrid and friends track whether you opened an email and what you clicked. Pixel stripping is included on all plans. <strong>Standard and Pro</strong> add link rewriting through our <code class="bg-white/60 rounded px-1">/r/</code> proxy that strips UTM and click-tracking parameters before the 302 redirect.</p>
                </div>

                {{-- Feature 13: Ghost Inbox (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New &middot; Pro</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Ghost Inbox — even we can't read it</h3>
                    <p class="text-grey-600 text-sm">Flag any alias as "ghost mode" and incoming mail is encrypted with an OpenPGP key that only your browser can unlock. The ciphertext is stored on our servers; the plaintext lives only in your browser. Database dumps, subpoenas for stored content, stolen backups — none of it reveals the message. If you forget the passphrase, not even we can recover it.</p>
                </div>

                {{-- Feature 14: Webhooks (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New &middot; Standard</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Outbound webhooks</h3>
                    <p class="text-grey-600 text-sm">Subscribe to <code>alias.received</code>, <code>alias.blocked</code>, and <code>alias.leaked</code> events. Every delivery is HMAC-SHA256 signed with a per-webhook secret, retried with exponential backoff, and logged so you can audit failures. Build Zapier-like automations, alert pipelines, or custom integrations without polling our API.</p>
                </div>

                {{-- Feature 15: Importer (NEW) --}}
                <div class="border border-cyan-200 bg-cyan-50/40 dark:bg-cyan-900/10 rounded-lg p-6 relative">
                    <span class="absolute top-4 right-4 inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-700">New</span>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">One-click import from SimpleLogin or Addy.io</h3>
                    <p class="text-grey-600 text-sm">Moving in from another alias service? Paste your API token and we'll fetch your existing aliases and recreate them on your MailFlusher subdomain — descriptions and active states preserved. Preview the import first, then commit when you're happy. Firefox Relay doesn't expose a public API, but we'll handle those manually.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Who it's for --}}
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-grey-900 mb-4">Who uses email aliases?</h2>
            <p class="text-center text-grey-600 mb-12 max-w-2xl mx-auto">Email aliasing is used by privacy-conscious individuals and professionals around the world.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="border border-grey-200 rounded-lg p-5">
                    <h3 class="font-semibold text-grey-900 mb-2">Online shoppers</h3>
                    <p class="text-grey-600 text-sm">Use a unique alias for every online store. If one gets breached or starts sending spam, deactivate just that alias.</p>
                </div>
                <div class="border border-grey-200 rounded-lg p-5">
                    <h3 class="font-semibold text-grey-900 mb-2">Freelancers & professionals</h3>
                    <p class="text-grey-600 text-sm">Compartmentalize client communications. Give each client a different alias and manage everything from one inbox.</p>
                </div>
                <div class="border border-grey-200 rounded-lg p-5">
                    <h3 class="font-semibold text-grey-900 mb-2">Privacy enthusiasts</h3>
                    <p class="text-grey-600 text-sm">Stop cross-referencing of your accounts across websites. Each service sees a different email, making profiling impossible.</p>
                </div>
                <div class="border border-grey-200 rounded-lg p-5">
                    <h3 class="font-semibold text-grey-900 mb-2">Newsletter readers</h3>
                    <p class="text-grey-600 text-sm">Subscribe to newsletters with aliases. If they become too frequent, deactivate the alias instead of unsubscribing from each one.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Jurisdiction / Why EU --}}
    <section id="jurisdiction" class="py-20 bg-grey-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full mb-4">Why jurisdiction matters</span>
                <h2 class="text-3xl font-bold text-grey-900 mb-4">Your email should live under European law</h2>
                <p class="text-grey-600 max-w-2xl mx-auto">
                    Most email forwarding services are US-based. That means your aliases, metadata, and forwarded messages can be compelled under the CLOUD Act, FISA §702, or an NSL — often without any notification to you. {{ config('app.name', 'MailFlusher') }} is run entirely from Sweden, under EU law.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">Servers in Sweden</h3>
                    <p class="text-grey-600 text-sm">All infrastructure is located in Sweden. Your data never leaves the EU. No US subsidiary, no US parent company, no data replication outside the union.</p>
                </div>

                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">GDPR by default</h3>
                    <p class="text-grey-600 text-sm">You have the right to access, export, and delete your data at any time. We never sell it, profile on it, or use it for advertising. Subject access requests are answered within 30 days.</p>
                </div>

                <div class="bg-white border border-grey-200 rounded-lg p-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-grey-900 mb-2">No US legal reach</h3>
                    <p class="text-grey-600 text-sm">We are not bound by the US CLOUD Act, FISA §702, or National Security Letters. Any lawful request must go through Swedish courts under EU data-protection standards.</p>
                </div>
            </div>

            {{-- Comparison table --}}
            <div class="mt-14 max-w-4xl mx-auto bg-white border border-grey-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-grey-200 bg-grey-50">
                    <h3 class="text-base font-semibold text-grey-900">Swedish hosting vs US-based forwarding services</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-grey-50 border-b border-grey-200">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold text-grey-700"></th>
                                <th class="text-center px-6 py-3 font-semibold text-indigo-700">{{ config('app.name', 'MailFlusher') }}</th>
                                <th class="text-center px-6 py-3 font-semibold text-grey-500">Typical US service</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grey-100">
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Server location</td>
                                <td class="text-center px-6 py-3 text-grey-900">Sweden (EU)</td>
                                <td class="text-center px-6 py-3 text-grey-500">United States</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Governing law</td>
                                <td class="text-center px-6 py-3 text-grey-900">GDPR + Swedish law</td>
                                <td class="text-center px-6 py-3 text-grey-500">US federal + state</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Subject to CLOUD Act</td>
                                <td class="text-center px-6 py-3 text-green-600 font-medium">No</td>
                                <td class="text-center px-6 py-3 text-red-500 font-medium">Yes</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Gag-order NSLs possible</td>
                                <td class="text-center px-6 py-3 text-green-600 font-medium">No</td>
                                <td class="text-center px-6 py-3 text-red-500 font-medium">Yes</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Data sold or profiled</td>
                                <td class="text-center px-6 py-3 text-green-600 font-medium">Never</td>
                                <td class="text-center px-6 py-3 text-grey-500">Varies</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 text-grey-700">Right to export / erase</td>
                                <td class="text-center px-6 py-3 text-green-600 font-medium">Guaranteed by GDPR</td>
                                <td class="text-center px-6 py-3 text-grey-500">At provider's discretion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-center text-grey-500 text-xs mt-6 max-w-2xl mx-auto">
                This comparison describes the general legal posture of Sweden-based vs US-based services. It is not legal advice and individual providers may differ. Read the full details in our <a href="/privacy" class="text-indigo-600 hover:text-indigo-500 underline">Privacy Policy</a>.
            </p>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-20 bg-grey-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-grey-900 mb-16">Frequently Asked Questions</h2>

            <div class="columns-1 md:columns-2 gap-6">
                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What is an email alias?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        An email alias is a forwarding address that redirects emails to your real inbox. You give out the alias instead of your real email. If the alias starts receiving spam, you simply deactivate it.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Can I reply to emails from an alias?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Yes. When you reply to a forwarded email, the reply is sent through the alias. The recipient will only see the alias address, not your real email.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What happens if an alias gets spam?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Simply deactivate or delete the alias. All future emails to that alias will be silently discarded or bounced. Your real email address remains unaffected.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Is my email encrypted?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        You can add your GPG/OpenPGP public key and all forwarded emails will be encrypted before delivery. Attachments are encrypted too. You can even encrypt the email subject.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Does this work with my email provider?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Yes. Email aliases work with any email provider — Gmail, Outlook, ProtonMail, Tutanota, or any other service. Emails are simply forwarded to whatever address you use.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What is catch-all?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Catch-all means that any email sent to your username domain will be automatically forwarded to you, even if the alias doesn't exist yet. For example, if your username is "mrunknown", emails to anything@mrunknown.{{ config('mailflusher.domain') }} will be received — the alias is created on the fly. Without catch-all, only pre-existing aliases will receive email. Catch-all is available on <a href="#pricing" class="text-indigo-600 hover:text-indigo-500 underline">Standard and Pro plans</a>.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        How do I hide my email address online?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Instead of giving out your real email address, create an email alias with {{ config('app.name') }} and use that instead. The alias forwards emails to your real inbox, but the sender never sees your actual email address. If the alias starts receiving spam, simply deactivate or delete it.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What's the difference between an email alias and a disposable email?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Disposable email services give you a temporary inbox that expires. Email aliases are permanent forwarding addresses that you control. You can reply from aliases, manage them from a dashboard, enable encryption, and keep them active as long as you want. Unlike disposable emails, aliases are suitable for important accounts like banking, shopping, and subscriptions.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Is email aliasing better than Gmail's plus (+) addressing?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Gmail's plus addressing (yourname+tag@gmail.com) is easy to strip — spammers simply remove the +tag to find your real address. With {{ config('app.name') }}, aliases use a completely different email address that can't be traced back to your real one. You can also deactivate individual aliases, which isn't possible with plus addressing.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        How do I know who sold my data?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Use a unique alias for every service you sign up to — for example, amazon@yourusername.{{ config('mailflusher.domain') }} for Amazon and netflix@yourusername.{{ config('mailflusher.domain') }} for Netflix. If you start receiving spam on one specific alias, you know exactly which company leaked or sold your data.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Where are your servers located?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        All our servers are located in Sweden, within the European Union. Your data is protected by EU data protection laws (GDPR) and Swedish privacy regulations, which are among the strongest in the world. Your data never leaves the EU. Read more in our <a href="/privacy" class="text-indigo-600 hover:text-indigo-500 underline">Privacy Policy</a>.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What is a burner alias?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        A burner alias is a regular {{ config('app.name', 'MailFlusher') }} alias that automatically deactivates after a time window (1 hour, 24 hours, 3/7/30 days) or a number of emails (1, 3, or 10). Great for one-time signups — a site that needs to email you once for a download link, a trial account, or a service you don't fully trust. Once the burner expires, further mail to that address is silently discarded or bounced back depending on your choice. Available on all plans; Free is capped at 2 active burners at a time, Standard at 20, Pro unlimited.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        How does {{ config('app.name', 'MailFlusher') }} detect if a company leaked my email?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        If you use a different alias for every service — <em>netflix@mrunknown</em>, <em>amazon@mrunknown</em>, and so on — each alias should only ever receive mail from that one brand. We watch each alias's sender domains and learn a baseline. When an unrelated sender starts emailing a locked-in alias (and it's not a known email service provider like SendGrid or Mailchimp, which we automatically allowlist), we flag a "suspected leak" in your dashboard. You can confirm or dismiss it with one click. No machine learning black box — just straightforward first-party observation of which brands sent what.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What does "tracker stripping" do?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Marketing emails are full of hidden tracking. Tiny 1×1 images (&ldquo;pixels&rdquo;) report back when you open the email; links are wrapped in redirectors that log every click. Enable tracker stripping in Settings and every forwarded email is cleaned before it reaches your inbox: tracking pixels are removed, and links can optionally be routed through our proxy that strips UTM, Facebook, Google, and ESP click-tracking parameters before 302-redirecting to the real destination. <em>Pixels only</em> is available on all plans. <em>Pixels and links</em> is available on Standard and Pro. Never destructive — unsubscribe links are preserved, and the stripper never blocks email delivery if something goes wrong.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        What is Ghost Inbox and how is it private?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Ghost Inbox (Pro) lets you flag an alias so incoming mail is <strong>stored instead of forwarded</strong>. The twist: your browser generates an OpenPGP keypair the first time you set up the vault, and we only ever see the public key plus a passphrase-encrypted copy of the private key. When mail arrives, the server encrypts the whole message with your public key, stores the ciphertext, and discards the plaintext. Reading happens entirely in your browser — we deliver the ciphertext, you enter the passphrase, OpenPGP.js decrypts locally. Database leaks, subpoenas for stored content, stolen backups: none of them reveal the message. Unlock sessions auto-expire after 15 minutes of inactivity (configurable). Forgot the passphrase? The mail is genuinely unrecoverable — we hand you a recovery sheet on setup. Stored emails auto-delete after 30 days.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Do you support webhooks?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Yes — on Standard and Pro plans. Add a webhook URL, pick one or more events (<code>alias.received</code>, <code>alias.blocked</code>, <code>alias.leaked</code>), and we'll POST a JSON payload there whenever one fires. Every request carries an <code>X-MailFlusher-Signature</code> HMAC-SHA256 header you can verify with the per-webhook secret we show once on creation. Failed deliveries retry with exponential backoff up to 5 attempts, and every attempt is logged in a per-webhook delivery log so you can audit and debug without us having to intervene.
                    </div>
                </details>

                <details class="group border border-grey-200 rounded-lg bg-white break-inside-avoid mb-6">
                    <summary class="flex items-center justify-between cursor-pointer p-6 text-lg font-medium text-grey-900">
                        Can I import my aliases from SimpleLogin or Addy.io?
                        <svg class="h-5 w-5 text-grey-500 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                    </summary>
                    <div class="px-6 pb-6 text-grey-600">
                        Yes. Settings &rarr; Import has a one-click wizard. Paste a SimpleLogin or Addy.io API token, hit Preview, and we fetch your aliases and tell you exactly how many will import and how many will skip (because of your plan's alias cap). Confirm and we recreate them on your MailFlusher subdomain, preserving the description and active state. Your original aliases keep working at the source service unless you deactivate them there — this is a copy, not a move. Firefox Relay has no public API, so that one needs to go through the <a href="/contact" class="text-indigo-600 hover:text-indigo-500 underline">contact form</a>; we'll import them manually, free.
                    </div>
                </details>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-grey-900 mb-4">Simple, transparent pricing</h2>
            <p class="text-center text-grey-600 mb-16 max-w-2xl mx-auto">Start for free, upgrade when you need more.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach ($plans as $key => $plan)
                    <div class="relative border {{ $key === 'standard' ? 'border-cyan-400 ring-2 ring-cyan-400' : 'border-grey-200' }} rounded-lg p-8 flex flex-col">
                        @if ($key === 'standard')
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="bg-cyan-400 text-cyan-900 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">Popular</span>
                            </div>
                        @endif

                        <h3 class="text-xl font-bold text-grey-900 mb-2">{{ $plan['name'] }}</h3>
                        <div class="mb-6">
                            <span class="text-4xl font-bold text-grey-900">{{ $plan['price'] === 0 ? 'Free' : '€' . $plan['price'] }}</span>
                            @if ($plan['price'] > 0)
                                <span class="text-grey-500 text-sm">/month</span>
                            @endif
                        </div>

                        <ul class="space-y-3 mb-8 flex-grow">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 {{ is_null($plan['aliases']) ? 'text-cyan-500' : 'text-grey-400' }} mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">{{ is_null($plan['aliases']) ? 'Unlimited' : $plan['aliases'] }} aliases</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 {{ ! array_key_exists('burner_aliases', $plan) || is_null($plan['burner_aliases']) ? 'text-cyan-500' : 'text-grey-400' }} mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">{{ ! array_key_exists('burner_aliases', $plan) || is_null($plan['burner_aliases']) ? 'Unlimited' : $plan['burner_aliases'] }} active burner aliases</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">{{ $plan['recipients'] }} {{ $plan['recipients'] === 1 ? 'recipient' : 'recipients' }}</span>
                            </li>
                            <li class="flex items-start">
                                @if ($plan['rules'] > 0)
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">{{ $plan['rules'] }} rules</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No rules</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['additional_usernames'] > 0)
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">{{ $plan['additional_usernames'] }} additional usernames</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No additional usernames</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['can_reply_send'])
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Reply and send from aliases</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No reply/send</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['can_view_failed_deliveries'])
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Failed delivery logs</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No failed delivery logs</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['can_use_blocklist'])
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Sender blocklist</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No blocklist</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['can_use_catch_all'])
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Catch-all</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No catch-all</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if ($plan['can_use_custom_domains'])
                                    <svg class="h-5 w-5 text-cyan-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Custom domains</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No custom domains</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">Leak attribution</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 {{ ! empty($plan['can_use_link_stripping']) ? 'text-cyan-500' : 'text-grey-400' }} mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">{{ ! empty($plan['can_use_link_stripping']) ? 'Pixel & link stripping' : 'Pixel stripping' }}</span>
                            </li>
                            <li class="flex items-start">
                                @if (! empty($plan['can_use_webhooks']))
                                    <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Outbound webhooks</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No webhooks</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                @if (! empty($plan['can_use_ghost_inbox']))
                                    <svg class="h-5 w-5 text-cyan-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    <span class="text-grey-700">Ghost Inbox (E2E encrypted)</span>
                                @else
                                    <svg class="h-5 w-5 text-grey-300 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    <span class="text-grey-500">No Ghost Inbox</span>
                                @endif
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">Import from SimpleLogin / Addy.io</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-grey-400 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-grey-700">{{ is_null($plan['bandwidth']) ? 'Unlimited' : round($plan['bandwidth'] / 1024 / 1024) . ' MB' }} bandwidth/month</span>
                            </li>
                        </ul>

                        @if ($plan['price'] === 0)
                            <a href="{{ $appUrl }}/{{ config('mailflusher.enable_registration') ? 'register' : 'login' }}" class="block text-center rounded-md border-2 border-grey-300 hover:border-grey-400 text-grey-700 px-6 py-3 font-bold">
                                Get started
                            </a>
                        @else
                            <a href="{{ $appUrl }}/{{ config('mailflusher.enable_registration') ? 'register' : 'login' }}" class="block text-center rounded-md {{ $key === 'standard' ? 'bg-cyan-400 hover:bg-cyan-300 text-cyan-900' : 'bg-indigo-600 hover:bg-indigo-500 text-white' }} px-6 py-3 font-bold shadow-sm">
                                Subscribe
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-indigo-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Stop giving out your real email address</h2>
            <p class="text-white text-lg mb-8">Join thousands of privacy-conscious users. Create your first anonymous email alias in seconds — no credit card required.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if (config('mailflusher.enable_registration'))
                    <a href="{{ $appUrl }}/register" class="inline-flex items-center justify-center rounded-md bg-cyan-400 hover:bg-cyan-300 text-cyan-900 px-8 py-3 font-bold shadow-lg text-lg">
                        Get started for free
                    </a>
                @endif
                <a href="{{ $appUrl }}/login" class="inline-flex items-center justify-center rounded-md border-2 border-white/60 hover:border-white text-white hover:text-white px-8 py-3 font-bold text-lg">
                    Login
                </a>
            </div>
        </div>
    </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-grey-900 pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-10 mb-12">
                {{-- Brand --}}
                <div class="md:col-span-1">
                    <div class="flex items-center mb-4">
                        <img src="/svg/icon-logo.svg" alt="{{ config('app.name', 'MailFlusher') }} logo" class="h-8 w-auto mr-3" loading="lazy" width="32" height="32">
                        <span class="text-lg font-bold text-white">{{ config('app.name', 'MailFlusher') }}</span>
                    </div>
                    <p class="text-grey-300 text-sm leading-relaxed">
                        Protect your real email address with anonymous forwarding aliases. Hosted in Sweden, EU. GDPR compliant.
                    </p>
                </div>

                {{-- Navigation --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-grey-300 hover:text-white text-sm transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-grey-300 hover:text-white text-sm transition-colors">How it works</a></li>
                        <li><a href="#pricing" class="text-grey-300 hover:text-white text-sm transition-colors">Pricing</a></li>
                        <li><a href="#faq" class="text-grey-300 hover:text-white text-sm transition-colors">FAQ</a></li>
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Account</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ $appUrl }}/login" class="text-grey-300 hover:text-white text-sm transition-colors">Login</a></li>
                        @if (config('mailflusher.enable_registration'))
                            <li><a href="{{ $appUrl }}/register" class="text-grey-300 hover:text-white text-sm transition-colors">Register</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Compare --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Compare</h4>
                    <ul class="space-y-2">
                        <li><a href="/vs/simplelogin" class="text-grey-300 hover:text-white text-sm transition-colors">vs SimpleLogin</a></li>
                        <li><a href="/vs/addy-io" class="text-grey-300 hover:text-white text-sm transition-colors">vs Addy.io</a></li>
                        <li><a href="/vs/firefox-relay" class="text-grey-300 hover:text-white text-sm transition-colors">vs Firefox Relay</a></li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="/help" class="text-grey-300 hover:text-white text-sm transition-colors">Help Centre</a></li>
                        <li><a href="/contact" class="text-grey-300 hover:text-white text-sm transition-colors">Contact Us</a></li>
                        <li><a href="/privacy" class="text-grey-300 hover:text-white text-sm transition-colors">Privacy Policy</a></li>
                        <li><a href="/terms" class="text-grey-300 hover:text-white text-sm transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-grey-800 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-grey-300 text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'MailFlusher') }}. Licensed under <a href="https://www.gnu.org/licenses/agpl-3.0.html" rel="noopener" class="underline hover:text-white">AGPL-3.0</a>. <a href="https://github.com/MailFlusher/mailflusher" rel="noopener" class="underline hover:text-white">Source</a>.</p>
                <p class="text-grey-300 text-xs">v{{ config('mailflusher.version', '1.0.0') }}</p>
            </div>
        </div>
    </footer>

    <script>
    (function() {
        var nav = document.getElementById('landing-nav');
        var pills = document.querySelectorAll('.nav-pill');
        var sections = ['how-it-works', 'features', 'pricing', 'faq'];
        var activeClass = 'bg-indigo-100 text-indigo-700';
        var inactiveClass = 'text-grey-500';

        // Add shadow on scroll
        var lastScroll = 0;
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                nav.classList.add('shadow-sm');
            } else {
                nav.classList.remove('shadow-sm');
            }

            // Scroll spy
            var current = '';
            var offset = 100;

            for (var i = 0; i < sections.length; i++) {
                var el = document.getElementById(sections[i]);
                if (el) {
                    var rect = el.getBoundingClientRect();
                    if (rect.top <= offset && rect.bottom > offset) {
                        current = sections[i];
                    }
                }
            }

            pills.forEach(function(pill) {
                var target = pill.getAttribute('data-nav');
                // Remove active styles
                activeClass.split(' ').forEach(function(c) { pill.classList.remove(c); });

                if (target === current) {
                    // Add active styles
                    activeClass.split(' ').forEach(function(c) { pill.classList.add(c); });
                    pill.classList.remove(inactiveClass);
                } else {
                    if (!pill.classList.contains(inactiveClass)) {
                        pill.classList.add(inactiveClass);
                    }
                }
            });
        }, { passive: true });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    history.pushState(null, '', this.getAttribute('href'));
                }
            });
        });
    })();
    </script>

</body>
</html>
