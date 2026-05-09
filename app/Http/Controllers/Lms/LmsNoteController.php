<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Services\LeadAccessControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LmsNoteController extends Controller
{
    public function __construct(
        private LeadAccessControlService $accessControl
    ) {}

    public function store(Request $request, CampaignLead $lead): RedirectResponse
    {
        $this->accessControl->authorize(auth()->user(), 'edit', $lead);

        $request->merge([
            'body' => trim($request->input('body', '')),
        ]);

        $validated = $request->validate([
            'body' => 'required|string|min:1',
        ]);

        $lead->notes()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('lms.leads.show', $lead);
    }
}
