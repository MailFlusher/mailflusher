<?php

namespace App\Http\Requests\Rule;

use App\Rules\General\ValidRuleId;
use Illuminate\Foundation\Http\FormRequest;

class StoreReorderRuleRequest extends FormRequest
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
            'ids' => [
                'required',
                'array',
                new ValidRuleId,
            ],
        ];
    }
}
