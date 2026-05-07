<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LmsNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = LmsNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lms.notifications.index', compact('notifications'));
    }

    public function markRead(LmsNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return redirect()->back();
    }

    public function markAllRead(): RedirectResponse
    {
        LmsNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back();
    }
}
