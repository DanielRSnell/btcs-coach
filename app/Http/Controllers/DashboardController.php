<?php

namespace App\Http\Controllers;

use App\Models\ActionItem;
use App\Models\Achievement;
use App\Models\CoachingSession;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $data = [
            'user' => $user->load('achievements', 'actionItems'),
            'stats' => $this->getUserStats($user),
            'recentSessions' => $this->getRecentSessions($user),
            'pendingActionItems' => $this->getPendingActionItems($user),
            'availableModules' => $this->getAvailableModules($user),
            'recentAchievements' => $this->getRecentAchievements($user),
        ];

        return Inertia::render('Dashboard', $data);
    }

    private function getUserStats($user)
    {
        return [
            'totalSessions' => $user->coachingSessions()->count(),
            'completedSessions' => $user->coachingSessions()->where('status', 'completed')->count(),
            'totalAchievements' => $user->achievements()->where('is_unlocked', true)->count(),
            'totalPoints' => $user->achievements()->where('is_unlocked', true)->sum('points'),
            'pendingActionItems' => $user->actionItems()->whereIn('status', ['pending', 'in_progress'])->count(),
            'completedActionItems' => $user->actionItems()->where('status', 'completed')->count(),
            'assignedModules' => $user->accessibleModules()->count(),
        ];
    }

    private function getRecentSessions($user)
    {
        return $user->coachingSessions()
            ->with('module')
            ->latest()
            ->limit(5)
            ->get();
    }

    private function getPendingActionItems($user)
    {
        return $user->actionItems()
            ->with('coachingSession')
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();
    }

    private function getAvailableModules($user)
    {
        return $user->accessibleModules()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    private function getRecentAchievements($user)
    {
        return $user->achievements()
            ->where('is_unlocked', true)
            ->latest('unlocked_at')
            ->limit(5)
            ->get();
    }
}
