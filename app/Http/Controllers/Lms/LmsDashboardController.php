<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\CampaignLead;
use App\Models\LeadFollowUp;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LmsDashboardController extends Controller
{
    public function index(): View
    {
        // Status counts (Requirement 2.1)
        $statusCounts = CampaignLead::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Leads created in last 7 days (Requirement 2.2)
        $recentLeadsCount = CampaignLead::where('created_at', '>=', now()->subDays(7))->count();

        // 10 most recently created leads (Requirement 2.3)
        $recentLeads = CampaignLead::orderBy('created_at', 'desc')->take(10)->get();

        // Upcoming follow-ups - next 7 days, not completed (Requirement 2.4)
        $upcomingFollowUps = LeadFollowUp::with('lead')
            ->upcoming(7)
            ->orderBy('scheduled_date')
            ->get();

        // Overdue follow-ups (Requirement 7.5)
        $overdueFollowUps = LeadFollowUp::with('lead')
            ->overdue()
            ->orderBy('scheduled_date')
            ->get();

        return view('lms.dashboard', compact(
            'statusCounts',
            'recentLeadsCount',
            'recentLeads',
            'upcomingFollowUps',
            'overdueFollowUps'
        ));
    }
}
