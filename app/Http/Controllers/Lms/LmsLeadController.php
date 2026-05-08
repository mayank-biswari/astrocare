<?php

namespace App\Http\Controllers\Lms;

use App\Events\Lms\LeadStatusChanged;
use App\Events\Lms\NewLeadCreated;
use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Models\LmsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LmsLeadController extends Controller
{
    private const ALLOWED_SORT_COLUMNS = [
        'full_name',
        'email',
        'phone_number',
        'source',
        'status',
        'created_at',
    ];

    private const DEFAULT_SORT_COLUMN = 'created_at';
    private const DEFAULT_SORT_DIRECTION = 'desc';
    private const PER_PAGE = 20;

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $source = $request->query('source');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir');

        // Validate sort column against allowed list, fall back to default
        if (!in_array($sortBy, self::ALLOWED_SORT_COLUMNS, true)) {
            $sortBy = self::DEFAULT_SORT_COLUMN;
        }

        // Validate sort direction
        $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : self::DEFAULT_SORT_DIRECTION;

        $leads = CampaignLead::query()
            ->search($search)
            ->filterStatus($status)
            ->filterSource($source)
            ->filterDateRange($dateFrom, $dateTo)
            ->orderBy($sortBy, $sortDir)
            ->paginate(self::PER_PAGE)
            ->appends($request->query());

        $filters = [
            'search' => $search,
            'status' => $status,
            'source' => $source,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];

        return view('lms.leads.index', compact('leads', 'filters'));
    }

    public function create(): View
    {
        return view('lms.leads.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => ['required', 'string', 'regex:/^[+]?[\d\s\-()]{7,20}$/'],
            'date_of_birth' => 'nullable|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
            'source' => 'nullable|string|max:50',
        ]);

        $validated['status'] = 'new';

        $lead = CampaignLead::create($validated);

        // Fire event and create persistent notifications for all LMS users
        NewLeadCreated::dispatch($lead);
        LmsNotification::createForLmsUsers(
            'new_lead',
            'New Lead',
            "{$lead->full_name} from {$lead->source}",
            $lead->id
        );

        return redirect()->route('lms.leads.show', $lead);
    }

    public function show(CampaignLead $lead): View
    {
        $lead->load([
            'notes' => function ($query) {
                $query->with('author')->orderBy('created_at', 'desc');
            },
            'followUps' => function ($query) {
                $query->with('author');
            },
        ]);

        return view('lms.leads.show', compact('lead'));
    }

    public function edit(CampaignLead $lead): View
    {
        return view('lms.leads.edit', compact('lead'));
    }

    public function update(Request $request, CampaignLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => ['required', 'string', 'regex:/^[+]?[\d\s\-()]{7,20}$/'],
            'date_of_birth' => 'nullable|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
            'source' => 'nullable|string|max:50',
        ]);

        $lead->update($validated);

        return redirect()->route('lms.leads.show', $lead);
    }

    public function destroy(CampaignLead $lead): RedirectResponse
    {
        try {
            $lead->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete lead. Please try again.');
        }

        return redirect()->route('lms.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, CampaignLead $lead): RedirectResponse
    {
        $rules = [
            'status' => 'required|in:new,contacted,qualified,converted,lost',
        ];

        // If status is 'converted' or 'lost', require a note
        if (in_array($request->input('status'), ['converted', 'lost'])) {
            $rules['note'] = 'required|string|min:1';
        }

        $validated = $request->validate($rules);

        $oldStatus = $lead->status;
        $lead->update(['status' => $validated['status']]);

        // Create a note if provided
        if (!empty($validated['note'])) {
            $lead->notes()->create([
                'user_id' => auth()->id(),
                'body' => $validated['note'],
            ]);
        }

        // Fire event and create persistent notifications for all LMS users
        LeadStatusChanged::dispatch($lead, $oldStatus, $validated['status'], auth()->user());
        LmsNotification::createForLmsUsers(
            'status_changed',
            'Lead Status Updated',
            "{$lead->full_name}: {$oldStatus} → {$validated['status']}",
            $lead->id,
            ['old_status' => $oldStatus, 'new_status' => $validated['status']]
        );

        return redirect()->route('lms.leads.show', $lead)
            ->with('success', 'Lead status updated successfully.');
    }
}
