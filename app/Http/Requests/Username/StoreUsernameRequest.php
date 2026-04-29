<?php

namespace App\Http\Requests\Username;

use App\Rules\Username\NotBlacklisted;
use App\Rules\Username\NotDeletedUsername;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsernameRequest extends FormRequest
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
            'username' => [
                'bail',
                'required',
                'regex:/^[a-zA-Z0-9]*$/',
                'max:20',
                'unique:usernames,username',
                new NotBlacklisted,
                new NotDeletedUsername,
            ],
        ];
    }
}
