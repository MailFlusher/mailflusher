<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Help Centre — {{ config('app.name', 'MailFlusher') }}</title>
    <meta name="description" content="Help Centre for {{ config('app.name', 'MailFlusher') }}. Learn how to use email aliases, custom domains, encryption, and more.">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#19216C">

    @vite('resources/css/landing.css')
</head>
<body class="bg-grey-50 antialiased text-grey-900">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-grey-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center">
                    <img src="/svg/logo.svg" alt="{{ config('app.name', 'MailFlusher') }}" class="h-8 w-auto mr-3" width="120" height="32">
                    <span class="text-xl font-bold text-grey-900">{{ config('app.name', 'MailFlusher') }}</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-grey-600 hover:text-grey-900 text-sm font-medium">Home</a>
                    <a href="/contact" class="text-grey-600 hover:text-grey-900 text-sm font-medium">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-grey-900 mb-2">Help Centre</h1>
            <p class="text-grey-600 mb-12">Everything you need to know about using {{ config('app.name', 'MailFlusher') }}.</p>

            {{-- Getting Started --}}
            <section class="mb-12" id="getting-started">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Getting Started</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I create an account?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Visit <a href="{{ config('app.url') }}/register" class="text-indigo-600 underline">the registration page</a> and choose a username. This username becomes your personal subdomain — for example, if you choose "mrunknown", your aliases will be <strong>anything@mrunknown.{{ config('mailflusher.domain') }}</strong>.</p>
                            <p class="mt-3">You'll also need to provide your real email address (where forwarded emails will be sent) and a password. After registering, verify your email address by clicking the link in the verification email.</p>
                            <p class="mt-3">You can also <a href="{{ config('app.url') }}/auth/google" class="text-indigo-600 underline">sign up with Google</a> for faster registration.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I create my first alias?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>There are two ways to create aliases:</p>
                            <p class="mt-3"><strong>1. On-the-fly (with catch-all enabled):</strong> Simply make up any email address using your subdomain and use it anywhere. For example, give out <strong>shopping@mrunknown.{{ config('mailflusher.domain') }}</strong> when signing up to an online store. The alias is created automatically when it receives its first email.</p>
                            <p class="mt-3"><strong>2. From the dashboard:</strong> Log in, go to Aliases, and click "Create Alias". You can choose a random format (random characters, random words, UUID) or enter a custom local part.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is the difference between alias types?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <ul class="space-y-3">
                                <li><strong>Standard Alias:</strong> Created using your username subdomain (e.g., hello@mrunknown.{{ config('mailflusher.domain') }}). These can be created on-the-fly when catch-all is enabled.</li>
                                <li><strong>Random Character Alias:</strong> A randomly generated string like x481n904{{ '@' . config('mailflusher.domain') }}. Cannot be linked back to your username.</li>
                                <li><strong>Random Word Alias:</strong> Two random words like circus.waltz449{{ '@' . config('mailflusher.domain') }}. Easier to remember than random characters.</li>
                                <li><strong>UUID Alias:</strong> A universally unique identifier for maximum anonymity. Cannot be linked to your account.</li>
                            </ul>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Aliases --}}
            <section class="mb-12" id="aliases">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Aliases</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I reply to a forwarded email?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>When you receive a forwarded email, the From header contains an encoded reply address like:</p>
                            <p class="mt-2 bg-grey-100 rounded p-3 font-mono text-xs break-all">alias+sender=example.com@mrunknown.{{ config('mailflusher.domain') }}</p>
                            <p class="mt-3">Simply click "Reply" in your email client — it will automatically use this encoded address. The reply is routed through {{ config('app.name') }} so the recipient only sees your alias, never your real email.</p>
                            <p class="mt-3">You can verify the reply was sent by checking the reply count on your alias in the dashboard. <em>Note: Reply/send is available on Standard and Pro plans.</em></p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I send email from an alias?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>To send an email from an alias to hello@example.com using the alias <strong>myalias@mrunknown.{{ config('mailflusher.domain') }}</strong>, compose an email to:</p>
                            <p class="mt-2 bg-grey-100 rounded p-3 font-mono text-xs break-all">myalias+hello=example.com@mrunknown.{{ config('mailflusher.domain') }}</p>
                            <p class="mt-3">Replace the <strong>@</strong> in the destination address with <strong>=</strong>. The email will appear to come from your alias. You must send from a verified recipient address on your account.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens when I deactivate an alias?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>When an alias is <strong>deactivated</strong>, all emails sent to it are silently discarded. The sender will not receive any error or bounce message — the emails simply disappear. You can reactivate the alias at any time to resume receiving emails.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens when I delete an alias?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>When an alias is <strong>deleted</strong>, emails sent to it will be rejected with an error message: "550 5.1.1 Address does not exist". The sender will be notified that the address doesn't exist.</p>
                            <p class="mt-3">Deleted aliases can be restored from the Aliases page by filtering for "Deleted only".</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is catch-all?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>With catch-all enabled, any email sent to your username domain will be forwarded to you — even if the alias doesn't exist yet. The alias is automatically created on its first email. For example, if your username is "mrunknown", emails to <strong>anything@mrunknown.{{ config('mailflusher.domain') }}</strong> will be received.</p>
                            <p class="mt-3">Without catch-all, only pre-existing aliases will receive email. Catch-all is available on Standard and Pro plans.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is a burner alias?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>A burner alias is a regular alias that automatically deactivates after a time limit or after it has received a certain number of emails. It behaves identically to a normal alias until the limit is reached, at which point it is treated as inactive — further mail is either silently discarded or bounced back to the sender, your choice.</p>
                            <p class="mt-3"><strong>To create one:</strong> open the "Create new alias" dialog, tick "Make this a burner alias", and pick an expiry preset (1 hour, 24 hours, 3/7/30 days) and/or an email-count preset (1, 3, or 10 emails). You can set both — the alias expires on whichever trigger fires first.</p>
                            <p class="mt-3"><strong>On-expiry behaviour:</strong> <em>Silently discard</em> drops future mail without notifying the sender (they think the email was delivered). <em>Bounce back to sender</em> returns a standard "does not accept mail" error so the sender knows the address is dead.</p>
                            <p class="mt-3"><strong>Plan limits:</strong> Free users can have up to 2 active burners at a time, Standard up to 20, Pro unlimited. Expired burners don't count against the limit.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How does leak attribution work?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Every time mail arrives at one of your aliases we record the sender domain. After the first sender (or after 14 days), we lock a "baseline" — the brand this alias belongs to. From then on, any email from an unrelated domain is a leak candidate.</p>
                            <p class="mt-3">Before we flag it, we check two allowlists:</p>
                            <ul class="mt-2 list-disc pl-5 space-y-1">
                                <li>Known email service providers (SendGrid, Mailchimp, Mailgun, etc.) — many legitimate brands send through these and we don't want false positives.</li>
                                <li>Same apex domain — <em>email.netflix.com</em> and <em>netflix.com</em> are obviously the same brand.</li>
                            </ul>
                            <p class="mt-3">If a new sender clears both checks, it shows up in the amber "suspected leaks" panel on your dashboard. You can <strong>Confirm</strong> (treat as a real leak — useful if you want to deactivate the alias and know who sold your data) or <strong>Dismiss</strong> (not a leak, ignore this sender from now on).</p>
                            <p class="mt-3">Attribution runs best-effort — it never delays or blocks a forwarded email.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Email Privacy --}}
            <section class="mb-12" id="email-privacy">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Email Privacy</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is tracker stripping?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Most marketing emails contain two kinds of tracking:</p>
                            <ul class="mt-2 list-disc pl-5 space-y-1">
                                <li><strong>Tracking pixels</strong> — tiny 1×1 images that load from the sender's server when you open the email. They report back when, where, and how many times you looked.</li>
                                <li><strong>Tracked links</strong> — links that go through a redirector (like <code class="bg-grey-100 rounded px-1">email.mailchimp.com/click?...</code>) that logs every click before forwarding you to the real destination.</li>
                            </ul>
                            <p class="mt-3">Enable tracker stripping in <strong>Settings → General → Email Tracker Stripping</strong> and we'll clean every forwarded email before it reaches your inbox.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What's the difference between "Pixels only" and "Pixels and links"?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p><strong>Pixels only</strong> is the conservative mode and available on <strong>all plans</strong>. We remove 1×1 tracking images and any image hosted on a known tracker domain (Mailchimp, HubSpot, SendGrid, Klaviyo, Braze, Meta, Google Analytics, and others). Link tracking is not touched.</p>
                            <p class="mt-3"><strong>Pixels and links</strong> is available on <strong>Standard and Pro</strong>. It does the above, plus rewrites every link in the email to go through <code class="bg-grey-100 rounded px-1">{{ config('app.url') }}/r/&lt;token&gt;</code>. When you click, we strip UTM, Facebook click id, Google click id, HubSpot, Mailchimp and similar tracking parameters, then redirect you to the clean destination. This breaks whatever analytics the sender was relying on.</p>
                            <p class="mt-3">Some poorly-written emails can look broken with link rewriting enabled (anchors with visible raw URLs, for example). If you see an email that looks wrong, switch back to Pixels only.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Will tracker stripping break my unsubscribe links?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>No. Unsubscribe links that come through standard <code class="bg-grey-100 rounded px-1">List-Unsubscribe</code> headers are preserved and routed to your email client's native unsubscribe button. In-body unsubscribe anchors are rewritten through the proxy the same as any other link, but they still work — we only strip tracking parameters, we never change the destination.</p>
                            <p class="mt-3">If anything does look off, tracker stripping is fully reversible — toggle it off in Settings and future emails will be delivered untouched.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens if tracker stripping fails on an email?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>The email is forwarded as-is. The stripper is wrapped in a try/catch — if anything at all goes wrong (malformed HTML, weird character encoding, unknown edge case), we log the problem and let the original email through unchanged. Tracker stripping never blocks or delays delivery.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Recipients --}}
            <section class="mb-12" id="recipients">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Recipients</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is a recipient?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Recipients are your real email addresses where forwarded mail is delivered. Your default recipient is the email address you registered with. Depending on your plan, you can add additional recipients and assign different ones to different aliases.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I add GPG/OpenPGP encryption?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Go to Recipients, click on a recipient to edit it, and add your <strong>public</strong> GPG/OpenPGP key. Once added, all emails forwarded to that recipient will be encrypted before delivery — including attachments.</p>
                            <p class="mt-3">You can also enable <strong>protected headers</strong> to encrypt the email subject line. This provides maximum privacy as even we cannot read the content of your forwarded emails.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Custom Domains --}}
            <section class="mb-12" id="domains">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Custom Domains</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I add a custom domain?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Custom domains are available on the Pro plan. To add one:</p>
                            <ol class="list-decimal list-inside mt-3 space-y-2">
                                <li>Go to Domains and click "Add Domain"</li>
                                <li>Enter your domain name (e.g., example.com)</li>
                                <li>Add a <strong>TXT record</strong> to your DNS to verify ownership</li>
                                <li>Add an <strong>MX record</strong> pointing to <strong>{{ config('mailflusher.hostname') }}</strong></li>
                                <li>Optionally add <strong>SPF</strong>, <strong>DKIM</strong>, and <strong>DMARC</strong> records to enable sending from your domain</li>
                            </ol>
                            <p class="mt-3">Allow time for DNS propagation. You can use a subdomain (e.g., mail.example.com) if you're already using the apex domain for email elsewhere.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Can I use a domain I'm already using for email?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>If your domain is already used for email (e.g., with Gmail, ProtonMail, or another provider), you cannot also use the same domain with {{ config('app.name') }} — email can only be handled by one mail server at a time.</p>
                            <p class="mt-3">Instead, use a <strong>subdomain</strong> like <strong>mail.example.com</strong>. This won't interfere with your existing email setup, and you'll be able to create aliases like anything@mail.example.com.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Account & Security --}}
            <section class="mb-12" id="account">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Account & Security</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is bandwidth and how is it calculated?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Bandwidth is the total size of emails processed through your account each month. It is incremented each time an email is forwarded or a reply/send is made. Blocked emails (deactivated or deleted aliases) do not count towards bandwidth.</p>
                            <p class="mt-3">Bandwidth resets at the start of each month. Limits by plan: Free (10 MB), Standard (200 MB), Pro (unlimited). You'll receive a notification when you approach your limit.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens when I delete my account?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>When you delete your account:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>All recipients are permanently deleted</li>
                                <li>All aliases on custom domains are permanently deleted</li>
                                <li>Aliases on shared domains are anonymized and soft-deleted to prevent reuse</li>
                                <li>All custom domains, rules, and API keys are deleted</li>
                                <li>Your username is encrypted and stored to prevent re-registration</li>
                                <li>All other account data is permanently removed</li>
                            </ul>
                            <p class="mt-3">This action cannot be undone. You can delete your account from Settings > Delete Account.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Do you store my emails?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p><strong>No.</strong> Emails are processed in memory and forwarded immediately to your recipient address. We do not store the content of any emails.</p>
                            <p class="mt-3">The only exception is if you enable "Store Failed Deliveries" in Settings — in that case, failed emails may be temporarily stored so you can retry delivery. This feature is available on Standard and Pro plans.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Ghost Inbox --}}
            <section class="mb-12" id="ghost-inbox">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Ghost Inbox <span class="ml-2 text-xs font-medium text-indigo-600">Pro</span></h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What is Ghost Inbox?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Ghost Inbox is a Pro-only feature that lets you flag an alias as "ghost mode" — incoming mail is stored in an encrypted browser-only inbox rather than forwarded to your real address. Useful for one-time codes, trial signups, or any mail you want to read but don't want cluttering your real inbox.</p>
                            <p class="mt-3">The critical property: stored messages are encrypted with an OpenPGP key pair that only your browser can unlock. Even we cannot read the content.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How is the encryption actually set up?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <ol class="list-decimal pl-5 space-y-1">
                                <li>On first setup, your browser generates a Curve25519 OpenPGP keypair via OpenPGP.js.</li>
                                <li>You choose a vault passphrase — it never leaves your browser.</li>
                                <li>The private key is encrypted with that passphrase using the standard OpenPGP password-protected format, and only the ciphertext is uploaded.</li>
                                <li>The public key is uploaded in plain form so the server can encrypt incoming mail.</li>
                            </ol>
                            <p class="mt-3">When mail arrives at a ghost-mode alias, the server encrypts the raw MIME with your public key and stores the ciphertext. Plaintext never hits disk. You read by entering your passphrase in the Ghost Inbox page; decryption runs locally.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens if I forget my passphrase?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Your stored emails are unrecoverable. We don't have the passphrase and we can't regenerate the private key — that's the point. You'll have to destroy the vault (which deletes all stored emails) and set up a new one.</p>
                            <p class="mt-3">To avoid this, save the recovery sheet we offer you when the vault is created. It contains the armored encrypted private key that can be decrypted with any OpenPGP tool (Thunderbird, GnuPG CLI, etc.) using your passphrase — useful if our site is ever unavailable.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How long are stored emails kept?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>30 days by default. A scheduled job deletes anything older automatically. You can also delete individual emails or destroy the whole vault at any time.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Can I still see sender and subject in the inbox list?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Yes, but only the first 10 characters of each, and only if you opt in. Settings → Ghost Inbox lets you pick:</p>
                            <ul class="mt-2 list-disc pl-5 space-y-1">
                                <li><strong>Show first 10 chars of From and Subject</strong> (default) — the inbox list is readable without unlocking.</li>
                                <li><strong>Encrypt everything</strong> — even previews are skipped; list shows only timestamps and sizes.</li>
                            </ul>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What's the honest threat model?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p><strong>Ghost Inbox defends against:</strong> database leaks, stolen backups, subpoena of stored content (we hand over ciphertext, not plaintext), compromised DBA credentials.</p>
                            <p class="mt-3"><strong>It does NOT defend against:</strong> an attacker who actively compromises our application server and pushes malicious JavaScript that captures your passphrase at unlock time. This is a fundamental limit of any browser-delivered end-to-end crypto — the same limit applies to Proton Mail, Tutanota, and every other "web E2E" system. For absolute guarantees, use an external OpenPGP tool with the public key we store for you.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Webhooks --}}
            <section class="mb-12" id="webhooks">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Webhooks <span class="ml-2 text-xs font-medium text-indigo-600">Standard &amp; Pro</span></h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What events can I subscribe to?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <ul class="list-disc pl-5 space-y-2">
                                <li><code>alias.received</code> — fires after an email is forwarded. Payload: alias id + email, from header, subject, size_bytes.</li>
                                <li><code>alias.blocked</code> — fires when a user rule blocks a forward. Same shape as received.</li>
                                <li><code>alias.leaked</code> — fires when leak attribution creates a new suspected-leak event. Payload: alias id + email, the unexpected sender_domain, the baseline sender we learned, and detected_at.</li>
                            </ul>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I verify the signature?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Every delivery carries an <code>X-MailFlusher-Signature</code> header of the form <code>sha256=&lt;hex-hmac&gt;</code>. Compute <code>hmac_sha256(secret, raw_request_body)</code> on your end with the per-webhook secret we showed you once on creation, and compare with constant-time equality. Reject anything that doesn't match.</p>
                            <p class="mt-3">Other headers: <code>X-MailFlusher-Event</code> (the event name), <code>X-MailFlusher-Delivery-Id</code> (unique id you can use for idempotency).</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens if my endpoint is down?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>We retry with exponential backoff: 1 min, 5 min, 30 min, 2 h, 12 h. After 5 total attempts the delivery is marked <code>giving_up</code> and we stop. Every attempt — successful or not — is visible in the per-webhook delivery log with the response code, response body (truncated), and timestamp. No silent failures.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Are there URL restrictions?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Yes. URLs must be HTTPS. Loopback (127.0.0.1, localhost) and link-local (169.254.*) addresses are rejected. This is a basic SSRF protection — the webhook would be running in our workers otherwise.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Import --}}
            <section class="mb-12" id="import">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Importing from other services</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I import from SimpleLogin or Addy.io?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <ol class="list-decimal pl-5 space-y-1">
                                <li>Generate an API token on the source service (SimpleLogin: Settings → API Keys; Addy.io: Settings → API).</li>
                                <li>Open Settings → Import in {{ config('app.name', 'MailFlusher') }}.</li>
                                <li>Pick the source, paste the token, click <strong>Preview import</strong>.</li>
                                <li>We'll show you the total count and how many will fit your plan's alias cap. If everything looks good, click <strong>Import N aliases</strong>.</li>
                            </ol>
                            <p class="mt-3">Descriptions and active/paused states are preserved. The new aliases live on your MailFlusher username subdomain — the email addresses themselves change, because we can't take over domains we don't own.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens to my aliases at the source service?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Nothing. This is a copy, not a move. Your SimpleLogin / Addy.io account is untouched; the originals keep forwarding mail there unless you deactivate them yourself. Update the signup services to the new {{ config('app.name', 'MailFlusher') }} aliases at your own pace, then deactivate the originals.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Do you support Firefox Relay import?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Not automatically — Firefox Relay has no public user-facing API we can call. Export your aliases from Relay's settings, then reach out via the <a href="/contact" class="text-indigo-600 underline">contact form</a> with the file and we'll import them manually. Free, one-off.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Integrations --}}
            <section class="mb-12" id="integrations">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Password manager integrations</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Can I use {{ config('app.name', 'MailFlusher') }} with Bitwarden?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Yes — verified working. {{ config('app.name', 'MailFlusher') }}'s API responds to Bitwarden's "addy.io" forwarded-alias requests the same way Addy.io does, so you can create aliases from Bitwarden's password generator without leaving the app.</p>
                            <p class="mt-3"><strong>Setup:</strong></p>
                            <ol class="mt-2 list-decimal pl-5 space-y-1">
                                <li>Log in to {{ config('app.name', 'MailFlusher') }} and go to <strong>Settings → API</strong>.</li>
                                <li>Click "Create new token", give it a name like "Bitwarden", and copy the token.</li>
                                <li>In Bitwarden, open the password generator and select <strong>Username → Forwarded email alias</strong>.</li>
                                <li>Set the service to <strong>addy.io</strong> (or AnonAddy, depending on your Bitwarden version).</li>
                                <li>Paste your API token into the API Key field.</li>
                                <li>In the Domain field, enter: <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">&lt;your-username&gt;.{{ config('mailflusher.domain') }}</code> — e.g. if your username is <em>mrunknown</em>, use <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">mrunknown.{{ config('mailflusher.domain') }}</code>.</li>
                                <li>In the Server URL / Self-host field, enter: <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">{{ config('app.url') }}</code></li>
                            </ol>
                            <p class="mt-3">Bitwarden will now generate aliases on {{ config('app.name', 'MailFlusher') }} whenever you use its email generator. If Bitwarden returns a validation error on the Domain field, check that the domain exactly matches one of the options in your <strong>Aliases → New Alias</strong> domain dropdown in {{ config('app.name', 'MailFlusher') }}.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Can I use {{ config('app.name', 'MailFlusher') }} with 1Password?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>1Password doesn't currently ship a native Addy.io / {{ config('app.name', 'MailFlusher') }} integration out-of-the-box. Two workarounds work well:</p>
                            <p class="mt-3"><strong>Option A — pre-create aliases in {{ config('app.name', 'MailFlusher') }}, save them in 1Password:</strong> create an alias in the dashboard, copy it, and paste it into 1Password's username field when saving a new login.</p>
                            <p class="mt-3"><strong>Option B — use the API from a shortcut:</strong> on macOS or iOS, create a Shortcut that calls <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">POST {{ config('app.url') }}/api/v1/aliases</code> with your API token, then pipe the result into 1Password. Advanced but reliable.</p>
                            <p class="mt-3"><em>If you'd like a first-class integration, please <a href="https://1password.community" class="text-indigo-600 underline">request it in the 1Password community forum</a> — provider support is decided by 1Password, not us.</em></p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I create an API token?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Go to <strong>Settings → API</strong>, click "Create new token", give it a memorable name, and copy the token. Treat it like a password — anyone with the token can create or delete aliases on your account. You can revoke it at any time from the same page.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            Does the API work with Addy.io client apps?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>{{ config('app.name', 'MailFlusher') }}'s API was originally derived from Addy.io and still exposes many of the same <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">/api/v1/</code> endpoints. Most third-party Addy.io clients (browser extensions, mobile apps, CLI tools) will work if you point them at <code class="bg-grey-100 rounded px-1.5 py-0.5 text-xs">{{ config('app.url') }}</code> as the custom server URL.</p>
                            <p class="mt-3">If a client hardcodes the addy.io domain and won't accept a custom host, please let us know via the <a href="/contact" class="text-indigo-600 underline">contact form</a> — we're tracking compatibility and working on official {{ config('app.name', 'MailFlusher') }} browser extensions and a mobile app.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Subscriptions --}}
            <section class="mb-12" id="subscriptions">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Subscriptions & Billing</h2>
                <div class="space-y-4">
                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            How do I upgrade my plan?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>Go to Settings > Subscription and click "Upgrade" on the plan you'd like. You'll be redirected to Stripe's secure checkout to enter your payment details. Your new plan activates immediately after payment.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What happens if I cancel my subscription?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>When you cancel, your subscription remains active until the end of the current billing period. After that, your account reverts to the Free plan. You can resume your subscription before the billing period ends to keep your current plan.</p>
                            <p class="mt-3">After downgrading to Free, features beyond the Free plan limits (extra aliases, recipients, rules, etc.) will become inaccessible but are not deleted.</p>
                        </div>
                    </details>

                    <details class="group bg-white border border-grey-200 rounded-lg">
                        <summary class="flex items-center justify-between cursor-pointer p-5 text-base font-medium text-grey-900">
                            What payment methods do you accept?
                            <svg class="h-5 w-5 text-grey-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                        </summary>
                        <div class="px-5 pb-5 text-grey-600 text-sm leading-relaxed">
                            <p>We use Stripe for payment processing. Stripe accepts all major credit and debit cards (Visa, Mastercard, American Express), as well as regional payment methods depending on your country. Your payment details are handled entirely by Stripe — we never see or store your card information.</p>
                        </div>
                    </details>
                </div>
            </section>

            {{-- Terminology --}}
            <section class="mb-12" id="terminology">
                <h2 class="text-xl font-semibold text-grey-900 mb-6 pb-2 border-b border-grey-200">Terminology</h2>
                <div class="bg-white border border-grey-200 rounded-lg divide-y divide-grey-100">
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">Alias</dt>
                        <dd class="mt-1 text-sm text-grey-600">An email address that forwards to your real email. You give out aliases instead of your real address.</dd>
                    </div>
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">Recipient</dt>
                        <dd class="mt-1 text-sm text-grey-600">Your real email address where forwarded mail is delivered (e.g., your Gmail, Outlook, or ProtonMail address).</dd>
                    </div>
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">Catch-all</dt>
                        <dd class="mt-1 text-sm text-grey-600">A setting that automatically accepts and forwards emails sent to any address on your domain, even if no alias exists yet.</dd>
                    </div>
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">Bandwidth</dt>
                        <dd class="mt-1 text-sm text-grey-600">The total size of emails processed through your account, measured in megabytes per month.</dd>
                    </div>
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">GPG/OpenPGP Key</dt>
                        <dd class="mt-1 text-sm text-grey-600">An encryption standard used to encrypt forwarded emails so only you can read them.</dd>
                    </div>
                    <div class="p-5">
                        <dt class="font-medium text-grey-900">Fingerprint</dt>
                        <dd class="mt-1 text-sm text-grey-600">A shorter representation of your GPG public key, used to verify the correct key is being used for encryption.</dd>
                    </div>
                </div>
            </section>

            {{-- Still need help --}}
            <div class="text-center bg-white border border-grey-200 rounded-lg p-8">
                <h3 class="text-lg font-semibold text-grey-900 mb-2">Still need help?</h3>
                <p class="text-grey-600 text-sm mb-4">Can't find what you're looking for? Get in touch and we'll help you out.</p>
                <a href="/contact" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-2.5 text-sm font-medium">
                    Contact Support
                </a>
            </div>
        </div>
    </div>

</body>
</html>
