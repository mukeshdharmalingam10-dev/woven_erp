<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-() ]+$/'],
            'role_id' => ['required', 'exists:roles,id'],
            'branches' => ['nullable', 'array'],
            'branches.*' => ['exists:branches,id'],
            'send_email' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'mobile.regex' => 'The mobile number format is invalid.',
            'role_id.required' => 'The role field is required.',
            'role_id.exists' => 'The selected role is invalid.',
            'branches.array' => 'Branches must be an array.',
        ];
    }
}
