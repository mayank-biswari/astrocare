<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Generate Kundli logic here
        return redirect()->route('kundli.index')->with('success', 'Kundli generated successfully!');
    }

    public function show($id)
    {
        return view('kundli.show', compact('id'));
    }
}
