<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LmsFollowUpController extends Controller
{
    public function store(Request $request, CampaignLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        $lead->followUps()->create([
            'user_id' => auth()->id(),
            'description' => $validated['description'],
            'scheduled_date' => $validated['scheduled_date'],
        ]);

        return redirect()->route('lms.leads.show', $lead);
    }

    public function complete(LeadFollowUp $followUp): RedirectResponse
    {
        $followUp->update([
            'completed_at' => now(),
        ]);

        return redirect()->back();
    }
}
