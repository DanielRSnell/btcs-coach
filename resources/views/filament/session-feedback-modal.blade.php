<div class="space-y-4">
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold mb-2">Session Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium">Session Name:</span>
                <span class="ml-2">{{ $session->name ?: 'Unnamed Session' }}</span>
            </div>
            <div>
                <span class="font-medium">Session ID:</span>
                <span class="ml-2 font-mono text-xs">{{ $session->session_id }}</span>
            </div>
            <div>
                <span class="font-medium">Created:</span>
                <span class="ml-2">{{ $session->session_created_at?->format('M j, Y g:i A') ?: 'Unknown' }}</span>
            </div>
            <div>
                <span class="font-medium">Messages:</span>
                <span class="ml-2">{{ is_array($session->value_data) && isset($session->value_data['turns']) ? count($session->value_data['turns']) : 0 }}</span>
            </div>
        </div>
    </div>

    @if ($session->hasFeedback())
        <div class="bg-white border rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-3">User Feedback</h3>
            
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="font-medium">Rating:</span>
                    @if ($session->feedback_rating === 'positive')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                            Positive
                        </span>
                    @elseif ($session->feedback_rating === 'negative')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.106-1.79l-.05-.025A4 4 0 0011.057 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z" />
                            </svg>
                            Negative
                        </span>
                    @endif
                </div>

                @if ($session->feedback_comment)
                    <div>
                        <span class="font-medium">Comment:</span>
                        <div class="mt-1 p-3 bg-gray-50 rounded border text-sm">
                            {{ $session->feedback_comment }}
                        </div>
                    </div>
                @endif

                <div class="text-xs text-gray-500">
                    <span class="font-medium">Submitted:</span>
                    {{ $session->feedback_submitted_at?->format('M j, Y g:i A') }}
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No Feedback Available</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>This session has not received any user feedback yet.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>