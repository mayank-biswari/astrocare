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
        
        \App\Models\Consultation::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'type' => $request->type,
            'duration' => $request->duration,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'amount' => $amount,
            'status' => 'scheduled'
        ]);

        return redirect()->route('dashboard.consultations')->with('success', 'Consultation booked successfully!');
    }

}
