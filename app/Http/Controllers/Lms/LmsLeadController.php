<?php

namespace App\Http\Controllers\Lms;

use App\Events\Lms\LeadStatusChanged;
use App\Events\Lms\NewLeadCreated;
use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Models\LmsNotification;
use App\Models\User;
use App\Services\LeadAccessControlService;
use App\Services\LeadAuditService;
use App\Services\LeadPiiMaskingService;
use Illuminate\Http\JsonResponse;
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

    public function __construct(
        private LeadAccessControlService $accessControl,
        private LeadAuditService $auditService,
        private LeadPiiMaskingService $piiMaskingService
    ) {}

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

        $query = CampaignLead::query();
        $query = $this->accessControl->scopeViewable($query, auth()->user());

        $canViewPii = $this->piiMaskingService->canViewPii(auth()->user());

        $leads = $query
            ->search($search, $canViewPii)
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

        $accessControl = $this->accessControl;
        $piiMasking = $this->piiMaskingService;

        return view('lms.leads.index', compact('leads', 'filters', 'accessControl', 'piiMasking'));
    }

    public function create(): View
    {
        $this->accessControl->authorize(auth()->user(), 'create');

        return view('lms.leads.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'create');

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
        $validated['owner_id'] = auth()->id();

        $lead = CampaignLead::create($validated);

        // Fire event and create persistent notifications for all LMS users
        NewLeadCreated::dispatch($lead);
        LmsNotification::createForLmsUsers(
            'new_lead',
            'New Lead',
            "{$lead->full_name} from {$lead->source}",
            $lead->id,
            ['lead_code' => $lead->lead_code]
        );

        return redirect()->route('lms.leads.show', $lead);
    }

    public function show(CampaignLead $lead): View
    {
        $this->accessControl->authorize(auth()->user(), 'view', $lead);

        $lead->load([
            'notes' => function ($query) {
                $query->with('author')->orderBy('created_at', 'desc');
            },
            'followUps' => function ($query) {
                $query->with('author');
            },
        ]);

        $accessControl = $this->accessControl;
        $piiMasking = $this->piiMaskingService;

        return view('lms.leads.show', compact('lead', 'accessControl', 'piiMasking'));
    }

    public function edit(CampaignLead $lead): View
    {
        $this->accessControl->authorize(auth()->user(), 'edit', $lead);

        $piiMasking = $this->piiMaskingService;

        return view('lms.leads.edit', compact('lead', 'piiMasking'));
    }

    public function update(Request $request, CampaignLead $lead): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'edit', $lead);

        $canViewPii = $this->piiMaskingService->canViewPii(auth()->user());

        $rules = [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'place_of_birth' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
            'source' => 'nullable|string|max:50',
        ];

        if ($canViewPii) {
            $rules['email'] = 'required|email|max:255';
            $rules['phone_number'] = ['required', 'string', 'regex:/^[+]?[\d\s\-()]{7,20}$/'];
        }

        $validated = $request->validate($rules);

        // For unauthorized users, remove email/phone from validated data to preserve existing values
        if (!$canViewPii) {
            unset($validated['email'], $validated['phone_number']);
        }

        $lead->update($validated);

        return redirect()->route('lms.leads.show', $lead);
    }

    public function destroy(CampaignLead $lead): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'delete', $lead);

        $this->auditService->logDeletion(auth()->user(), $lead);

        try {
            $lead->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete lead. Please try again.');
        }

        return redirect()->route('lms.leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, CampaignLead $lead): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'edit', $lead);

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
            ['old_status' => $oldStatus, 'new_status' => $validated['status'], 'lead_code' => $lead->lead_code]
        );

        return redirect()->route('lms.leads.show', $lead)
            ->with('success', 'Lead status updated successfully.');
    }

    public function assign(CampaignLead $lead): View
    {
        $this->accessControl->authorize(auth()->user(), 'assign', $lead);

        // Get users with "access lms" permission as selectable targets
        $users = User::permission('access lms')->get();

        return view('lms.leads.assign', compact('lead', 'users'));
    }

    public function storeAssignment(Request $request, CampaignLead $lead): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'assign', $lead);

        $validated = $request->validate([
            'owner_id' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Validate target users have "access lms" permission
        if (!empty($validated['owner_id'])) {
            $ownerUser = User::find($validated['owner_id']);
            if (!$ownerUser || !$ownerUser->hasPermissionTo('access lms')) {
                return redirect()->back()->withErrors(['owner_id' => 'The selected user does not have LMS access.']);
            }
        }

        if (!empty($validated['assigned_to'])) {
            $assigneeUser = User::find($validated['assigned_to']);
            if (!$assigneeUser || !$assigneeUser->hasPermissionTo('access lms')) {
                return redirect()->back()->withErrors(['assigned_to' => 'The selected user does not have LMS access.']);
            }
        }

        $previousOwnerId = $lead->owner_id;
        $previousAssigneeId = $lead->assigned_to;

        $lead->update([
            'owner_id' => $validated['owner_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        // Refresh the model to ensure new ownership context is applied
        $lead->refresh();

        // Log the assignment change
        $this->auditService->logAssignment(
            auth()->user(),
            $lead,
            $previousOwnerId,
            $lead->owner_id,
            $previousAssigneeId,
            $lead->assigned_to
        );

        return redirect()->route('lms.leads.show', $lead)->with('success', 'Lead assignment updated successfully.');
    }

    public function revealPii(Request $request, CampaignLead $lead): JsonResponse
    {
        $request->validate([
            'field' => 'required|in:email,phone_number',
        ]);

        if (!$this->piiMaskingService->canViewPii(auth()->user())) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        $field = $request->input('field');

        $this->auditService->logPiiReveal(auth()->user(), $lead, $field);

        return response()->json(['value' => $lead->{$field}]);
    }
}
