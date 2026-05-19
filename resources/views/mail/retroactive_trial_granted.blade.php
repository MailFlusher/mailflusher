@component('mail::message')

# You have {{ $durationDays }} days of {{ $proName }} — free

We've activated a one-time **{{ $durationDays }}-day {{ $proName }} trial** on your account, no card required. Everything that's gated by paid tiers — unlimited aliases, reply/send, custom domains, blocklist, Ghost Inbox — is now available to you.

**Your trial ends on {{ $trialEndsAt->format('jS F Y') }}.**

## Your current usage

- **Aliases:** {{ $activeAliases }} active (Free limit: {{ $freeAliasLimit }})
- **Recipients:** {{ $activeRecipients }} (Free limit: {{ $freeRecipientLimit }})
- **Rules:** {{ $activeRules }} (Free limit: {{ $freeRuleLimit }})
@if ($activeDomains > 0)
- **Custom domains:** {{ $activeDomains }}
@endif

@php
    $excessAliases = max(0, $activeAliases - ($freeAliasLimit ?? PHP_INT_MAX));
    $excessRules = max(0, $activeRules - ($freeRuleLimit ?? PHP_INT_MAX));
    $domainsAtRisk = ! $freeCanUseCustomDomains && $activeDomains > 0;
@endphp

@if ($excessAliases > 0 || $excessRules > 0 || $domainsAtRisk)
## What happens on {{ $trialEndsAt->format('jS F Y') }}

If you don't upgrade by then, the following will be **deactivated** (not deleted — you can reactivate any of them later by upgrading or by removing others to fit the Free plan):

@if ($excessAliases > 0)
- **{{ $excessAliases }} excess aliases** (your oldest {{ $freeAliasLimit }} stay active; the rest are deactivated by creation order)
@endif
@if ($excessRules > 0)
- **{{ $excessRules }} excess rules** (all rules are deactivated since the Free plan doesn't include them)
@endif
@if ($domainsAtRisk)
- **{{ $activeDomains }} custom {{ $activeDomains === 1 ? 'domain' : 'domains' }}** (the Free plan doesn't support custom domains)
@endif

You'll receive reminders **7, 3, and 1 day** before the trial ends.
@endif

@component('mail::button', ['url' => config('app.url').'/subscription'])
View subscription
@endcomponent

Enjoy the trial — and as always, reply with any questions.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
