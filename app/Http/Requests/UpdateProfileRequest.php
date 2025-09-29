<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
                $userId = $this->user()->id;

        return [
            //
              'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|unique:users,email,' . $userId,
            'age'         => 'nullable|integer|min:1|max:120',
            'profile_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
