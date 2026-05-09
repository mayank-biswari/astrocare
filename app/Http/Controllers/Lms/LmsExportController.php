<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Services\LeadAccessControlService;
use App\Services\LeadAuditService;
use App\Services\LeadPiiMaskingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LmsExportController extends Controller
{
    public function __construct(
        private LeadAccessControlService $accessControl,
        private LeadAuditService $auditService,
        private LeadPiiMaskingService $piiMaskingService
    ) {}

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'export');

        $search = $request->query('search');
        $status = $request->query('status');
        $source = $request->query('source');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = CampaignLead::query()
            ->with(['owner', 'assignee'])
            ->search($search)
            ->filterStatus($status)
            ->filterSource($source)
            ->filterDateRange($dateFrom, $dateTo);

        // Apply view scoping — user only exports leads they can view
        $query = $this->accessControl->scopeViewable($query, auth()->user());

        // Get total count before applying limit to detect truncation
        $totalCount = $query->count();
        $truncated = $totalCount > 10000;

        // Apply 10,000 row limit
        $leads = $query->limit(10000)->get();

        if ($leads->isEmpty()) {
            return redirect()->back()->with('info', 'No leads match the current filters');
        }

        // Log the export
        $filters = array_filter([
            'search' => $search,
            'status' => $status,
            'source' => $source,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        if ($truncated) {
            $filters['truncated'] = true;
            $filters['total_matching'] = $totalCount;
        }

        $this->auditService->logExport(auth()->user(), $leads->count(), $filters);

        $filename = 'leads-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['full_name', 'lead_code', 'email', 'phone_number', 'date_of_birth', 'place_of_birth', 'source', 'status', 'message', 'owner_name', 'assignee_name', 'created_at'];

        return new StreamedResponse(function () use ($leads, $columns, $truncated) {
            $handle = fopen('php://output', 'w');

            // Write header row
            fputcsv($handle, $columns);

            // Determine PII visibility for the current user
            $canViewPii = $this->piiMaskingService->canViewPii(auth()->user());

            // Write data rows
            foreach ($leads as $lead) {
                $row = [];
                foreach ($columns as $column) {
                    if ($column === 'owner_name') {
                        $row[] = $lead->owner?->name ?? '';
                    } elseif ($column === 'assignee_name') {
                        $row[] = $lead->assignee?->name ?? '';
                    } elseif ($column === 'email') {
                        $row[] = $canViewPii ? ($lead->email ?? '') : ($lead->lead_code ?? '');
                    } elseif ($column === 'phone_number') {
                        $row[] = $canViewPii ? ($lead->phone_number ?? '') : 'MASKED';
                    } else {
                        $value = $lead->{$column};
                        if ($value instanceof \Carbon\Carbon || $value instanceof \DateTimeInterface) {
                            $value = $value->format('Y-m-d');
                        }
                        $row[] = $value ?? '';
                    }
                }
                fputcsv($handle, $row);
            }

            // Add truncation indication if results were limited
            if ($truncated) {
                $truncationRow = array_fill(0, count($columns), '');
                $truncationRow[0] = '--- Export truncated at 10,000 rows. Additional records exist. ---';
                fputcsv($handle, $truncationRow);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
