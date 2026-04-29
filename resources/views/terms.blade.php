<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Terms of Service — {{ config('app.name', 'MailFlusher') }}</title>
    <meta name="description" content="Terms of Service for {{ config('app.name', 'MailFlusher') }} anonymous email forwarding service.">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#19216C">

    @vite('resources/css/landing.css')
</head>
<body class="bg-grey-50 antialiased text-grey-900">

    <nav class="bg-white border-b border-grey-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center">
                    <img src="/svg/logo.svg" alt="{{ config('app.name', 'MailFlusher') }} logo" class="h-8 w-auto mr-3">
                    <span class="text-xl font-bold text-grey-900">{{ config('app.name', 'MailFlusher') }}</span>
                </a>
                <a href="/" class="text-grey-600 hover:text-grey-900 text-sm font-medium">Back to home</a>
            </div>
        </div>
    </nav>

    <div class="py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-grey-900 mb-2">Terms of Service</h1>
            <p class="text-sm text-grey-500 mb-12">Last updated: {{ date('F j, Y') }}</p>

            <div class="prose prose-grey max-w-none space-y-8">

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">1. Acceptance of Terms</h2>
                    <p class="text-grey-700 leading-relaxed">By accessing or using {{ config('app.name', 'MailFlusher') }} ("the Service"), you agree to be bound by these Terms of Service. If you do not agree, you may not use the Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">2. Description of Service</h2>
                    <p class="text-grey-700 leading-relaxed">{{ config('app.name', 'MailFlusher') }} provides anonymous email forwarding through email aliases. The Service allows you to create email aliases that forward messages to your real email address, reply from aliases, and manage your email privacy.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">3. Acceptable Use</h2>
                    <p class="text-grey-700 leading-relaxed">You agree not to use the Service to:</p>
                    <ul class="list-disc list-inside text-grey-700 space-y-1 mt-2">
                        <li>Send spam, phishing emails, or any unsolicited bulk messages</li>
                        <li>Create multiple free accounts to circumvent plan limits</li>
                        <li>Use aliases to create large numbers of accounts on other websites or services</li>
                        <li>Engage in any illegal activity or violate any applicable laws</li>
                        <li>Distribute malware, viruses, or other harmful content</li>
                        <li>Impersonate others or misrepresent your identity for fraudulent purposes</li>
                        <li>Interfere with or disrupt the Service or its infrastructure</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">4. Account Responsibilities</h2>
                    <p class="text-grey-700 leading-relaxed">You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must notify us immediately of any unauthorized use.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">5. Service Availability</h2>
                    <p class="text-grey-700 leading-relaxed">We strive to maintain high availability but do not guarantee uninterrupted access to the Service. We may perform maintenance, updates, or experience outages. We are not liable for any damages resulting from service interruptions.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">6. Plan Limits and Billing</h2>
                    <p class="text-grey-700 leading-relaxed">Free and paid plans are subject to the limits described on our <a href="/#pricing" class="text-indigo-600 hover:text-indigo-500 underline">pricing page</a>. Exceeding limits may result in temporary restrictions on your account. Paid subscriptions are billed according to the selected plan and can be cancelled at any time.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">7. Spam Policy</h2>
                    <p class="text-grey-700 leading-relaxed">You must not mark emails forwarded by {{ config('app.name', 'MailFlusher') }} as spam in your email provider, as this damages the reputation of our mail servers. If an alias receives unwanted email, deactivate or delete the alias instead. Repeated spam marking may result in account suspension.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">8. Account Termination</h2>
                    <p class="text-grey-700 leading-relaxed">We reserve the right to suspend or terminate accounts that violate these Terms. You may delete your account at any time from the account settings page. Upon deletion, all your data will be permanently removed as described in our <a href="/privacy" class="text-indigo-600 hover:text-indigo-500 underline">Privacy Policy</a>.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">9. Limitation of Liability</h2>
                    <p class="text-grey-700 leading-relaxed">The Service is provided "as is" without warranty of any kind. We are not liable for any indirect, incidental, or consequential damages arising from your use of the Service, including but not limited to loss of data, missed emails, or service interruptions.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">10. Changes to Terms</h2>
                    <p class="text-grey-700 leading-relaxed">We may update these Terms from time to time. Continued use of the Service after changes constitutes acceptance of the updated Terms. Significant changes will be communicated via email or through the Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">11. Governing Law</h2>
                    <p class="text-grey-700 leading-relaxed">These Terms are governed by the laws of Sweden. Any disputes shall be resolved in the courts of Sweden.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-grey-900 mb-3">12. Contact</h2>
                    <p class="text-grey-700 leading-relaxed">For questions about these Terms, please <a href="/contact" class="text-indigo-600 hover:text-indigo-500 underline">contact us</a>.</p>
                </section>

            </div>
        </div>
    </div>

</body>
</html>
