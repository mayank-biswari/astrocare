@extends('layouts.app')

@section('title', 'Ask a Question - Get Astrological Guidance')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Ask Your Question</h1>
            <p class="text-xl text-gray-600">Get personalized astrological guidance for your life questions</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('ask.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" name="dob" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time of Birth</label>
                        <input type="time" name="time" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth</label>
                        <input type="text" name="place" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Category</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        <option value="career">Career & Business</option>
                        <option value="love">Love & Relationships</option>
                        <option value="marriage">Marriage & Family</option>
                        <option value="health">Health & Wellness</option>
                        <option value="finance">Finance & Money</option>
                        <option value="education">Education & Studies</option>
                        <option value="general">General Life Guidance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Question</label>
                    <textarea name="question" rows="5" required 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Please describe your question in detail. The more specific you are, the better guidance we can provide."></textarea>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-bold text-blue-800 mb-2">Service Details:</h3>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Detailed written response within 24-48 hours</li>
                        <li>• Personalized astrological analysis</li>
                        <li>• Practical remedies and suggestions</li>
                        <li>• Follow-up support via email</li>
                    </ul>
                </div>

                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600 mb-4">₹499</div>
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 text-lg">
                        Submit Question & Pay
                    </button>
                </div>
            </form>
        </div>

        <!-- FAQ Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-center mb-8">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold mb-2">How long does it take to get an answer?</h3>
                    <p class="text-gray-600">You will receive a detailed written response within 24-48 hours of submitting your question.</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold mb-2">Can I ask multiple questions?</h3>
                    <p class="text-gray-600">Each submission covers one main question. For multiple questions, please submit separate forms.</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold mb-2">Do I need exact birth time?</h3>
                    <p class="text-gray-600">While exact birth time helps provide more accurate predictions, we can still provide guidance with approximate time or date only.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection