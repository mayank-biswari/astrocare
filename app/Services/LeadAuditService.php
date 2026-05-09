<?php

namespace App\Services;

use App\Models\CampaignLead;
use App\Models\LeadAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadAuditService
{
    /**
     * Log a lead assignment change.
     *
     * Records who changed the owner/assignee, the previous and new values,
     * and a human-readable description of the change.
     */
    public function logAssignment(
        User $actor,
        CampaignLead $lead,
        ?int $previousOwnerId,
        ?int $newOwnerId,
        ?int $previousAssigneeId,
        ?int $newAssigneeId
    ): void {
        $description = Str::limit(
            "User {$actor->name} assigned lead #{$lead->id} (owner: {$previousOwnerId}→{$newOwnerId}, assignee: {$previousAssigneeId}→{$newAssigneeId})",
            500,
            ''
        );

        $metadata = [
            'previous_owner_id' => $previousOwnerId,
            'new_owner_id' => $newOwnerId,
            'previous_assignee_id' => $previousAssigneeId,
            'new_assignee_id' => $newAssigneeId,
        ];

        try {
            LeadAuditLog::create([
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'action' => 'assignment',
                'description' => $description,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for lead assignment', [
                'action' => 'assignment',
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log a lead deletion.
     *
     * Records who deleted the lead and captures the lead's name and email
     * before the record is removed.
     */
    public function logDeletion(User $actor, CampaignLead $lead): void
    {
        $description = Str::limit(
            "User {$actor->name} deleted lead #{$lead->id} ({$lead->lead_code})",
            500,
            ''
        );

        $metadata = [
            'lead_name' => $lead->full_name,
            'lead_code' => $lead->lead_code,
        ];

        try {
            LeadAuditLog::create([
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'action' => 'deletion',
                'description' => $description,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for lead deletion', [
                'action' => 'deletion',
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log a lead export.
     *
     * Records who exported leads, how many were exported, and what filters
     * were applied.
     */
    public function logExport(User $actor, int $leadCount, array $filters): void
    {
        $filterSummary = !empty($filters) ? json_encode($filters) : 'none';

        $description = Str::limit(
            "User {$actor->name} exported {$leadCount} leads with filters: {$filterSummary}",
            500,
            ''
        );

        $metadata = [
            'lead_count' => $leadCount,
            'filters' => $filters,
        ];

        try {
            LeadAuditLog::create([
                'user_id' => $actor->id,
                'lead_id' => null,
                'action' => 'export',
                'description' => $description,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for lead export', [
                'action' => 'export',
                'user_id' => $actor->id,
                'lead_count' => $leadCount,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log a PII reveal action.
     *
     * Records who revealed PII, which field was revealed, and for which lead.
     * If persistence fails, the error is logged but the reveal operation is not blocked.
     */
    public function logPiiReveal(User $actor, CampaignLead $lead, string $field): void
    {
        $description = Str::limit(
            "{$actor->name} revealed {$field} for lead {$lead->lead_code}",
            500,
            ''
        );

        $metadata = [
            'field' => $field,
            'timestamp' => now()->toISOString(),
        ];

        try {
            LeadAuditLog::create([
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'action' => 'pii_reveal',
                'description' => $description,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for PII reveal', [
                'action' => 'pii_reveal',
                'user_id' => $actor->id,
                'lead_id' => $lead->id,
                'field' => $field,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
