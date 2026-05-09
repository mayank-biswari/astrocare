<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled by the controller/service
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['nullable', 'exists:users,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate owner has LMS access
            if ($this->owner_id) {
                $owner = User::find($this->owner_id);
                if ($owner && !$owner->hasPermissionTo('access lms')) {
                    $validator->errors()->add('owner_id', 'The selected user does not have LMS access.');
                }
            }

            // Validate assignee has LMS access
            if ($this->assigned_to) {
                $assignee = User::find($this->assigned_to);
                if ($assignee && !$assignee->hasPermissionTo('access lms')) {
                    $validator->errors()->add('assigned_to', 'The selected user does not have LMS access.');
                }
            }
        });
    }
}
