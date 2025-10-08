<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:8',
            // 'pin'             => 'required|digits:4',
            'country'         => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'phone'           => 'required|string|max:15|unique:users,phone',
            'username'        => 'required|string|unique:users,username',
        ];
    }

    public function messages(): array
    {
        return [
            // 'secret_phrase.required' => 'The secret phrase is required',
            // 'pin.required'           => 'The pin is required',
            // 'pin.digits'             => 'The pin must be 4 digits',
            'country.required'       => 'The country is required',
            'state.required'         => 'The state is required',
            'phone.required'         => 'The phone is required',
            'username.required'      => 'The username is required',
            'username.unique'        => 'This username is already taken',
        ];
    }
}
