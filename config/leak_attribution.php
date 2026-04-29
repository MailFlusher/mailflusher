<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Baseline lock window
    |--------------------------------------------------------------------------
    |
    | After either this many distinct sender domains OR this many days have
    | elapsed since the alias's first email, the baseline_sender_domain is
    | locked. Any sender domain that arrives after lock is a leak candidate.
    |
    */
    'baseline_lock_after_senders' => 3,
    'baseline_lock_after_days' => 14,

    /*
    |--------------------------------------------------------------------------
    | ESP allowlist
    |--------------------------------------------------------------------------
    |
    | Emails from these sending-domains are likely transactional/marketing
    | pass-throughs, not evidence of a leak. They never trigger leak events.
    | Match is "endsWith" — e.g. "list-manage.com" matches any subdomain.
    |
    */
    'esp_domains' => [
        'sendgrid.net',
        'sendgrid.com',
        'mailgun.org',
        'mailgun.info',
        'mandrillapp.com',
        'sparkpostmail.com',
        'list-manage.com',       // Mailchimp
        'mcsv.net',              // Mailchimp
        'rsgsv.net',             // Mailchimp
        'customeriomail.com',
        'amazonses.com',
        'hubspotemail.net',
        'klaviyomail.com',
        'sendinblue.com',
        'postmarkapp.com',
        'mailersend.net',
        'braze.com',
        'convertkit-mail.com',
        'icloud.com',
        'googlemail.com',
        'gmail.com',
    ],
];
