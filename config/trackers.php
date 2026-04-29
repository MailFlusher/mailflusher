<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tracking pixel domains
    |--------------------------------------------------------------------------
    |
    | <img> tags whose src points at any of these domains (or subdomains) are
    | removed from forwarded HTML emails. Match uses endsWith semantics, so
    | "email.mailchimp.com" matches the entry "mailchimp.com".
    |
    */
    'pixel_domains' => [
        'mailchimp.com',
        'list-manage.com',
        'mcsv.net',
        'sendgrid.net',
        'sendgrid.com',
        'hubspotemail.net',
        'hubspot.com',
        'mailgun.org',
        'mailgun.info',
        'mandrillapp.com',
        'sparkpostmail.com',
        'klaviyo.com',
        'klaviyomail.com',
        'iterable.com',
        'braze.com',
        'customer.io',
        'convertkit-mail.com',
        'mailersend.com',
        'sendpulse.com',
        'sendinblue.com',
        'mailjet.com',
        'postmarkapp.com',
        'marketo.com',
        'mktoresp.com',
        'pardot.com',
        'eloqua.com',
        'everestengagement.com',
        'facebook.com',
        'doubleclick.net',
        'google-analytics.com',
        'googletagmanager.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking URL query parameters
    |--------------------------------------------------------------------------
    |
    | When rewriting links, these query parameters are stripped from the
    | target URL before the 302 redirect.
    |
    */
    'tracking_params' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_id',
        'utm_name',
        'utm_reader',
        'fbclid',
        'gclid',
        'gbraid',
        'wbraid',
        'dclid',
        'yclid',
        'msclkid',
        'mc_eid',
        'mc_cid',
        '_hsenc',
        '_hsmi',
        '_ke',
        'vero_id',
        'vero_conv',
        'ml_subscriber',
        'ml_subscriber_hash',
        'oly_anon_id',
        'oly_enc_id',
        'trk',
        'trk_contact',
        'trk_sid',
        'pk_campaign',
        'pk_source',
        'pk_medium',
        'piwik_campaign',
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect token TTL (days)
    |--------------------------------------------------------------------------
    */
    'redirect_token_ttl_days' => 90,
];
