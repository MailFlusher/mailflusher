<?php

namespace App\Http\Requests\Webhook;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreWebhookRequest extends FormRequest
{
    public const ALLOWED_EVENTS = [
        'alias.received',
        'alias.blocked',
        'alias.leaked',
    ];

    public function authorize(): bool
    {
        return (bool) $this->user()?->canUseWebhooks();
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'starts_with:https://', 'max:500'],
            'description' => ['nullable', 'string', 'max:200'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', self::ALLOWED_EVENTS)],
            'active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            $host = parse_url((string) $this->input('url'), PHP_URL_HOST);
            if ($host && (in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_starts_with($host, '169.254.'))) {
                $v->errors()->add('url', 'Webhook URLs cannot point at loopback or link-local addresses.');
            }
        });
    }
}
