@extends('dashboard.layout')

@section('dashboard-content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">My Questions</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">View and track your astrological questions</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 gap-3">
        <h2 class="text-lg sm:text-xl font-bold">Questions History</h2>
        <div class="flex gap-2">
            <select onchange="window.location.href='?status='+this.value" class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base w-full sm:w-auto">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Answered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
    </div>

    @if($questions->count() > 0)
        <div class="space-y-3 sm:space-y-4">
            @foreach($questions as $question)
                <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium
                                    {{ $question->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $question->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $question->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($question->status) }}
                                </span>
                                <span class="text-xs sm:text-sm text-gray-500">{{ ucfirst($question->category) }}</span>
                            </div>
                            <p class="text-sm sm:text-base text-gray-700 mb-2 line-clamp-2">{{ $question->question }}</p>
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $question->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($question->amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto">
                            @if($question->status == 'completed' && $question->answer)
                                <button onclick="showAnswer({{ $question->id }})" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base text-center">
                                    View Answer
                                </button>
                            @endif
                        </div>
                    </div>
                    @if($question->status == 'completed' && $question->answer)
                        <div id="answer-{{ $question->id }}" class="hidden mt-3 sm:mt-4 p-3 sm:p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold mb-2 text-sm sm:text-base">Answer:</h4>
                            <p class="text-sm sm:text-base text-gray-700 whitespace-pre-line">{{ $question->answer }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 sm:py-12 px-4">
            <i class="fas fa-question-circle text-5xl sm:text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-base sm:text-lg mb-4">No questions found</p>
            <a href="{{ route('ask.question') }}" class="inline-block px-4 sm:px-6 py-2 sm:py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                Ask Your First Question
            </a>
        </div>
    @endif
</div>

<script>
function showAnswer(id) {
    const answerDiv = document.getElementById('answer-' + id);
    answerDiv.classList.toggle('hidden');
}
</script>
@endsection
