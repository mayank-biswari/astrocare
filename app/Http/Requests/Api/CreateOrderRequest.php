<?php

namespace App\Http\Requests\Api;

use App\Models\PaymentGateway;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
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
            'plan_id' => ['required', 'string', Rule::in(array_keys(config('plans')))],
            'payment_gateway' => [
                'required',
                'string',
                'max:50',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $planId = $this->input('plan_id');
                    $plans = config('plans');

                    if (!$planId || !isset($plans[$planId])) {
                        return;
                    }

                    $currency = $plans[$planId]['currency'];
                    $activeGateways = PaymentGateway::getActiveGateways($currency);
                    $activeCodes = $activeGateways->pluck('code')->toArray();

                    if (!in_array($value, $activeCodes)) {
                        $fail('The selected payment gateway is not available for this plan.');
                    }
                },
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
            'plan_id.required' => 'The plan is required.',
            'plan_id.in' => 'The selected plan is invalid.',
            'payment_gateway.required' => 'The payment gateway is required.',
            'payment_gateway.string' => 'The payment gateway must be a string.',
            'payment_gateway.max' => 'The payment gateway must not exceed 50 characters.',
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
