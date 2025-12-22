<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Process question submission
        return redirect()->back()->with('success', 'Question submitted successfully!');
    }

    public function predictions()
    {
        return view('services.predictions');
    }
}
