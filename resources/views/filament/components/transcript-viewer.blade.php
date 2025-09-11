@php
    use Carbon\Carbon;
@endphp

<div class="transcript-viewer space-y-4">
    <!-- Session Header -->
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-blue-900">
                Session: {{ $session->name ?: 'Unnamed Session' }}
            </h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($sessionData['status'] === 'ACTIVE') bg-green-100 text-green-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $sessionData['status'] ?? 'Unknown' }}
            </span>
        </div>
        <div class="text-sm text-blue-700 space-y-1">
            <div><strong>User:</strong> {{ $session->user->name }} ({{ $session->user->email }})</div>
            <div><strong>Session ID:</strong> {{ $session->session_id }}</div>
            @if(isset($sessionData['startTime']))
                <div><strong>Started:</strong> {{ Carbon::createFromTimestamp($sessionData['startTime'] / 1000)->format('M j, Y g:i A') }}</div>
            @endif
            <div><strong>Total Turns:</strong> {{ count($turns) }}</div>
        </div>
    </div>

    <!-- Conversation Flow -->
    <div class="conversation-flow space-y-3">
        @foreach($turns as $index => $turn)
            @php
                $turnNum = $index + 1;
                $type = $turn['type'] ?? 'unknown';
                $timestamp = isset($turn['timestamp']) 
                    ? Carbon::createFromTimestamp($turn['timestamp'] / 1000)
                    : null;
            @endphp
            
            <div class="turn-container border rounded-lg overflow-hidden
                @if($type === 'user') border-blue-200 bg-blue-50
                @elseif($type === 'system') border-green-200 bg-green-50
                @else border-gray-200 bg-gray-50 @endif">
                
                <!-- Turn Header -->
                <div class="turn-header px-4 py-2 border-b
                    @if($type === 'user') bg-blue-100 border-blue-200
                    @elseif($type === 'system') bg-green-100 border-green-200  
                    @else bg-gray-100 border-gray-200 @endif">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-sm
                            @if($type === 'user') text-blue-800
                            @elseif($type === 'system') text-green-800
                            @else text-gray-800 @endif">
                            Turn {{ $turnNum }} - {{ ucfirst($type) }}
                        </span>
                        @if($timestamp)
                            <span class="text-xs
                                @if($type === 'user') text-blue-600
                                @elseif($type === 'system') text-green-600
                                @else text-gray-600 @endif">
                                {{ $timestamp->format('g:i:s A') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Turn Content -->
                <div class="turn-content p-4 space-y-3">
                    @if(isset($turn['message']))
                        <!-- User Message -->
                        <div class="message user-message">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">👤</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-blue-900 mb-1">User</div>
                                    <div class="text-sm text-gray-900 whitespace-pre-wrap bg-white p-3 rounded-lg border">
                                        {{ $turn['message'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($turn['messages']) && is_array($turn['messages']))
                        @foreach($turn['messages'] as $msgIndex => $message)
                            @php
                                $msgType = $message['type'] ?? 'unknown';
                                $isAI = isset($message['ai']) && $message['ai'];
                            @endphp
                            
                            <div class="message system-message">
                                @if($msgType === 'text' && isset($message['text']))
                                    <!-- AI/System Text Message -->
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 @if($isAI) bg-purple-500 @else bg-green-500 @endif rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm">@if($isAI)🤖@else💬@endif</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium @if($isAI) text-purple-900 @else text-green-900 @endif mb-1">
                                                @if($isAI) AI Assistant @else System @endif
                                            </div>
                                            <div class="text-sm text-gray-900 whitespace-pre-wrap bg-white p-3 rounded-lg border prose prose-sm max-w-none">
                                                {!! nl2br(e($message['text'])) !!}
                                            </div>
                                        </div>
                                    </div>
                                    
                                @elseif($msgType === 'EXTENSION')
                                    <!-- Extension Message -->
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm">🔧</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-orange-900 mb-1">Extension</div>
                                            <div class="text-sm text-gray-700 bg-orange-50 p-3 rounded-lg border border-orange-200">
                                                @if(isset($message['payload']['extension']['name']))
                                                    <strong>{{ $message['payload']['extension']['name'] }}</strong>
                                                    @if(isset($message['payload']['trace']['payload']['name']))
                                                        - {{ $message['payload']['trace']['payload']['name'] }}
                                                    @endif
                                                @else
                                                    Extension Message
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                @else
                                    <!-- Other Message Types -->
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center">
                                                <span class="text-white text-sm">📋</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 mb-1">{{ ucfirst($msgType) }}</div>
                                            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border">
                                                Message type: {{ $msgType }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .transcript-viewer {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .turn-container {
        transition: all 0.2s ease;
    }
    
    .turn-container:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .message {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>