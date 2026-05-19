# MailFlusher

A privacy-first email aliasing and forwarding service. Live at [mailflusher.com](https://mailflusher.com), hosted in Germany.

---

<p align="center">
    <a href="https://github.com/MailFlusher/mailflusher"><img src="https://img.shields.io/github/languages/code-size/MailFlusher/mailflusher.svg" alt="Code Size" /></a>
    <a href="https://github.com/MailFlusher/mailflusher/actions/workflows/github-code-scanning/codeql"><img src="https://github.com/MailFlusher/mailflusher/actions/workflows/github-code-scanning/codeql/badge.svg" alt="CodeQL" /></a>
    <a href="https://github.com/MailFlusher/mailflusher"><img src="https://badgen.net/github/stars/MailFlusher/mailflusher" alt="GitHub stars" /></a>
    <a href="./LICENSE.md"><img src="https://img.shields.io/badge/License-AGPL_v3-blue.svg" alt="AGPL v3" /></a>
</p>

---

## Origin

MailFlusher started as a fork of [Addy.io](https://addy.io) (formerly AnonAddy) by Will Browning, licensed under AGPL-3.0. The fork has diverged substantially — see below — but core email-forwarding plumbing, custom mail driver, and DKIM/encryption pipeline are derivative work and remain under AGPL-3.0.

---

## What it is

You generate email aliases like `shopping@yourname.mailflusher.com` and hand those out instead of your real address. Mail to the alias forwards to your real inbox. You can:

- Reply or send from an alias (the recipient sees the alias, not your real address)
- Encrypt forwarded mail with GPG / OpenPGP
- Filter, route, or auto-respond using user-defined rules
- Disable or delete an alias when it starts attracting spam
- Detect which company leaked your address via per-alias leak attribution
- Read mail offline through an end-to-end encrypted **Ghost Inbox** vault when delivery fails
- Burn aliases automatically after a time window for one-shot signups

---

## Major changes from upstream Addy.io

The fork is not a thin reskin. The following are MailFlusher-specific:

### Features added

- **Ghost Inbox:** client-side OpenPGP-encrypted vault for failed-delivery messages. Server stores ciphertext; only the user's passphrase decrypts. Recovery sheet, auto-lock timer, configurable preview length.
- **Burner aliases:** auto-deactivating time-limited aliases (1h / 24h / 7d / custom).
- **Alias groups:** color-coded grouping for organising aliases by purpose.
- **Leak attribution:** per-alias detection of mail from unrelated domains, surfaced as a "probably leaked" signal.
- **Tracker stripper:** outbound forwarded mail has tracking pixels and click-tracking redirects removed before delivery.
- **Webhooks UI:** full create/test/rotate-secret/redelivery management.
- **Plan-based subscription model** (Free / Standard / Pro) with limits enforced across aliases, recipients, rules, bandwidth, and feature gates. Stripe billing via Laravel Cashier.
- **Bitwarden compatibility:** `/api/v1/` endpoints respond identically to Addy.io's so the Bitwarden password generator works against MailFlusher with the existing "addy.io" service plugin.

### Stack changes

- Upgraded to Laravel 13 / PHP 8.5 (upstream tracks Laravel 12 at time of fork).
- Replaced `mews/captcha` with Cloudflare Turnstile.
- Replaced webpack with Vite 8, lazy page loading, openpgp lazy-import.
- Restructured directory layout: controllers, notifications, requests, rules, and console commands grouped by domain rather than flat.
- Migrations consolidated to a `database/schema/{driver}-schema.sql` baseline (MySQL + SQLite).

### Removed / changed

- Domain naming (URLs, CLI commands, env vars, mail headers) is `mailflusher` instead of `anonaddy`. `X-AnonAddy-*` mail headers became `X-MailFlusher-*`.
- Default test username, test sender, and test fixtures rebranded.
- A few upstream features that were never enabled here (some legacy admin tools, OAuth client management) have been removed to slim the surface.
- Disabled-but-included upstream features: 2FA, WebAuthn, reply-from-alias — all live again as of the latest commit.

---

## Hosting / privacy stance

- Servers are physically in Germany (a single jurisdiction with strong constitutional privacy protections).
- No third-party analytics, trackers, or chatbots on the marketing site.
- Source must remain available to users per AGPL-3.0 (linked in the in-app sidebar and landing footer).

---

## Tech stack

| Layer | What |
|---|---|
| Backend | Laravel 13, PHP 8.5 |
| Frontend | Vue 3, Inertia.js v2, Tailwind CSS v3 |
| Bundler | Vite 8 |
| Database | MariaDB / MySQL (SQLite in-memory for tests) |
| Queue / Cache | Redis |
| Mail | Postfix → custom Laravel mailable pipeline |
| Auth | Sanctum API tokens, WebAuthn, TOTP 2FA, optional reverse-proxy auth |
| Billing | Laravel Cashier (Stripe) |
| Spam / DKIM | Rspamd, OpenDKIM |

---

## Hosted vs self-hosted

The hosted service at [mailflusher.com](https://mailflusher.com) is the supported way to use MailFlusher.

The repo is the running source — that's an AGPL requirement and a deliberate choice — but self-hosting is not officially supported. Standing up a working instance requires Postfix, Rspamd, DKIM keys, a verified sending domain, queue workers, Redis, MySQL, Cloudflare Turnstile keys, and Stripe billing if you want subscriptions. Upstream Addy.io documents a similar setup if you want a starting point.

---

## License

AGPL-3.0-or-later. Network-clause applies: any modified version offered as a service must publish its source.

---

## Contact

- **Web:** [https://mailflusher.com](https://mailflusher.com)
- **Contact:** [https://mailflusher.com/contact](https://mailflusher.com/contact)