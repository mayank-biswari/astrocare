<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

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

        // Update user phone if not already filled
        if (auth()->check() && !auth()->user()->phone) {
            auth()->user()->update(['phone' => $request->phone]);
        }

        $questionData = session('question_checkout');
        $question = Question::findOrFail($questionData['question_id']);

        // Update question status to completed (after payment processing)
        $question->update(['status' => 'completed']);

        // Clear session
        session()->forget('question_checkout');

        return redirect()->route('dashboard')->with('success', 'Question submitted successfully! You will receive an answer within 24-48 hours.');
    }

    public function predictions()
    {
        return view('services.predictions');
    }
}
