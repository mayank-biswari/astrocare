<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LmsExportController extends Controller
{
    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $source = $request->query('source');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $leads = CampaignLead::query()
            ->search($search)
            ->filterStatus($status)
            ->filterSource($source)
            ->filterDateRange($dateFrom, $dateTo)
            ->get();

        if ($leads->isEmpty()) {
            return redirect()->back()->with('info', 'No leads match the current filters');
        }

        $filename = 'leads-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['full_name', 'email', 'phone_number', 'date_of_birth', 'place_of_birth', 'source', 'status', 'message', 'created_at'];

        return new StreamedResponse(function () use ($leads, $columns) {
            $handle = fopen('php://output', 'w');

            // Write header row
            fputcsv($handle, $columns);

            // Write data rows
            foreach ($leads as $lead) {
                $row = [];
                foreach ($columns as $column) {
                    $value = $lead->{$column};
                    if ($value instanceof \Carbon\Carbon || $value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d');
                    }
                    $row[] = $value ?? '';
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
