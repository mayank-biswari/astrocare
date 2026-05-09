<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LeadAuditLog;
use Illuminate\View\View;

class LmsAuditLogController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Only Super Admin (ID 1) or users with "admin" role can access
        if ($user->id !== 1 && !$user->hasRole('admin')) {
            if (request()->expectsJson()) {
                abort(response()->json(['message' => 'Access denied.'], 403));
            }

            abort(403, 'Access denied.');
        }

        $logs = LeadAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('lms.audit-logs.index', compact('logs'));
    }
}
