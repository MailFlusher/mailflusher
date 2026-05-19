<?php

return [
    'only' => [
        'dashboard.*',
        'aliases.*',
        'recipients.*',
        'domains.*',
        'usernames.*',
        'domains.*',
        'blocklist.*',
        'failed_deliveries.*',
        'rules.*',
        'settings.*',
        'ghost_inbox.*',
        'alias_groups.*',
        'webauthn.create',
        'verification.notice',
        'verification.resend',
        'logout',
        'account.destroy',
        'admin.*',
        'subscription.*',
        'promo.*',
    ],
];
