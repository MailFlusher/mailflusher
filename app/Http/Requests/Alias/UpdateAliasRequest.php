<?php

namespace App\Http\Requests\Alias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAliasRequest extends FormRequest
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
            'description' => 'nullable|max:200',
            'from_name' => 'nullable|string|max:50',
            'alias_group_id' => [
                'nullable',
                'uuid',
                Rule::exists('alias_groups', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
