<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
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
        // return [
        //     //
        //     'CardHolderName' => 'required|string',
        //     'last4'            => 'required|digits:4',
        //     'Type'            => 'required|string',
        //     'token'            => 'nullable|string',

        // ];
        return [
    'payment_method_id' => 'required|string',
];

    }
}
