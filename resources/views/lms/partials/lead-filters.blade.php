{{-- LMS Lead Filters Partial --}}
{{-- Can be included via @include('lms.partials.lead-filters') --}}
{{-- Searches full_name, email, phone_number; filters by status, source, date range --}}

<div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('lms.leads.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search Input -->
            <div>
                <label for="filter-search" class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                <input type="text"
                       id="filter-search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Name, email, or phone..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>

            <!-- Status Dropdown -->
            <div>
                <label for="filter-status" class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select id="filter-status"
                        name="status"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>

            <!-- Source Dropdown -->
            <div>
                <label for="filter-source" class="block text-xs font-medium text-gray-600 mb-1">Source</label>
                <select id="filter-source"
                        name="source"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">All Sources</option>
                    @php
                        $sources = \App\Models\CampaignLead::select('source')
                            ->whereNotNull('source')
                            ->where('source', '!=', '')
                            ->distinct()
                            ->orderBy('source')
                            ->pluck('source');
                    @endphp
                    @foreach($sources as $source)
                        <option value="{{ $source }}" {{ request('source') === $source ? 'selected' : '' }}>
                            {{ ucfirst(str_replace(['-', '_'], ' ', $source)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="filter-date-from" class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                <input type="date"
                       id="filter-date-from"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>

            <!-- Date To -->
            <div>
                <label for="filter-date-to" class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                <input type="date"
                       id="filter-date-to"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Apply Filters
            </button>
            <a href="{{ route('lms.leads.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>
