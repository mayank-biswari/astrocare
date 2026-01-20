<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        // Check for pending consultation booking after login
        if (session('consultation_booking_data')) {
            $bookingData = session('consultation_booking_data');
            session()->forget('consultation_booking_data');
            
            // Calculate price based on duration
            $service = \App\Models\Service::findOrFail($bookingData['service_id']);
            $multiplier = 1;
            if ($bookingData['duration'] == 45) $multiplier = 1.5;
            elseif ($bookingData['duration'] == 60) $multiplier = 2;
            $amount = $service->price * $multiplier;
            
            // Store booking details in session for checkout
            session([
                'consultation_booking' => [
                    'service_id' => $bookingData['service_id'],
                    'type' => $bookingData['type'],
                    'duration' => $bookingData['duration'],
                    'scheduled_at' => $bookingData['scheduled_at'],
                    'notes' => $bookingData['notes'] ?? null,
                    'amount' => $amount
                ]
            ]);
            
            return redirect()->route('consultations.checkout');
        }
        
        // Check for pending pooja booking after login
        if (session('pooja_booking_data')) {
            $bookingData = session('pooja_booking_data');
            session()->forget('pooja_booking_data');
            
            // Store booking details in session for checkout
            session([
                'pooja_booking' => [
                    'name' => $bookingData['name'],
                    'type' => $bookingData['type'],
                    'amount' => $bookingData['amount'],
                    'scheduled_at' => $bookingData['scheduled_at'],
                    'devotee_name' => $bookingData['devotee_name'],
                    'phone' => $bookingData['phone'],
                    'email' => $bookingData['email'] ?? null,
                    'gotra' => $bookingData['gotra'] ?? null,
                    'special_requirements' => $bookingData['special_requirements'] ?? null
                ]
            ]);
            
            return redirect()->route('pooja.checkout');
        }
        
        // Check for pending kundli generation after login
        if (session('kundli_generation_data')) {
            $kundliData = session('kundli_generation_data');
            session()->forget('kundli_generation_data');
            
            return redirect()->route('kundli.create')->with('kundli_data', $kundliData);
        }
        
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

    public function questions(Request $request)
    {
        $query = auth()->user()->questions();

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $questions = $query->latest()->get();
        return view('dashboard.questions', compact('questions'));
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

        // Get user's orders stats
        $orderStats = [
            'total' => $user->orders()->count(),
            'completed' => $user->orders()->where('status', 'completed')->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count()
        ];

        // Get user's consultations stats
        $consultationStats = [
            'total' => $user->consultations()->count(),
            'completed' => $user->consultations()->where('status', 'completed')->count(),
            'upcoming' => $user->consultations()->where('status', 'scheduled')->count(),
            'total_spent' => $user->consultations()->where('status', 'completed')->sum('amount')
        ];

        // Get user's kundlis stats
        $kundliStats = [
            'generated' => $user->kundlis()->count(),
            'downloaded' => $user->kundlis()->whereNotNull('downloaded_at')->count(),
            'shared' => $user->kundlis()->where('is_shared', true)->count()
        ];

        // Get user's poojas stats
        $poojaStats = [
            'total' => $user->poojas()->count(),
            'completed' => $user->poojas()->where('status', 'completed')->count(),
            'upcoming' => $user->poojas()->where('status', 'scheduled')->count(),
            'total_spent' => $user->poojas()->where('status', 'completed')->sum('amount')
        ];

        // Get user's questions stats
        $questionStats = [
            'total' => $user->questions()->count(),
            'completed' => $user->questions()->where('status', 'completed')->count(),
            'pending' => $user->questions()->where('status', 'pending')->count(),
            'total_spent' => $user->questions()->where('status', 'completed')->sum('amount')
        ];

        // Calculate spending
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $spendingStats = [
            'this_month' => $user->orders()->where('created_at', '>=', $thisMonth)->sum('total_amount') +
                          $user->consultations()->where('created_at', '>=', $thisMonth)->sum('amount') +
                          $user->poojas()->where('created_at', '>=', $thisMonth)->sum('amount') +
                          $user->questions()->where('created_at', '>=', $thisMonth)->sum('amount'),
            'last_month' => $user->orders()->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('total_amount') +
                          $user->consultations()->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('amount') +
                          $user->poojas()->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('amount') +
                          $user->questions()->whereBetween('created_at', [$lastMonth, $thisMonth])->sum('amount'),
            'lifetime' => $user->orders()->sum('total_amount') + $user->consultations()->sum('amount') + $user->poojas()->sum('amount') + $user->questions()->sum('amount')
        ];

        // Activity stats
        $activityStats = [
            'account_age' => $user->created_at->diffForHumans(null, true),
            'last_login' => $user->updated_at->diffForHumans(),
            'profile_completion' => $this->calculateProfileCompletion($user)
        ];

        // Recent activities
        $recentActivities = collect()
            ->merge($user->orders()->latest()->take(3)->get()->map(fn($order) => [
                'type' => 'order',
                'title' => 'Order #' . $order->id . ' placed',
                'description' => 'Total: ₹' . number_format($order->total_amount),
                'date' => $order->created_at
            ]))
            ->merge($user->consultations()->latest()->take(2)->get()->map(fn($consultation) => [
                'type' => 'consultation',
                'title' => 'Consultation ' . ($consultation->status == 'completed' ? 'completed' : 'booked'),
                'description' => $consultation->type . ' Session',
                'date' => $consultation->created_at
            ]))
            ->merge($user->kundlis()->latest()->take(2)->get()->map(fn($kundli) => [
                'type' => 'kundli',
                'title' => 'Kundli generated',
                'description' => 'Birth Chart Analysis',
                'date' => $kundli->created_at
            ]))
            ->sortByDesc('date')
            ->take(5);

        return view('dashboard.reports', compact(
            'orderStats', 'consultationStats', 'kundliStats',
            'poojaStats', 'questionStats', 'spendingStats', 'activityStats', 'recentActivities'
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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
            ]
        ]);

        if (!\Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        auth()->user()->update([
            'password' => \Hash::make($request->password)
        ]);

        return redirect()->route('dashboard.settings')->with('success', 'Password updated successfully!');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            \Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_photo));
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->update(['profile_photo' => '/storage/' . $path]);

        return redirect()->route('dashboard.settings')->with('success', 'Profile photo updated successfully!');
    }

    public function deleteProfilePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo) {
            \Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_photo));
            $user->update(['profile_photo' => null]);
        }

        return redirect()->route('dashboard.settings')->with('success', 'Profile photo deleted successfully!');
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
