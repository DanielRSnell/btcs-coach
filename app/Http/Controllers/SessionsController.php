<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SessionsController extends Controller
{
    /**
     * Display the sessions page with full Voiceflow chat.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get user's sessions for the sidebar
        $sessions = $user->getSessions() ?? [];
        
        // Check for new session parameters
        $sessionName = $request->query('name');
        $sessionStatus = $request->query('status');
        $isAudioMode = $request->query('mode') === 'audio';
        
        return Inertia::render('Sessions', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'pi_behavioral_pattern_id' => $user->pi_behavioral_pattern_id,
                'pi_behavioral_pattern' => $user->pi_behavioral_pattern,
                'pi_raw_scores' => $user->pi_raw_scores,
                'pi_assessed_at' => $user->pi_assessed_at,
                'pi_notes' => $user->pi_notes,
                'pi_profile' => $user->pi_profile,
                'pi_chart_image' => $user->getPiChartImageUrl(),
                'has_pi_assessment' => $user->hasPiAssessment(),
                'has_pi_profile' => $user->hasPiProfile(),
            ],
            'sessions' => $sessions,
            'newSessionName' => $sessionName,
            'newSessionStatus' => $sessionStatus,
            'isAudioMode' => $isAudioMode,
        ]);
    }

    /**
     * Display a specific session by ID.
     */
    public function show(Request $request, string $sessionId): Response
    {
        $user = $request->user();
        
        // Get user's sessions for the sidebar
        $sessions = $user->getSessions() ?? [];
        
        // Find the specific session
        $currentSession = null;
        if (isset($sessions[$sessionId])) {
            $currentSession = $sessions[$sessionId];
        }
        
        $isAudioMode = $request->query('mode') === 'audio';
        
        return Inertia::render('Sessions', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'pi_behavioral_pattern_id' => $user->pi_behavioral_pattern_id,
                'pi_behavioral_pattern' => $user->pi_behavioral_pattern,
                'pi_raw_scores' => $user->pi_raw_scores,
                'pi_assessed_at' => $user->pi_assessed_at,
                'pi_notes' => $user->pi_notes,
                'pi_profile' => $user->pi_profile,
                'pi_chart_image' => $user->getPiChartImageUrl(),
                'has_pi_assessment' => $user->hasPiAssessment(),
                'has_pi_profile' => $user->hasPiProfile(),
            ],
            'sessions' => $sessions,
            'currentSessionId' => $sessionId,
            'currentSession' => $currentSession,
            'isAudioMode' => $isAudioMode,
        ]);
    }

    /**
     * Register a new session from localStorage
     */
    public function registerSession(Request $request): JsonResponse
    {
        \Log::info('📝 Session Registration Request', [
            'user_id' => $request->user()->id,
            'project_id' => $request->input('project_id'),
            'session_data' => $request->input('session_data'),
            'session_name' => $request->input('session_name'),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'project_id' => 'required|string', // This is the localStorage key (like '686331bc96acfa1dd62f6fd5')
            'session_data' => 'array',
            'session_name' => 'nullable|string|max:255'
        ]);

        $user = $request->user();
        $projectId = $request->input('project_id'); // The localStorage key
        $sessionData = $request->input('session_data', []);
        $sessionName = $request->input('session_name');

        // Extract userID from session data for logging
        $valueData = $sessionData['last_turn'] ?? $sessionData;
        $voiceflowUserID = $valueData['userID'] ?? 'unknown';
        
        \Log::info('📦 Processing session registration', [
            'user_id' => $user->id,
            'project_id' => $projectId,
            'voiceflow_user_id' => $voiceflowUserID,
            'parsed_session_data' => $sessionData
        ]);

        // Prepare session data
        $processedSessionData = array_merge([
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ], $sessionData);

        \Log::info('📋 Final session data before storage', [
            'user_id' => $user->id,
            'project_id' => $projectId,
            'voiceflow_user_id' => $voiceflowUserID,
            'final_session_data' => $processedSessionData
        ]);

        // Register the session using project_id
        $user->setSession($projectId, $processedSessionData, $sessionName);

        // Verify the session was stored
        $storedSession = $user->getSession($projectId, $voiceflowUserID);
        \Log::info('✅ Session storage verification', [
            'user_id' => $user->id,
            'project_id' => $projectId,
            'voiceflow_user_id' => $voiceflowUserID,
            'stored_successfully' => $storedSession !== null,
            'stored_session' => $storedSession,
            'all_user_sessions' => $user->getSessions()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session registered successfully',
            'session' => $processedSessionData,
            'stored_session' => $storedSession
        ]);
    }

    /**
     * Update session data from localStorage changes
     */
    public function updateSession(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'session_data' => 'required|array'
        ]);

        $user = $request->user();
        $projectId = $request->input('project_id');
        $sessionData = $request->input('session_data');
        
        // Extract userID for more precise lookup
        $valueData = $sessionData['last_turn'] ?? $sessionData;
        $voiceflowUserID = $valueData['userID'] ?? null;

        // Check if session exists
        $existingSession = $user->getSession($projectId, $voiceflowUserID);
        if (!$existingSession) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        // Update the session with the new localStorage value
        $user->updateSessionLastTurn($projectId, $valueData);
        
        // Get the updated session for response
        $updatedSession = $user->getSession($projectId, $voiceflowUserID);

        return response()->json([
            'success' => true,
            'message' => 'Session updated successfully',
            'session' => $updatedSession
        ]);
    }

    /**
     * Check if a session is registered for the user
     */
    public function checkSession(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|string',
            'voiceflow_user_id' => 'nullable|string'
        ]);

        $user = $request->user();
        $projectId = $request->input('project_id');
        $voiceflowUserID = $request->input('voiceflow_user_id');
        $session = $user->getSession($projectId, $voiceflowUserID);

        return response()->json([
            'exists' => $session !== null,
            'session' => $session
        ]);
    }

    /**
     * Submit feedback for a session
     */
    public function submitFeedback(Request $request): JsonResponse
    {
        \Log::info('📝 Session Feedback Submission Request', [
            'user_id' => $request->user()->id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'session_id' => 'required|string',
            'rating' => 'required|in:positive,negative',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = $request->user();
        $sessionId = $request->input('session_id');
        $rating = $request->input('rating');
        $comment = $request->input('comment');

        // Find the session by session_id (which is the Voiceflow userID)
        $voiceflowSession = $user->voiceflowSessions()->where('session_id', $sessionId)->first();

        if (!$voiceflowSession) {
            \Log::warning('📝 Session not found for feedback submission', [
                'user_id' => $user->id,
                'session_id' => $sessionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        // Submit feedback based on rating
        if ($rating === 'positive') {
            $voiceflowSession->setPositiveFeedback($comment);
        } else {
            $voiceflowSession->setNegativeFeedback($comment);
        }

        \Log::info('✅ Session feedback submitted successfully', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'rating' => $rating,
            'has_comment' => !empty($comment)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'feedback' => [
                'rating' => $rating,
                'comment' => $comment,
                'submitted_at' => $voiceflowSession->feedback_submitted_at->toISOString()
            ]
        ]);
    }
}
