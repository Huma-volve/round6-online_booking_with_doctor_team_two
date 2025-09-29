<?php

namespace App\Http\Requests\Api\Auth;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|string|email|max:255|unique:users,email',
            'age' => 'nullable|integer|min:0',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:doctor,patient,admin',
            'profile_img' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:500',
            
            //
        ];
    }
}
