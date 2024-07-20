<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'zip_code' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'img' => 'nullable|string|max:255',
        ];
    }
}
