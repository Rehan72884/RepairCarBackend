<?php

namespace App\Http\Requests\UserManagement\Role;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id');

        return [
            'name' => 'required|string|max:100|unique:roles,name,'.$roleId.',id',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id', // validate permission id exists in database
            'description' => 'nullable|string|max:191',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The role name field is required.',
            'name.string' => 'The role name must be a string.',
            'name.max' => 'The role name must be less than or equal to 100 characters.',
            'name.unique' => 'The role name has already been taken.',
            'permissions.required' => 'The permissions field is required.',
            'permissions.*.integer' => 'The permissions must be an array of integers.',
            'permissions.*.exists' => 'The selected permission does not exist.',
            'description' => 'nullable|string|max:191',
        ];
    }

    // method for errors on validation failed
    public function failedValidation(Validator $validator)
    {
        // return the first error message
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400),
        );
    }
}
