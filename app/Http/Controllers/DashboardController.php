<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Dashboard data for authenticated user
        $data = [
            'user' => $user,
            'stats' => $this->getUserStats($user),
        ];

        return Inertia::render('Dashboard', $data);
    }

    private function getUserStats($user)
    {
        return [
            'sessionsCount' => $user->voiceflowSessions()->count(),
            'activeSessionsCount' => $user->voiceflowSessions()->where('status', 'ACTIVE')->count(),
            'completedSessionsCount' => $user->voiceflowSessions()->where('status', 'COMPLETED')->count(),
        ];
    }
}
