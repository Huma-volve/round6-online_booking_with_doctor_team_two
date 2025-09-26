<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => 'required_without:phone|nullable|email',
            'phone'    => 'required_without:email|nullable|string',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.required_without' => 'Email or phone is required.',
            'phone.required_without' => 'Email or phone is required.',
            'password.required'      => 'Password is required.',
        ];
    }
}
