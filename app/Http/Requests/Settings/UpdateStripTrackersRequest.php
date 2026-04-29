<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStripTrackersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'strip_trackers' => 'required|string|in:off,pixels_only,pixels_and_links',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v) {
            if ($this->input('strip_trackers') === 'pixels_and_links' && ! $this->user()->canUseLinkStripping()) {
                $v->errors()->add('strip_trackers', 'Link stripping is only available on Standard and Pro plans.');
            }
        });
    }
}
