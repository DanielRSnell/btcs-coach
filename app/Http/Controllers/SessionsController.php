<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                'has_pi_assessment' => $user->hasPiAssessment(),
                'has_pi_profile' => $user->hasPiProfile(),
            ],
            'sessions' => $sessions,
        ]);
    }
}
