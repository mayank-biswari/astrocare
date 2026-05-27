<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class ConsultationController extends Controller
{
    public function index()
    {
        $service = \App\Models\Service::where('type', 'consultation')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->with('tiers')
            ->first();

        return view('consultations.index', compact('service'));
    }

    public function show($type)
    {
        $service = \App\Models\Service::where('type', 'consultation')
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->with(['tiers' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->first();

        if (!$service) {
            abort(404);
        }

        // Get selected tier from query string (if coming from index page)
        $selectedTierId = request('tier');
        $selectedTier = null;
        if ($selectedTierId) {
            $selectedTier = $service->tiers->firstWhere('id', $selectedTierId);
        }

        return view('consultations.show', compact('service', 'type', 'selectedTier'));
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
            'tier_id' => 'required|exists:service_tiers,id',
            'type' => 'required|string',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'captcha' => 'required|captcha'
        ], [
            'captcha.required' => 'Please enter the captcha code.',
            'captcha.captcha' => 'The captcha code you entered is incorrect. Please try again.',
        ]);

        $service = \App\Models\Service::findOrFail($request->service_id);
        $tier = \App\Models\ServiceTier::where('id', $request->tier_id)
            ->where('service_id', $service->id)
            ->where('is_active', true)
            ->firstOrFail();

        $amount = $tier->price;

        // Store booking details in session
        session([
            'consultation_booking' => [
                'service_id' => $request->service_id,
                'tier_id' => $tier->id,
                'tier_name' => $tier->name,
                'type' => $request->type,
                'duration' => (int) filter_var($tier->name, FILTER_SANITIZE_NUMBER_INT) ?: 30,
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

        // Create order
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'orderable_type' => 'App\\Models\\Consultation',
            'orderable_id' => $consultation->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $booking['amount'],
            'status' => 'pending',
            'payment_method' => $request->payment_gateway,
            'payment_status' => 'pending',
            'items' => [['name' => ucfirst($booking['type']) . ' Consultation', 'quantity' => 1, 'price' => $booking['amount']]],
            'shipping_address' => ['phone' => auth()->user()->phone ?? ''],
        ]);

        // Process payment
        $paymentService = new PaymentService();
        $result = $paymentService->processPayment($order, $request->payment_gateway);

        if (isset($result['redirect'])) {
            return redirect()->away($result['redirect']);
        }

        session()->forget('consultation_booking');

        if ($result['success']) {
            return redirect()->route('dashboard.consultations')->with('success', 'Consultation booked successfully!');
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
