<?php

namespace App\Http\Requests\Alias;

use App\Models\AliasGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAliasGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('alias_groups', 'name')->where('user_id', $this->user()->id),
            ],
            'description' => 'nullable|string|max:200',
            'color' => [
                'nullable',
                'string',
                Rule::in(AliasGroup::PALETTE),
            ],
            'sort_order' => 'nullable|integer|min:0|max:1000',
        ];
    }
}
