@component('mail::message')

# Your {{ $previousPlanName }} subscription has ended

Your **{{ $previousPlanName }}** subscription has been cancelled or its renewal failed, so your account is now on the **Free plan**.

@if ($deactivated['aliases'] > 0 || ($deactivated['recipients'] ?? 0) > 0 || $deactivated['rules'] > 0 || $deactivated['domains'] > 0)
## What was deactivated

The following items were deactivated to fit Free plan limits. **Nothing was deleted** — anything below can be reactivated by resubscribing, or by removing other items so it fits the Free limits.

@if ($deactivated['aliases'] > 0)
- **{{ $deactivated['aliases'] }} {{ $deactivated['aliases'] === 1 ? 'alias' : 'aliases' }}** deactivated (oldest stayed active)
@endif
@if (($deactivated['recipients'] ?? 0) > 0)
- **{{ $deactivated['recipients'] }} {{ $deactivated['recipients'] === 1 ? 'recipient' : 'recipients' }}** deactivated (your default recipient stayed active; forwarding to deactivated recipients is paused)
@endif
@if ($deactivated['rules'] > 0)
- **{{ $deactivated['rules'] }} {{ $deactivated['rules'] === 1 ? 'rule' : 'rules' }}** deactivated (Free plan has no rules)
@endif
@if ($deactivated['domains'] > 0)
- **{{ $deactivated['domains'] }} custom {{ $deactivated['domains'] === 1 ? 'domain' : 'domains' }}** deactivated (Free plan doesn't support custom domains)
@endif
@else
Nothing was deactivated — your usage fits within the Free plan.
@endif

You can resubscribe at any time and everything comes back.

@component('mail::button', ['url' => config('app.url').'/subscription'])
Resubscribe
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
