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

        // Dashboard data for authenticated user
        $data = [
            'user' => $user->load('actionItems'),
            'stats' => $this->getUserStats($user),
            'pendingActionItems' => $this->getPendingActionItems($user),
            'availableModules' => $this->getAvailableModules($user),
        ];

        // Debug for Railway deployment
        \Log::info('Dashboard Controller - About to render Dashboard component');
        \Log::info('Manifest exists: ' . (file_exists(public_path('build/manifest.json')) ? 'YES' : 'NO'));
        if (file_exists(public_path('build/manifest.json'))) {
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            \Log::info('Dashboard in manifest: ' . (isset($manifest['resources/js/pages/Dashboard.tsx']) ? 'YES' : 'NO'));
        }

        return Inertia::render('Dashboard', $data);
    }

    private function getUserStats($user)
    {
        $assignedModules = $user->accessibleModules()->count();
        $completedModules = $user->accessibleModules()->wherePivot('completed_at', '!=', null)->count();
        $inProgressModules = $user->accessibleModules()->wherePivot('assigned_at', '!=', null)->wherePivot('completed_at', null)->count();
        
        return [
            'totalModules' => $assignedModules,
            'completedModules' => $completedModules,
            'inProgressModules' => $inProgressModules,
            'pendingActionItems' => $user->actionItems()->whereIn('status', ['pending', 'in_progress'])->count(),
            'completedActionItems' => $user->actionItems()->where('status', 'completed')->count(),
            'moduleCompletionRate' => $assignedModules > 0 ? round(($completedModules / $assignedModules) * 100) : 0,
        ];
    }


    private function getPendingActionItems($user)
    {
        return $user->actionItems()
            ->with(['module', 'coachingSession'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
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

}
