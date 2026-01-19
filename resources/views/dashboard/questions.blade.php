@extends('dashboard.layout')

@section('dashboard-content')
<div class="bg-white p-6 rounded-lg shadow-sm mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-2xl font-bold">My Questions</h1>
    <p class="text-white/90 mt-1">View and track your astrological questions</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Questions History</h2>
        <div class="flex gap-2">
            <select onchange="window.location.href='?status='+this.value" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Answered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
    </div>

    @if($questions->count() > 0)
        <div class="space-y-4">
            @foreach($questions as $question)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $question->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $question->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $question->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($question->status) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ ucfirst($question->category) }}</span>
                            </div>
                            <p class="text-gray-700 mb-2 line-clamp-2">{{ $question->question }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $question->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($question->amount, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if($question->status == 'completed' && $question->answer)
                                <button onclick="showAnswer({{ $question->id }})" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    View Answer
                                </button>
                            @endif
                        </div>
                    </div>
                    @if($question->status == 'completed' && $question->answer)
                        <div id="answer-{{ $question->id }}" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold mb-2">Answer:</h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $question->answer }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-question-circle text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">No questions found</p>
            <a href="{{ route('ask.question') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
