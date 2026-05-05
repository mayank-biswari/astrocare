@extends('dashboard.layout')

@section('title', 'Chat Sessions')

@section('dashboard-content')
@include('dashboard.expert.submenu')

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Chat Sessions</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Manage your scheduled chat consultations</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    @if($chatSessions->count() > 0)
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Order #</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chatSessions as $session)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-4 font-medium">{{ $session['order']->order_number }}</td>
                            <td class="px-4 py-4">
                                @if($session['customer'])
                                    <div>{{ $session['customer']->name }}</div>
                                    <div class="text-xs text-gray-500">Code: {{ $session['customer']->user_code ?? 'N/A' }}</div>
                                @else
                                    <span class="text-gray-400">Guest</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">{{ $session['item']['quantity'] ?? 1 }} {{ $session['item']['quantity_unit'] ?? 'min' }}</td>
                            <td class="px-4 py-4 font-semibold">{{ currencySymbol() }}{{ number_format(($session['item']['price'] ?? 0) * ($session['item']['quantity'] ?? 1), 2) }}</td>
                            <td class="px-4 py-4">
                                @php $status = $session['order']->status; @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-500">{{ $session['order']->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="sm:hidden space-y-3">
            @foreach($chatSessions as $session)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-500">{{ $session['order']->order_number }}</span>
                        @php $status = $session['order']->status; @endphp
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            {{ $status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ ucfirst($status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            @if($session['customer'])
                                <div class="font-semibold text-sm text-gray-800">{{ $session['customer']->name }}</div>
                                <div class="text-xs text-gray-500">Code: {{ $session['customer']->user_code ?? 'N/A' }}</div>
                            @else
                                <span class="text-gray-400 text-sm">Guest</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm text-gray-800">{{ currencySymbol() }}{{ number_format(($session['item']['price'] ?? 0) * ($session['item']['quantity'] ?? 1), 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $session['item']['quantity'] ?? 1 }} {{ $session['item']['quantity_unit'] ?? 'min' }}</div>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 mt-2">{{ $session['order']->created_at->format('M d, Y H:i') }}</div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600 text-sm">No chat sessions scheduled yet.</p>
    @endif
</div>
@endsection
