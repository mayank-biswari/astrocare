@extends('lms.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Status Count Cards --}}
    <section aria-label="Lead statistics">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
            @include('lms.partials.stats-card', [
                'title' => 'Total Leads',
                'value' => array_sum($statusCounts),
                'icon' => 'fas fa-users',
                'color' => 'gray',
                'link' => route('lms.leads.index'),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'New',
                'value' => $statusCounts['new'] ?? 0,
                'icon' => 'fas fa-user-plus',
                'color' => 'green',
                'link' => route('lms.leads.index', ['status' => 'new']),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'Contacted',
                'value' => $statusCounts['contacted'] ?? 0,
                'icon' => 'fas fa-phone',
                'color' => 'blue',
                'link' => route('lms.leads.index', ['status' => 'contacted']),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'Qualified',
                'value' => $statusCounts['qualified'] ?? 0,
                'icon' => 'fas fa-star',
                'color' => 'yellow',
                'link' => route('lms.leads.index', ['status' => 'qualified']),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'Converted',
                'value' => $statusCounts['converted'] ?? 0,
                'icon' => 'fas fa-check-circle',
                'color' => 'indigo',
                'link' => route('lms.leads.index', ['status' => 'converted']),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'Lost',
                'value' => $statusCounts['lost'] ?? 0,
                'icon' => 'fas fa-times-circle',
                'color' => 'red',
                'link' => route('lms.leads.index', ['status' => 'lost']),
            ])

            @include('lms.partials.stats-card', [
                'title' => 'Last 7 Days',
                'value' => $recentLeadsCount,
                'icon' => 'fas fa-calendar-week',
                'color' => 'purple',
            ])
        </div>
    </section>

    {{-- Recent Leads Table --}}
    <section aria-label="Recent leads" class="mb-8">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Recent Leads</h2>
                <a href="{{ route('lms.leads.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentLeads as $lead)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('lms.leads.show', $lead) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        {{ $lead->full_name }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $lead->email }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $lead->phone_number }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $lead->source ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $statusBadge = match($lead->status) {
                                            'new' => 'bg-green-100 text-green-800',
                                            'contacted' => 'bg-blue-100 text-blue-800',
                                            'qualified' => 'bg-yellow-100 text-yellow-800',
                                            'converted' => 'bg-indigo-100 text-indigo-800',
                                            'lost' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500">{{ $lead->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                                    <p>No leads yet. <a href="{{ route('lms.leads.create') }}" class="text-indigo-600 hover:text-indigo-800">Create your first lead</a>.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Follow-Ups Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upcoming Follow-Ups --}}
        <section aria-label="Upcoming follow-ups">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                        Upcoming Follow-Ups
                        @if($upcomingFollowUps->count() > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $upcomingFollowUps->count() }}
                            </span>
                        @endif
                    </h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($upcomingFollowUps as $followUp)
                        <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('lms.leads.show', $followUp->lead) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $followUp->lead->full_name }}
                                    </a>
                                    <p class="text-sm text-gray-600 mt-0.5 truncate">{{ $followUp->description }}</p>
                                </div>
                                <div class="ml-4 shrink-0 text-right">
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $followUp->scheduled_date->format('M d, Y') }}
                                    </p>
                                    @if($followUp->scheduled_date->isToday())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                            Today
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-calendar text-gray-300 text-2xl mb-2"></i>
                            <p class="text-sm">No upcoming follow-ups in the next 7 days.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Overdue Follow-Ups --}}
        <section aria-label="Overdue follow-ups">
            <div class="bg-white rounded-lg border border-red-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-200 bg-red-50">
                    <h2 class="text-lg font-semibold text-red-800">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Overdue Follow-Ups
                        @if($overdueFollowUps->count() > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $overdueFollowUps->count() }}
                            </span>
                        @endif
                    </h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($overdueFollowUps as $followUp)
                        <div class="px-5 py-3 hover:bg-red-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('lms.leads.show', $followUp->lead) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $followUp->lead->full_name }}
                                    </a>
                                    <p class="text-sm text-gray-600 mt-0.5 truncate">{{ $followUp->description }}</p>
                                </div>
                                <div class="ml-4 shrink-0 text-right">
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-calendar-times mr-1 text-red-400"></i>
                                        {{ $followUp->scheduled_date->format('M d, Y') }}
                                    </p>
                                    @php
                                        $daysOverdue = $followUp->scheduled_date->diffInDays(now());
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                        {{ $daysOverdue }} {{ Str::plural('day', $daysOverdue) }} overdue
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-green-300 text-2xl mb-2"></i>
                            <p class="text-sm">No overdue follow-ups. You're all caught up!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
