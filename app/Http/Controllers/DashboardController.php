<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function orders(Request $request)
    {
        $query = auth()->user()->orders();
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->get();
        return view('dashboard.orders', compact('orders'));
    }

    public function consultations(Request $request)
    {
        $query = auth()->user()->consultations();
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $consultations = $query->latest()->get();
        return view('dashboard.consultations', compact('consultations'));
    }

    public function kundlis()
    {
        $kundlis = auth()->user()->kundlis()->latest()->get();
        return view('dashboard.kundlis', compact('kundlis'));
    }

    public function poojas(Request $request)
    {
        $query = auth()->user()->poojas();
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $poojas = $query->latest()->get();
        return view('dashboard.poojas', compact('poojas'));
    }

    public function reports()
    {
        $user = auth()->user();
        
        // Get user's orders
        $orders = $user->orders();
        $orderStats = [
            'total' => $orders->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count()
        ];
        
        // Get user's consultations
        $consultations = $user->consultations();
        $consultationStats = [
            'total' => $consultations->count(),
            'completed' => $consultations->where('status', 'completed')->count(),
            'upcoming' => $consultations->where('status', 'scheduled')->count(),
            'total_spent' => $consultations->where('status', 'completed')->sum('price')
        ];
        
        // Get user's kundlis
        $kundlis = $user->kundlis();
        $kundliStats = [
            'generated' => $kundlis->count(),
            'downloaded' => $kundlis->whereNotNull('downloaded_at')->count(),
            'shared' => $kundlis->where('is_shared', true)->count()
        ];
        
        // Get user's poojas
        $poojas = $user->poojas();
        $poojaStats = [
            'total' => $poojas->count(),
            'completed' => $poojas->where('status', 'completed')->count(),
            'upcoming' => $poojas->where('status', 'scheduled')->count(),
            'total_spent' => $poojas->where('status', 'completed')->sum('price')
        ];
        
        // Calculate spending
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $spendingStats = [
            'this_month' => $orders->where('created_at', '>=', $thisMonth)->sum('total_amount') + 
                          $consultations->where('created_at', '>=', $thisMonth)->sum('price') + 
                          $poojas->where('created_at', '>=', $thisMonth)->sum('price'),
            'last_month' => $orders->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total_amount') + 
                          $consultations->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('price') + 
                          $poojas->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('price'),
            'lifetime' => $orders->sum('total_amount') + $consultations->sum('price') + $poojas->sum('price')
        ];
        
        // Activity stats
        $activityStats = [
            'account_age' => $user->created_at->diffForHumans(null, true),
            'last_login' => $user->updated_at->diffForHumans(),
            'profile_completion' => $this->calculateProfileCompletion($user)
        ];
        
        // Recent activities
        $recentActivities = collect()
            ->merge($orders->latest()->take(3)->get()->map(fn($order) => [
                'type' => 'order',
                'title' => 'Order #' . $order->id . ' placed',
                'description' => 'Total: ₹' . number_format($order->total_amount),
                'date' => $order->created_at
            ]))
            ->merge($consultations->latest()->take(2)->get()->map(fn($consultation) => [
                'type' => 'consultation',
                'title' => 'Consultation ' . ($consultation->status == 'completed' ? 'completed' : 'booked'),
                'description' => $consultation->type . ' Session',
                'date' => $consultation->created_at
            ]))
            ->merge($kundlis->latest()->take(2)->get()->map(fn($kundli) => [
                'type' => 'kundli',
                'title' => 'Kundli generated',
                'description' => 'Birth Chart Analysis',
                'date' => $kundli->created_at
            ]))
            ->sortByDesc('date')
            ->take(5);
        
        return view('dashboard.reports', compact(
            'orderStats', 'consultationStats', 'kundliStats', 
            'poojaStats', 'spendingStats', 'activityStats', 'recentActivities'
        ));
    }
    
    private function calculateProfileCompletion($user)
    {
        $fields = ['name', 'email', 'phone', 'date_of_birth'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }

    public function settings()
    {
        return view('dashboard.settings');
    }

    public function orderDetails($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        return view('dashboard.order-details', compact('order'));
    }

    public function trackOrder($id)
    {
        return view('dashboard.track-order', compact('id'));
    }

    public function downloadInvoice($id)
    {
        $pdf = Pdf::loadView('invoice', ['orderId' => $id]);
        return $pdf->download('invoice-' . $id . '.pdf');
    }

    public function cancelOrder($id)
    {
        // Cancel order logic
        return redirect()->route('dashboard.orders')->with('success', 'Order cancelled successfully!');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'language' => 'string',
            'currency' => 'string'
        ]);

        auth()->user()->update([
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'marketing_emails' => $request->has('marketing_emails'),
            'language' => $request->language ?? 'English',
            'currency' => $request->currency ?? 'INR (₹)'
        ]);

        return redirect()->route('dashboard.settings')->with('success', 'Preferences updated successfully!');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date'
        ]);

        auth()->user()->update($request->only(['name', 'email', 'phone', 'date_of_birth']));

        return redirect()->route('dashboard.settings')->with('success', 'Profile updated successfully!');
    }

    public function consultationDetails($id)
    {
        $consultation = auth()->user()->consultations()->findOrFail($id);
        return view('dashboard.consultation-details', compact('consultation'));
    }

    public function rescheduleConsultation($id)
    {
        $consultation = auth()->user()->consultations()->findOrFail($id);
        
        if ($consultation->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled consultations can be rescheduled.');
        }
        
        return view('dashboard.reschedule-consultation', compact('consultation'));
    }

    public function updateReschedule(Request $request, $id)
    {
        $consultation = auth()->user()->consultations()->findOrFail($id);
        
        if ($consultation->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled consultations can be rescheduled.');
        }
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required'
        ]);
        
        $scheduledAt = $request->date . ' ' . $request->time;
        
        $consultation->update([
            'scheduled_at' => $scheduledAt,
            'reschedule_reason' => $request->reschedule_reason
        ]);
        
        return redirect()->route('dashboard.consultation.details', $consultation->id)
            ->with('success', 'Consultation rescheduled successfully.');
    }

    public function cancelConsultation(Request $request, $id)
    {
        $consultation = auth()->user()->consultations()->findOrFail($id);
        
        if ($consultation->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled consultations can be cancelled.');
        }
        
        $request->validate([
            'cancellation_reason' => 'required|string'
        ]);
        
        $consultation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason
        ]);
        
        return redirect()->route('dashboard.consultations')
            ->with('success', 'Consultation cancelled successfully.');
    }

    public function downloadReport($id)
    {
        $consultation = auth()->user()->consultations()->findOrFail($id);
        
        if ($consultation->status !== 'completed') {
            return redirect()->back()->with('error', 'Report is only available for completed consultations.');
        }
        
        // Generate PDF report
        $pdf = Pdf::loadView('consultation-report', compact('consultation'));
        return $pdf->download('consultation-report-' . $id . '.pdf');
    }

    public function poojaDetails($id)
    {
        $pooja = auth()->user()->poojas()->findOrFail($id);
        return view('dashboard.pooja-details', compact('pooja'));
    }
}
