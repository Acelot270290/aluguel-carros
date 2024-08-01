<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'zip_code' => 'required|nullable|string|max:20',
            'street' => 'required|nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'city' => 'required|nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'state' => 'required|nullable|string|max:100',
            'img' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'error' => 'Validation error',
            'messages' => $errors->messages()
        ], 422));
    }
}
