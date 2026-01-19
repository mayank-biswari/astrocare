@extends('dashboard.layout')

@section('title', __('messages.my_consultations') . ' - ' . __('messages.dashboard'))

@section('dashboard-content')
@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg mb-6 shadow-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.my_consultations') }}</h1>
</div>

<div class="flex justify-end mb-6">
    <a href="{{ route('consultations.index') }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 shadow-md hover:shadow-lg transition-all">
        <i class="fas fa-calendar-plus mr-2"></i>{{ __('messages.book_now') }}
    </a>
</div>

<!-- Status Filter -->
<div class="mb-6">
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('dashboard.consultations') }}" class="px-4 py-2 rounded-lg transition-all {{ !request('status') || request('status') == 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">All</a>
        <a href="{{ route('dashboard.consultations', ['status' => 'scheduled']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'scheduled' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.scheduled') }}</a>
        <a href="{{ route('dashboard.consultations', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'completed' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.completed') }}</a>
        <a href="{{ route('dashboard.consultations', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg transition-all {{ request('status') == 'cancelled' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">{{ __('messages.cancelled') }}</a>
    </div>
</div>

@if($consultations->count() > 0)
    <!-- Consultations List -->
    <div class="grid gap-6">
        @foreach($consultations as $consultation)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold">{{ $consultation->type }} {{ __('messages.consultations') }}</h3>
                        <p class="text-gray-600">{{ $consultation->description ?? __('messages.astrology_consultation') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($consultation->status == 'completed') bg-green-100 text-green-800
                        @elseif($consultation->status == 'scheduled') bg-blue-100 text-blue-800
                        @elseif($consultation->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ __('messages.' . $consultation->status) }}
                    </span>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('messages.consultation_date') }}</p>
                        <p class="font-medium">
                            @if($consultation->scheduled_at)
                                {{ \Carbon\Carbon::parse($consultation->scheduled_at)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($consultation->scheduled_at)->format('h:i A') }}
                            @else
                                Not scheduled
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-medium">{{ $consultation->duration }} minutes</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('messages.phone') }}</p>
                        <p class="font-medium">{{ $consultation->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('messages.amount') }}</p>
                        <p class="font-medium text-indigo-600">{{ formatPrice($consultation->amount) }}</p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard.consultation.details', $consultation->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">{{ __('messages.view_details') }}</a>
                    @if($consultation->status == 'completed')
                        <a href="{{ route('dashboard.consultation.report', $consultation->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">{{ __('messages.view_report') }}</a>
                    @elseif($consultation->status == 'scheduled')
                        <a href="{{ route('dashboard.consultation.reschedule', $consultation->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">{{ __('messages.reschedule') }}</a>
                        <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="openCancelModal({{ $consultation->id }})">{{ __('messages.cancel') }}</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-12">
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.no_items_found') }}</h3>
        <a href="{{ route('consultations.index') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
            {{ __('messages.book_now') }}
        </a>
    </div>
@endif
@endsection
