@extends('dashboard.layout')

@section('title', 'Call Sessions')

@section('dashboard-content')
@include('dashboard.expert.submenu')

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Call Sessions</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Manage your scheduled call consultations</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <p class="text-gray-600">No call sessions scheduled yet.</p>
</div>
@endsection
