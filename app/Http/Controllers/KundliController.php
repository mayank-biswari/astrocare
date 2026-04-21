<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kundli;
use App\Services\PaymentService;

class KundliController extends Controller
{
    public function index()
    {
        return view('kundli.index');
    }

    public function create()
    {
        return view('kundli.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_time' => 'required',
            'birth_place' => 'required|string|max:255',
            'type' => 'required|in:basic,detailed,premium'
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            // Store kundli data in session for after login
            session(['kundli_generation_data' => $request->all()]);
            return redirect()->route('login')->with('info', 'Please login or create an account to generate your Kundli.');
        }

        // Calculate price based on type
        $prices = [
            'basic' => 299,
            'detailed' => 599,
            'premium' => 999
        ];
        $amount = $prices[$request->type];

        // Create kundli record
        $kundli = Kundli::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'birth_time' => $request->birth_time,
            'birth_place' => $request->birth_place,
            'type' => $request->type,
            'amount' => $amount,
            'status' => 'pending'
        ]);

        // Store kundli details in session for checkout
        session([
            'kundli_checkout' => [
                'kundli_id' => $kundli->id,
                'name' => $request->name,
                'type' => $request->type,
                'amount' => $amount
            ]
        ]);

        // Redirect to payment/checkout page
        return redirect()->route('kundli.checkout');
    }

    public function show($id)
    {
        return view('kundli.show', compact('id'));
    }

    public function checkout()
    {
        if (!session('kundli_checkout')) {
            return redirect()->route('kundli.create')->with('error', 'No kundli data found. Please generate a kundli first.');
        }

        $kundliData = session('kundli_checkout');
        $paymentGateways = \App\Models\PaymentGateway::getActiveGateways();
        return view('kundli.checkout', compact('kundliData', 'paymentGateways'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_gateway' => 'required|exists:payment_gateways,code'
        ]);

        if (!session('kundli_checkout')) {
            return redirect()->route('kundli.create')->with('error', 'No kundli data found.');
        }

        if (auth()->check() && !auth()->user()->phone) {
            auth()->user()->update(['phone' => $request->phone]);
        }

        $kundliData = session('kundli_checkout');
        $kundli = Kundli::findOrFail($kundliData['kundli_id']);

        // Create order
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'orderable_type' => 'App\\Models\\Kundli',
            'orderable_id' => $kundli->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $kundliData['amount'],
            'status' => 'pending',
            'payment_method' => $request->payment_gateway,
            'payment_status' => 'pending',
            'items' => [['name' => 'Kundli - ' . ucfirst($kundliData['type']), 'quantity' => 1, 'price' => $kundliData['amount']]],
            'shipping_address' => ['phone' => $request->phone],
        ]);

        // Process payment
        $paymentService = new PaymentService();
        $result = $paymentService->processPayment($order, $request->payment_gateway);

        if (isset($result['redirect'])) {
            return redirect()->away($result['redirect']);
        }

        $kundli->update(['status' => 'completed']);
        session()->forget('kundli_checkout');

        if ($result['success']) {
            return redirect()->route('dashboard.kundlis')->with('success', 'Kundli generated successfully!');
        }

        return redirect()->route('kundli.create')->with('error', $result['message']);
    }

    public function download($id)
    {
        $kundli = Kundli::findOrFail($id);

        // Check if user owns this kundli
        if (auth()->check() && $kundli->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kundli.pdf', compact('kundli'));
        return $pdf->download('kundli-' . $kundli->name . '-' . $kundli->id . '.pdf');
    }
}
