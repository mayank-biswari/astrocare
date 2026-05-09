<?php

namespace App\Services;

use App\Models\CampaignLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class LeadAccessControlService
{
    /**
     * Permission mapping: action => ['any' => permission name, 'own' => permission name or null]
     */
    protected array $permissionMap = [
        'view' => ['any' => 'view any lead', 'own' => 'view own lead'],
        'edit' => ['any' => 'edit any lead', 'own' => 'edit own lead'],
        'delete' => ['any' => 'delete any lead', 'own' => 'delete own lead'],
        'create' => ['any' => 'create lead', 'own' => null],
        'assign' => ['any' => 'assign leads', 'own' => null],
        'export' => ['any' => 'export leads', 'own' => null],
    ];

    /**
     * Check if user is Super Admin (ID === 1).
     */
    public function isSuperAdmin(User $user): bool
    {
        return $user->id === 1;
    }

    /**
     * Check if user owns or is assigned to a lead.
     */
    public function isOwnLead(User $user, CampaignLead $lead): bool
    {
        return $lead->owner_id === $user->id || $lead->assigned_to === $user->id;
    }

    /**
     * Check if user can perform an action on a specific lead.
     *
     * Logic:
     * 1. Super Admin bypass — always true
     * 2. Check "any" permission — if granted, true regardless of ownership
     * 3. Check "own" permission — if granted AND user owns/is assigned to lead, true
     * 4. Otherwise — false
     *
     * @param User $user
     * @param string $action One of: 'view', 'edit', 'delete', 'assign', 'create', 'export'
     * @param CampaignLead|null $lead Required for view/edit/delete; null for create/assign/export
     * @return bool
     */
    public function can(User $user, string $action, ?CampaignLead $lead = null): bool
    {
        // Super Admin bypass — user ID 1 always has full access
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $permissions = $this->permissionMap[$action] ?? null;

        if ($permissions === null) {
            return false;
        }

        // Check "any" permission — grants access regardless of ownership
        if ($user->hasPermissionTo($permissions['any'])) {
            return true;
        }

        // Check "own" permission — grants access only if user owns or is assigned to the lead
        if ($permissions['own'] !== null && $user->hasPermissionTo($permissions['own'])) {
            // If no lead provided (e.g., for list-level checks), deny
            if ($lead === null) {
                return false;
            }

            return $this->isOwnLead($user, $lead);
        }

        return false;
    }

    /**
     * Authorize or abort with 403.
     *
     * Detects JSON requests (Accept: application/json) and returns
     * a JSON response for API calls, otherwise aborts with HTML 403.
     */
    public function authorize(User $user, string $action, ?CampaignLead $lead = null): void
    {
        if ($this->can($user, $action, $lead)) {
            return;
        }

        if (request()->expectsJson()) {
            abort(response()->json(['message' => 'Access denied.'], 403));
        }

        abort(403, 'Access denied.');
    }

    /**
     * Check if user can view PII (email, phone).
     *
     * Returns true if the user is Super Admin (ID === 1) or has the
     * "view lead pii" permission.
     */
    public function canViewPii(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $user->hasPermissionTo('view lead pii');
    }

    /**
     * Scope a query to only leads the user can view.
     *
     * - "view any lead" → return unmodified query (all leads)
     * - "view own lead" → scope to owned or assigned leads
     * - Neither → return empty result set
     */
    public function scopeViewable(Builder $query, User $user): Builder
    {
        // Super Admin sees all leads
        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        // "view any lead" → all leads
        if ($user->hasPermissionTo('view any lead')) {
            return $query;
        }

        // "view own lead" → only owned or assigned leads
        if ($user->hasPermissionTo('view own lead')) {
            return $query->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        // No view permission → empty result
        return $query->whereRaw('0 = 1');
    }
}
