@extends('lms.layouts.app')

@section('title', 'Leads')
@section('page-title', 'Leads')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Leads</h1>
        <a href="{{ route('lms.leads.create') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Create Lead
        </a>
    </div>

    <!-- Filters -->
    @include('lms.partials.lead-filters')

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'full_name', 'sort_dir' => ($filters['sort_by'] === 'full_name' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Name
                                @if($filters['sort_by'] === 'full_name')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'email', 'sort_dir' => ($filters['sort_by'] === 'email' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Email
                                @if($filters['sort_by'] === 'email')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'phone_number', 'sort_dir' => ($filters['sort_by'] === 'phone_number' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Phone
                                @if($filters['sort_by'] === 'phone_number')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'source', 'sort_dir' => ($filters['sort_by'] === 'source' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Source
                                @if($filters['sort_by'] === 'source')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'status', 'sort_dir' => ($filters['sort_by'] === 'status' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Status
                                @if($filters['sort_by'] === 'status')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <a href="{{ route('lms.leads.index', array_merge($filters, ['sort_by' => 'created_at', 'sort_dir' => ($filters['sort_by'] === 'created_at' && $filters['sort_dir'] === 'asc') ? 'desc' : 'asc'])) }}" class="inline-flex items-center hover:text-indigo-600 transition-colors">
                                Created
                                @if($filters['sort_by'] === 'created_at')
                                    <i class="fas fa-sort-{{ $filters['sort_dir'] === 'asc' ? 'up' : 'down' }} ml-1 text-indigo-600"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Follow-Up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('lms.leads.show', $lead) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $lead->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->phone_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->source ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @switch($lead->status)
                                        @case('new') bg-blue-100 text-blue-800 @break
                                        @case('contacted') bg-yellow-100 text-yellow-800 @break
                                        @case('qualified') bg-purple-100 text-purple-800 @break
                                        @case('converted') bg-green-100 text-green-800 @break
                                        @case('lost') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($lead->followUps->last())
                                    {{ $lead->followUps->last()->scheduled_date->format('M d, Y') }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-gray-300 text-3xl mb-3"></i>
                                <p class="text-sm">No leads found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($leads->hasPages())
        <div class="flex justify-center">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection
