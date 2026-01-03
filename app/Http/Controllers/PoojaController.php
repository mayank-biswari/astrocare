<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoojaController extends Controller
{
    public function index()
    {
        $poojas = \App\Models\PoojaService::where('is_active', true)->get();
        return view('pooja.index', compact('poojas'));
    }

    public function temple()
    {
        return view('pooja.temple');
    }

    public function home()
    {
        return view('pooja.home');
    }

    public function jaapHomam()
    {
        return view('pooja.jaap-homam');
    }

    public function specialOccasion()
    {
        return view('pooja.special-occasion');
    }

    public function panditBooking()
    {
        return view('pooja.pandit-booking');
    }

    public function show($slug)
    {
        $pooja = \App\Models\PoojaService::where('slug', $slug)->firstOrFail();
        return view('pooja.show', compact('pooja'));
    }

    public function book(Request $request)
    {
        if (!auth()->check()) {
            // Store form data in session
            session(['pooja_booking_data' => $request->all()]);
            return redirect()->route('login')->with('error', 'Please login to book a pooja.');
        }
        
        // Check if we have stored booking data from before login
        $bookingData = session('pooja_booking_data', $request->all());
        session()->forget('pooja_booking_data');
        
        $request->merge($bookingData);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'scheduled_at' => 'required|date|after:now',
            'devotee_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'gotra' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'captcha' => 'required|captcha'
        ]);

        // Store booking details in session
        session([
            'pooja_booking' => [
                'name' => $request->name,
                'type' => $request->type,
                'amount' => $request->amount,
                'scheduled_at' => $request->scheduled_at,
                'devotee_name' => $request->devotee_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'gotra' => $request->gotra,
                'special_requirements' => $request->special_requirements
            ]
        ]);

        return redirect()->route('pooja.checkout');
    }

    public function checkout()
    {
        $booking = session('pooja_booking');
        
        if (!$booking) {
            return redirect()->route('pooja.index')->with('error', 'No booking found.');
        }

        $paymentGateways = \App\Models\PaymentGateway::where('is_active', true)->get();
        
        return view('pooja.checkout', compact('booking', 'paymentGateways'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_gateway' => 'required|string'
        ]);

        $booking = session('pooja_booking');
        
        if (!$booking) {
            return redirect()->route('pooja.index')->with('error', 'No booking found.');
        }

        // Create pooja booking
        $pooja = \App\Models\Pooja::create([
            'user_id' => auth()->id(),
            'name' => $booking['name'],
            'type' => $booking['type'],
            'description' => $booking['special_requirements'] ?? 'Pooja booking',
            'amount' => $booking['amount'],
            'scheduled_at' => $booking['scheduled_at'],
            'special_requirements' => $booking['special_requirements'],
            'status' => 'booked'
        ]);

        // Clear session
        session()->forget('pooja_booking');

        return redirect()->route('dashboard.poojas')->with('success', 'Pooja booked successfully!');
    }
}
