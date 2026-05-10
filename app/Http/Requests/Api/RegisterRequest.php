<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Supported country dialing codes for phone number registration.
     */
    public const SUPPORTED_COUNTRY_CODES = [
        '+91', '+1', '+44', '+61', '+971', '+65', '+60', '+977',
        '+94', '+880', '+92', '+49', '+33', '+81', '+86', '+27',
        '+234', '+254', '+55', '+52',
    ];

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&]).+$/',
            ],
            'country_code' => [
                'required',
                'string',
                'regex:/^\+\d{1,4}$/',
                Rule::in(self::SUPPORTED_COUNTRY_CODES),
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^[\d\s\-]+$/',
                'min:7',
                'max:15',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (@$!%*#?&).',
            'country_code.regex' => 'The country code must start with "+" followed by 1 to 4 digits.',
            'country_code.in' => 'The selected country code is not supported.',
            'phone_number.regex' => 'The phone number may only contain digits, spaces, and hyphens.',
            'phone_number.min' => 'The phone number must be at least 7 characters.',
            'phone_number.max' => 'The phone number must not exceed 15 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
