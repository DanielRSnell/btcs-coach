<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Module;
use App\Models\CoachingSession;
use App\Models\ActionItem;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Overview Statistics
        $totalUsers = User::count();
        $activeUsers = User::whereHas('coachingSessions', function($query) {
            $query->where('created_at', '>=', now()->subMonth());
        })->count();
        
        $totalModules = Module::where('is_active', true)->count();
        $completedSessions = CoachingSession::where('status', 'completed')->count();
        $avgSessionDuration = CoachingSession::where('status', 'completed')
            ->avg('duration') ?? 0;
        
        $totalActionItems = ActionItem::count();
        $completedActionItems = ActionItem::where('status', 'completed')->count();
        $totalAchievements = Achievement::where('is_unlocked', true)->count();

        // User Growth Data (last 6 months)
        $userGrowth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            
            $totalUsersUpToMonth = User::where('created_at', '<=', $month->endOfMonth())->count();
            $activeUsersInMonth = User::whereHas('coachingSessions', function($query) use ($month) {
                $query->whereBetween('created_at', [$month->startOfMonth(), $month->endOfMonth()]);
            })->count();
            
            $userGrowth->push([
                'month' => $monthName,
                'users' => $totalUsersUpToMonth,
                'active' => $activeUsersInMonth
            ]);
        }

        // Module Engagement Data
        $moduleEngagement = Module::where('is_active', true)
            ->withCount(['coachingSessions as sessions'])
            ->with(['users' => function($query) {
                $query->whereNotNull('module_user.completed_at');
            }])
            ->get()
            ->map(function ($module) {
                $totalAssigned = $module->users()->count();
                $completedCount = $module->users()->whereNotNull('module_user.completed_at')->count();
                $completionRate = $totalAssigned > 0 ? round(($completedCount / $totalAssigned) * 100, 1) : 0;
                
                $avgDuration = CoachingSession::where('module_id', $module->id)
                    ->where('status', 'completed')
                    ->avg('duration') ?? 0;

                return [
                    'module' => $module->title,
                    'sessions' => $module->sessions,
                    'completionRate' => $completionRate,
                    'avgDuration' => round($avgDuration, 1)
                ];
            })
            ->sortByDesc('sessions')
            ->values();

        // PI Patterns Distribution (simulated data based on common PI patterns)
        $piPatterns = [
            ['pattern' => 'High Dominance', 'count' => 12, 'percentage' => 35.3],
            ['pattern' => 'High Influence', 'count' => 8, 'percentage' => 23.5],
            ['pattern' => 'High Steadiness', 'count' => 7, 'percentage' => 20.6],
            ['pattern' => 'High Compliance', 'count' => 4, 'percentage' => 11.8],
            ['pattern' => 'Balanced Pattern', 'count' => 3, 'percentage' => 8.8]
        ];

        // Session Trends (last 14 days)
        $sessionTrends = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $sessionCount = CoachingSession::whereDate('created_at', $date->toDateString())->count();
            $avgDuration = CoachingSession::whereDate('created_at', $date->toDateString())
                ->where('status', 'completed')
                ->avg('duration') ?? 0;

            $sessionTrends->push([
                'date' => $date->format('M d'),
                'sessions' => $sessionCount,
                'duration' => round($avgDuration, 1)
            ]);
        }

        $analytics = [
            'overview' => [
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'totalModules' => $totalModules,
                'completedSessions' => $completedSessions,
                'avgSessionDuration' => round($avgSessionDuration, 1),
                'totalActionItems' => $totalActionItems,
                'completedActionItems' => $completedActionItems,
                'totalAchievements' => $totalAchievements,
            ],
            'userGrowth' => $userGrowth,
            'moduleEngagement' => $moduleEngagement,
            'piPatterns' => $piPatterns,
            'sessionTrends' => $sessionTrends
        ];

        return Inertia::render('analytics', [
            'analytics' => $analytics
        ]);
    }
}