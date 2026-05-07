<?php

namespace App\Console\Commands;

use App\Events\Lms\FollowUpOverdue;
use App\Models\LeadFollowUp;
use App\Models\LmsNotification;
use Illuminate\Console\Command;

class CheckOverdueFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'lms:check-overdue-followups';

    /**
     * The console command description.
     */
    protected $description = 'Check for overdue follow-ups and notify their authors';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $overdueFollowUps = LeadFollowUp::with('lead')
            ->whereNull('completed_at')
            ->where('scheduled_date', '<', today())
            ->get();

        $notified = 0;

        foreach ($overdueFollowUps as $followUp) {
            // Check if an overdue notification was already sent today for this follow-up's author
            $alreadyNotifiedToday = LmsNotification::where('user_id', $followUp->user_id)
                ->where('type', 'follow_up_overdue')
                ->where('lead_id', $followUp->campaign_lead_id)
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyNotifiedToday) {
                continue;
            }

            // Fire the FollowUpOverdue event
            event(new FollowUpOverdue($followUp));

            // Create a persistent LmsNotification record for the follow-up author
            LmsNotification::create([
                'user_id' => $followUp->user_id,
                'type' => 'follow_up_overdue',
                'title' => 'Overdue Follow-Up',
                'message' => "Follow-up for {$followUp->lead->full_name} is overdue",
                'lead_id' => $followUp->campaign_lead_id,
                'data' => ['follow_up_id' => $followUp->id],
            ]);

            $notified++;
        }

        $this->info("Processed {$overdueFollowUps->count()} overdue follow-ups. Sent {$notified} new notifications.");

        return Command::SUCCESS;
    }
}
