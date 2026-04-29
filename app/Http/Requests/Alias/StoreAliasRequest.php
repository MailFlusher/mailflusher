<?php

namespace App\Http\Requests\Alias;

use App\Rules\Alias\ValidAliasLocalPart;
use App\Rules\Recipient\VerifiedRecipientId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreAliasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'domain' => strtolower($this->domain),
            'local_part_without_extension' => Str::before($this->local_part, '+'), // Remove extension so that we can check alias uniqueness properly
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'domain' => [
                'required',
                'string',
                Rule::in($this->user()->domainOptions()),
            ],
            'description' => 'nullable|max:200',
            'format' => 'nullable|in:random_characters,uuid,random_words,random_male_name,random_female_name,random_noun,custom',
            'recipient_ids' => [
                'bail',
                'nullable',
                'array',
                'max:10',
                new VerifiedRecipientId,
            ],
            'expires_at' => 'nullable|date|after:now|before:+10 years',
            'max_emails' => 'nullable|integer|min:1|max:10000',
            'on_expiry' => 'nullable|in:discard,bounce',
            'ghost_mode' => 'nullable|boolean',
            'alias_group_id' => [
                'nullable',
                'uuid',
                Rule::exists('alias_groups', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('local_part_without_extension', [
            'bail',
            'required',
            'max:50',
            Rule::unique('aliases', 'local_part')->where(function ($query) {
                return $query->where('domain', $this->validationData()['domain']);
            }),
            new ValidAliasLocalPart,
        ], function () {
            $format = $this->validationData()['format'] ?? 'random_characters';

            return $format === 'custom';
        });
    }

    public function messages()
    {
        return [
            'local_part_without_extension.unique' => 'That alias already exists.',
        ];
    }
}
