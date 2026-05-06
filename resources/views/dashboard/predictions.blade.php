@extends('dashboard.layout')

@section('title', 'My Predictions - Dashboard')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">My Predictions</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Track your monthly and yearly prediction reports</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">Predictions History</h2>
        <a href="{{ route('predictions.index') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
            Order New Prediction
        </a>
    </div>

    @if($predictions->count() > 0)
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Date Ordered</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Payment</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Report</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($predictions as $prediction)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-4 font-medium">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $prediction->type === 'yearly' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($prediction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $prediction->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4 font-semibold">₹{{ number_format($prediction->amount, 2) }}</td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $prediction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($prediction->payment_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $prediction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $prediction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $prediction->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($prediction->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($prediction->report)
                                    <span class="text-green-600 font-medium text-sm">Available</span>
                                @else
                                    <span class="text-gray-400 text-sm">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-3">
            @foreach($predictions as $prediction)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $prediction->type === 'yearly' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($prediction->type) }}
                        </span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            {{ $prediction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $prediction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($prediction->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm">₹{{ number_format($prediction->amount, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $prediction->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs">
                                Payment: <span class="font-medium {{ $prediction->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ ucfirst($prediction->payment_status) }}</span>
                            </div>
                            <div class="text-xs">
                                Report: <span class="font-medium {{ $prediction->report ? 'text-green-600' : 'text-gray-400' }}">{{ $prediction->report ? 'Available' : 'Pending' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $predictions->links() }}
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-4xl mb-4">🔮</div>
            <h3 class="text-lg font-bold text-gray-600 mb-2">No Predictions Yet</h3>
            <p class="text-gray-500 mb-4">Order your first monthly or yearly prediction report.</p>
            <a href="{{ route('predictions.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Get Predictions</a>
        </div>
    @endif
</div>
@endsection
