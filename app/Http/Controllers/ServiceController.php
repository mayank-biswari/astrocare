<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Events\QuestionSubmitted;
use App\Services\PaymentService;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index');
    }

    public function horoscopeMatching()
    {
        return view('services.horoscope-matching');
    }

    public function processMatching(Request $request)
    {
        // Process horoscope matching logic
        return view('services.matching-result');
    }

    public function askQuestion()
    {
        return view('services.ask-question');
    }

    public function submitQuestion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'dob' => 'required|date',
            'category' => 'required|string',
            'question' => 'required|string'
        ]);

        // Check if user is authenticated
        if (!auth()->check()) {
            session(['question_data' => $request->all()]);
            return redirect()->route('login')->with('info', 'Please login or create an account to submit your question.');
        }

        $amount = 499;

        // Create question record
        $question = Question::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'time' => $request->time,
            'place' => $request->place,
            'category' => $request->category,
            'question' => $request->question,
            'amount' => $amount,
            'status' => 'pending'
        ]);

        // Dispatch event to send emails
        event(new QuestionSubmitted($question));

        // Store question details in session for checkout
        session([
            'question_checkout' => [
                'question_id' => $question->id,
                'name' => $request->name,
                'category' => $request->category,
                'amount' => $amount
            ]
        ]);

        return redirect()->route('ask.checkout');
    }

    public function checkout()
    {
        if (!session('question_checkout')) {
            return redirect()->route('ask.question')->with('error', 'No question data found. Please submit your question first.');
        }

        $questionData = session('question_checkout');
        $paymentGateways = \App\Models\PaymentGateway::getActiveGateways();
        return view('services.ask-checkout', compact('questionData', 'paymentGateways'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_gateway' => 'required|exists:payment_gateways,code'
        ]);

        if (!session('question_checkout')) {
            return redirect()->route('ask.question')->with('error', 'No question data found.');
        }

        if (auth()->check() && !auth()->user()->phone) {
            auth()->user()->update(['phone' => $request->phone]);
        }

        $questionData = session('question_checkout');
        $question = Question::findOrFail($questionData['question_id']);

        // Create order
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'orderable_type' => 'App\\Models\\Question',
            'orderable_id' => $question->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $questionData['amount'],
            'status' => 'pending',
            'payment_method' => $request->payment_gateway,
            'payment_status' => 'pending',
            'items' => [['name' => 'Ask Question - ' . ucfirst($questionData['category']), 'quantity' => 1, 'price' => $questionData['amount']]],
            'shipping_address' => ['phone' => $request->phone],
        ]);

        // Process payment
        $paymentService = new PaymentService();
        $result = $paymentService->processPayment($order, $request->payment_gateway);

        if (isset($result['redirect'])) {
            return redirect()->away($result['redirect']);
        }

        $question->update(['status' => 'completed']);
        session()->forget('question_checkout');

        if ($result['success']) {
            return redirect()->route('dashboard')->with('success', 'Question submitted successfully! You will receive an answer within 24-48 hours.');
        }

        return redirect()->route('ask.question')->with('error', $result['message']);
    }

    public function predictions()
    {
        return view('services.predictions');
    }
}
