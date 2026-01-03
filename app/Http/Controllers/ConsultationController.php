<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $services = \App\Models\Service::where('type', 'consultation')
            ->where('is_active', true)
            ->get();
        return view('consultations.index', compact('services'));
    }

    public function show($type)
    {
        $service = \App\Models\Service::where('type', 'consultation')
            ->where('name', 'like', '%' . ucfirst($type) . '%')
            ->first();
        
        if (!$service) {
            abort(404);
        }
        
        return view('consultations.show', compact('service', 'type'));
    }

    public function book(Request $request)
    {
        if (!auth()->check()) {
            // Store form data in session
            session(['consultation_booking_data' => $request->all()]);
            return redirect()->route('login')->with('error', 'Please login to book a consultation.');
        }
        
        // Check if we have stored booking data from before login
        $bookingData = session('consultation_booking_data', $request->all());
        session()->forget('consultation_booking_data');
        
        $request->merge($bookingData);
        
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'type' => 'required|string',
            'duration' => 'required|integer|in:30,45,60',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'captcha' => 'required|captcha'
        ]);

        $service = \App\Models\Service::findOrFail($request->service_id);
        
        // Calculate price based on duration
        $multiplier = 1;
        if ($request->duration == 45) $multiplier = 1.5;
        elseif ($request->duration == 60) $multiplier = 2;
        
        $amount = $service->price * $multiplier;
        
        // Store booking details in session
        session([
            'consultation_booking' => [
                'service_id' => $request->service_id,
                'type' => $request->type,
                'duration' => $request->duration,
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
                'amount' => $amount
            ]
        ]);

        return redirect()->route('consultations.checkout');
    }
    
    public function checkout()
    {
        $booking = session('consultation_booking');
        
        if (!$booking) {
            return redirect()->route('consultations.index')->with('error', 'No booking found.');
        }

        $paymentGateways = \App\Models\PaymentGateway::where('is_active', true)->get();
        
        return view('consultations.checkout', compact('booking', 'paymentGateways'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_gateway' => 'required|string'
        ]);

        $booking = session('consultation_booking');
        
        if (!$booking) {
            return redirect()->route('consultations.index')->with('error', 'No booking found.');
        }

        // Create consultation booking
        $consultation = \App\Models\Consultation::create([
            'user_id' => auth()->id(),
            'service_id' => $booking['service_id'],
            'type' => $booking['type'],
            'duration' => $booking['duration'],
            'scheduled_at' => $booking['scheduled_at'],
            'notes' => $booking['notes'],
            'amount' => $booking['amount'],
            'status' => 'scheduled'
        ]);

        // Clear session
        session()->forget('consultation_booking');

        return redirect()->route('dashboard.consultations')->with('success', 'Consultation booked successfully!');
    }
}
