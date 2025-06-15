<?php

namespace App\Http\Requests\UserManagement\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => 'required|numeric|exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
        ];
    }

    // method for customer validation messages
    public function messages(): array
    {
        return [
            'role.required' => 'Role is required.',
            'role.numeric' => 'Role should be a numeric.',
            'role.exists' => 'Role does not exists.',
            'name.required' => 'Name is required.',
            'name.string' => 'Name should be a string.',
            'name.max' => 'Name should not exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email should be a valid email.',
            'email.max' => 'Email should not exceed 255 characters.',
            'email.unique' => 'Email already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password should be at least 8 characters.',
        ];
    }

    // method for errors on validation failed
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400),
        );
    }
}
