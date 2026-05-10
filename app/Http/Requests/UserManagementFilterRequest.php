<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserManagementFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // AdminMiddleware handles auth
    }

    public function rules(): array
    {
        return [
            'search'    => ['nullable', 'string', 'max:100'],
            'role'      => ['nullable', 'string', 'in:admin,expert,user'],
            'date_from' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'date_to'   => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'sort_by'   => ['nullable', 'string', 'in:name,email,role,created_at'],
            'sort_dir'  => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('search')) {
            $search = trim($this->input('search'));
            // Whitespace-only becomes null
            $search = $search === '' ? null : mb_substr($search, 0, 100);
            $this->merge(['search' => $search]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Cross-field validation: date_from must be <= date_to
            if ($this->filled('date_from') && $this->filled('date_to')) {
                if ($this->input('date_from') > $this->input('date_to')) {
                    $validator->errors()->add(
                        'date_from',
                        'The start date must be before or equal to the end date.'
                    );
                }
            }
        });
    }
}
