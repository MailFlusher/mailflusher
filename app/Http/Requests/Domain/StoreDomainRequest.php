<?php

namespace App\Http\Requests\Domain;

use App\Rules\Domain\NotLocalDomain;
use App\Rules\Domain\NotUsedAsRecipientDomain;
use App\Rules\Domain\ValidDomain;
use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'domain' => [
                'bail',
                'required',
                'string',
                'max:100',
                'unique:domains',
                new ValidDomain,
                new NotLocalDomain,
                new NotUsedAsRecipientDomain,
            ],
        ];
    }
}
