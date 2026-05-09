<?php

namespace App\Services;

use App\Models\CampaignLead;
use App\Models\User;

class LeadPiiMaskingService
{
    /**
     * Determine if the user is authorized to view PII (email, phone).
     *
     * Returns true if the user is Super Admin (ID === 1) or has the
     * "view lead pii" permission.
     */
    public function canViewPii(User $user): bool
    {
        if ($user->id === 1) {
            return true;
        }

        return $user->hasPermissionTo('view lead pii');
    }

    /**
     * Determine if PII should be masked for the given user.
     *
     * Inverse of canViewPii — convenience method for view logic.
     */
    public function shouldMask(User $user): bool
    {
        return !$this->canViewPii($user);
    }

    /**
     * Return the email value or its masked equivalent.
     *
     * If the user is authorized to view PII, returns the actual email.
     * Otherwise, returns the lead's lead_code as a non-sensitive substitute.
     */
    public function maskEmail(CampaignLead $lead, User $user): string
    {
        if ($this->canViewPii($user)) {
            return $lead->email ?? '';
        }

        return $lead->lead_code ?? '';
    }

    /**
     * Return the phone number value or its masked equivalent.
     *
     * If the user is authorized to view PII, returns the actual phone number.
     * Otherwise, returns a masked placeholder string.
     */
    public function maskPhone(CampaignLead $lead, User $user): string
    {
        if ($this->canViewPii($user)) {
            return $lead->phone_number ?? '';
        }

        return '••••••••';
    }
}
