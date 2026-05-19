<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Privacy Policy — {{ config('app.name', 'MailFlusher') }}</title>
    <meta name="description" content="Privacy Policy for {{ config('app.name', 'MailFlusher') }}. Learn how we handle your data. Hosted in Germany, EU. GDPR compliant.">

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
                    <img src="/svg/logo.svg" alt="{{ config('app.name', 'MailFlusher') }}" class="h-8 w-auto mr-3">
                    <span class="text-xl font-bold text-grey-900">{{ config('app.name', 'MailFlusher') }}</span>
                </a>
                <a href="/" class="text-grey-600 hover:text-grey-900 text-sm font-medium">Back to home</a>
            </div>
        </div>
    </nav>

    <div class="py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-grey-900 mb-2">Privacy Policy</h1>
            <p class="text-sm text-grey-500 mb-12">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose prose-grey max-w-none space-y-8">

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">1. Introduction</h2>
                    <p class="text-grey-700 leading-relaxed">
                        {{ config('app.name', 'MailFlusher') }} ("we", "our", "us") is an email forwarding service operated from Germany, within the European Union. We are committed to protecting your privacy and handling your personal data in accordance with the EU General Data Protection Regulation (GDPR).
                    </p>
                    <p class="text-grey-700 leading-relaxed mt-3">
                        This Privacy Policy explains what data we collect, why we collect it, how we use it, and your rights regarding your personal data.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">2. Data We Collect</h2>

                    <h3 class="text-lg font-medium text-grey-800 mt-4 mb-2">Account Information</h3>
                    <ul class="list-disc list-inside text-grey-700 space-y-1">
                        <li>Username (chosen by you)</li>
                        <li>Email address (your real email, used as the forwarding destination)</li>
                        <li>Password (stored as a one-way hash, never in plain text)</li>
                        <li>Google account ID (if you sign in with Google)</li>
                    </ul>

                    <h3 class="text-lg font-medium text-grey-800 mt-4 mb-2">Email Data</h3>
                    <ul class="list-disc list-inside text-grey-700 space-y-1">
                        <li>Email aliases you create</li>
                        <li>Aggregate statistics (number of emails forwarded, blocked, replied, sent)</li>
                        <li>Monthly bandwidth usage</li>
                    </ul>
                    <p class="text-grey-700 leading-relaxed mt-2">
                        <strong>We do not store the content of forwarded emails.</strong> Emails are processed in memory and forwarded immediately to your recipient address. The only exception is if you have enabled the "Store Failed Deliveries" option, in which case failed emails may be temporarily stored so you can retry delivery.
                    </p>

                    <h3 class="text-lg font-medium text-grey-800 mt-4 mb-2">Encryption Keys</h3>
                    <p class="text-grey-700 leading-relaxed">
                        If you choose to add a GPG/OpenPGP public key, it is stored so we can encrypt forwarded emails before delivery. We never have access to your private key.
                    </p>

                    <h3 class="text-lg font-medium text-grey-800 mt-4 mb-2">Ghost Inbox (Pro feature)</h3>
                    <p class="text-grey-700 leading-relaxed">
                        If you enable Ghost Inbox and mark an alias as "ghost mode", incoming mail to that alias is stored rather than forwarded. To make this genuinely private we designed the feature so that {{ config('app.name', 'MailFlusher') }} staff and server operators <strong>cannot read the stored content</strong>, even with full database access:
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li>Your browser generates an OpenPGP keypair the first time you set up your vault. The passphrase that protects the private key never leaves your browser.</li>
                        <li>We store the <strong>public key</strong> (used to encrypt incoming mail for you) and the <strong>passphrase-encrypted private key</strong> (a ciphertext blob that only your passphrase can unlock).</li>
                        <li>When mail arrives at a ghost-mode alias, the server immediately encrypts the entire MIME message with your public key, stores the resulting ciphertext, and discards the plaintext. No copy in logs, no copy in backups beyond the encrypted form.</li>
                        <li>Reading requires unlocking the private key in your browser with your passphrase. Decryption happens locally; plaintext is only ever in your browser's memory.</li>
                        <li>Depending on your preview-mode setting, we may additionally store the first 10 characters of the From and Subject headers in plain text so the inbox list is usable without unlocking. You can disable this by switching to "Encrypt everything" in Settings → Ghost Inbox.</li>
                        <li>Stored emails are automatically deleted after 30 days.</li>
                        <li>If you forget your passphrase, stored emails are permanently unreadable. We cannot recover them. You are offered a recovery sheet to download at setup time.</li>
                        <li>Rotating or destroying your vault deletes all previously-stored emails, because they would be unreadable under a new key anyway.</li>
                    </ul>
                    <p class="text-grey-700 leading-relaxed mt-3">
                        <strong>Threat-model note:</strong> a browser-delivered web app can never offer the same guarantees as a native cryptographic tool, because we deliver the JavaScript that performs the decryption. An attacker with control of our application server could theoretically push JavaScript that captures your passphrase. Ghost Inbox protects against passive database compromise, server-side subpoena of stored content, and stolen backups — not an active application-layer attack. If you need stronger guarantees, use an external OpenPGP client with the public key we store for you.
                    </p>

                    <h3 class="text-lg font-medium text-grey-800 mt-4 mb-2">Server Logs</h3>
                    <p class="text-grey-700 leading-relaxed">
                        Standard server access logs (IP address, timestamp, request URL) are kept for security and debugging purposes and are automatically rotated and deleted.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">3. How We Use Your Data</h2>
                    <p class="text-grey-700 leading-relaxed">We use your data solely to:</p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li>Provide the email forwarding service</li>
                        <li>Authenticate you when you log in</li>
                        <li>Send you service-related notifications (e.g. email verification, bandwidth warnings)</li>
                        <li>Prevent abuse of the service (rate limiting, spam filtering)</li>
                    </ul>
                    <p class="text-grey-700 leading-relaxed mt-3">
                        <strong>We do not:</strong>
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-1">
                        <li>Sell your data to third parties</li>
                        <li>Use your data for advertising</li>
                        <li>Track you across websites</li>
                        <li>Use analytics or tracking scripts on our website</li>
                        <li>Share your data with third parties except as required by law</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">4. Data Storage & Security</h2>
                    <p class="text-grey-700 leading-relaxed">
                        All data is stored on servers located in <strong>Germany, European Union</strong>. Your data never leaves the EU.
                    </p>
                    <p class="text-grey-700 leading-relaxed mt-3">
                        We use industry-standard security measures including:
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li>TLS encryption for all connections</li>
                        <li>Encrypted database fields for sensitive data</li>
                        <li>DKIM, SPF, and DMARC for email authentication</li>
                        <li>Bcrypt password hashing</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">5. Third-Party Services</h2>
                    <p class="text-grey-700 leading-relaxed">
                        We use the following third-party services:
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li><strong>Cloudflare</strong> — DNS and DDoS protection. Cloudflare may process your IP address. See <a href="https://www.cloudflare.com/privacypolicy/" class="text-indigo-600 hover:text-indigo-500 underline" target="_blank" rel="noopener">Cloudflare's Privacy Policy</a>.</li>
                        <li><strong>Google OAuth</strong> — If you choose to sign in with Google, Google processes your authentication. See <a href="https://policies.google.com/privacy" class="text-indigo-600 hover:text-indigo-500 underline" target="_blank" rel="noopener">Google's Privacy Policy</a>.</li>
                    </ul>
                    <p class="text-grey-700 leading-relaxed mt-3">
                        We do not use any analytics, advertising, or tracking services.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">6. Your Rights (GDPR)</h2>
                    <p class="text-grey-700 leading-relaxed">
                        Under the GDPR, you have the following rights:
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li><strong>Right of access</strong> — You can view all your data in your account settings</li>
                        <li><strong>Right to rectification</strong> — You can update your email address and account details at any time</li>
                        <li><strong>Right to erasure</strong> — You can delete your account and all associated data from the account settings page</li>
                        <li><strong>Right to data portability</strong> — You can export your aliases from the account data settings page</li>
                        <li><strong>Right to restrict processing</strong> — You can deactivate aliases to stop forwarding</li>
                        <li><strong>Right to object</strong> — Contact us if you wish to object to any data processing</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">7. Data Retention</h2>
                    <p class="text-grey-700 leading-relaxed">
                        We retain your data for as long as your account is active. When you delete your account:
                    </p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li>All recipients are permanently deleted</li>
                        <li>All aliases on custom domains are permanently deleted</li>
                        <li>Aliases on shared domains are anonymized and soft-deleted to prevent reuse</li>
                        <li>Your username is encrypted and stored to prevent re-registration</li>
                        <li>All other account data is permanently deleted</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">8. Cookies</h2>
                    <p class="text-grey-700 leading-relaxed">
                        We use only essential cookies required for the service to function (session cookies for authentication). We do not use any tracking cookies, advertising cookies, or third-party cookies.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">9. Changes to This Policy</h2>
                    <p class="text-grey-700 leading-relaxed">
                        We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated revision date.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">10. Contact</h2>
                    <p class="text-grey-700 leading-relaxed">
                        If you have any questions about this Privacy Policy or your personal data, please contact us at <a href="/contact" class="text-indigo-600 hover:text-indigo-500 underline">our contact page</a>.
                    </p>
                </section>

            </div>
        </div>
    </div>

</body>
</html>
