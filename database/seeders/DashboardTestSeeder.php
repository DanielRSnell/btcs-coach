<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use App\Models\CoachingSession;
use App\Models\ActionItem;
use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing user to have admin role
        $user = User::where('email', 'john@btcs.com')->first();
        if ($user) {
            $user->update(['role' => 'admin']);
        }

        // Create sample modules
        $modules = [
            [
                'title' => 'Leadership Fundamentals',
                'description' => 'Learn the core principles of effective leadership and team management.',
                'slug' => 'leadership-fundamentals',
                'type' => 'coaching',
                'topics' => ['leadership', 'team-management', 'communication'],
                'learning_objectives' => 'Understand leadership styles, develop communication skills, learn delegation techniques.',
                'estimated_duration' => 60,
                'difficulty' => 'beginner',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Advanced Sales Techniques',
                'description' => 'Master advanced sales strategies and customer relationship building.',
                'slug' => 'advanced-sales-techniques',
                'type' => 'training',
                'topics' => ['sales', 'customer-relations', 'negotiation'],
                'learning_objectives' => 'Learn consultative selling, objection handling, and closing techniques.',
                'estimated_duration' => 90,
                'difficulty' => 'advanced',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Time Management Mastery',
                'description' => 'Optimize your productivity and manage time effectively.',
                'slug' => 'time-management-mastery',
                'type' => 'coaching',
                'topics' => ['productivity', 'planning', 'prioritization'],
                'learning_objectives' => 'Learn prioritization frameworks, time blocking, and productivity systems.',
                'estimated_duration' => 45,
                'difficulty' => 'intermediate',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($modules as $moduleData) {
            $module = Module::create($moduleData);
            
            // Assign module to user
            if ($user) {
                $user->accessibleModules()->attach($module->id, [
                    'assigned_at' => now(),
                    'progress_data' => json_encode(['completion_percentage' => rand(0, 100)]),
                ]);
            }
        }

        if ($user) {
            // Create sample coaching sessions
            $sessions = [
                [
                    'user_id' => $user->id,
                    'module_id' => Module::first()->id,
                    'session_id' => Str::uuid(),
                    'topic' => 'Leadership Assessment',
                    'summary' => 'Initial leadership skills assessment and goal setting.',
                    'duration' => 30,
                    'interactions' => 15,
                    'status' => 'completed',
                    'satisfaction_score' => 4.5,
                    'started_at' => now()->subDays(7),
                    'completed_at' => now()->subDays(7)->addMinutes(30),
                ],
                [
                    'user_id' => $user->id,
                    'module_id' => Module::skip(1)->first()->id,
                    'session_id' => Str::uuid(),
                    'topic' => 'Sales Strategy Planning',
                    'summary' => 'Developed personalized sales approach and identified key opportunities.',
                    'duration' => 45,
                    'interactions' => 22,
                    'status' => 'completed',
                    'satisfaction_score' => 4.8,
                    'started_at' => now()->subDays(3),
                    'completed_at' => now()->subDays(3)->addMinutes(45),
                ],
                [
                    'user_id' => $user->id,
                    'session_id' => Str::uuid(),
                    'topic' => 'Progress Check-in',
                    'summary' => null,
                    'duration' => null,
                    'interactions' => 5,
                    'status' => 'active',
                    'started_at' => now()->subHours(2),
                ],
            ];

            foreach ($sessions as $sessionData) {
                CoachingSession::create($sessionData);
            }

            // Get modules for action items
            $modules = Module::all();
            $coreCoachingModule = $modules->where('slug', 'core-coaching-scenarios')->first();
            $performanceModule = $modules->where('slug', 'performance-growth-cycle')->first();
            
            // Create sample action items
            $actionItems = [
                [
                    'user_id' => $user->id,
                    'module_id' => $coreCoachingModule?->id,
                    'coaching_session_id' => CoachingSession::first()->id,
                    'title' => 'Schedule team one-on-ones',
                    'description' => 'Set up individual meetings with each team member this week.',
                    'priority' => 'high',
                    'status' => 'pending',
                    'due_date' => now()->addDays(3),
                ],
                [
                    'user_id' => $user->id,
                    'module_id' => $performanceModule?->id,
                    'coaching_session_id' => CoachingSession::skip(1)->first()->id,
                    'title' => 'Research competitor pricing',
                    'description' => 'Analyze competitor pricing strategies for Q4 planning.',
                    'priority' => 'medium',
                    'status' => 'in_progress',
                    'due_date' => now()->addWeek(),
                ],
                [
                    'user_id' => $user->id,
                    'module_id' => $coreCoachingModule?->id,
                    'title' => 'Update project documentation',
                    'description' => 'Revise project documentation to reflect recent changes.',
                    'priority' => 'low',
                    'status' => 'completed',
                    'completed_at' => now()->subDays(2),
                ],
            ];

            foreach ($actionItems as $itemData) {
                ActionItem::create($itemData);
            }

            // Create sample achievements
            $achievements = [
                [
                    'user_id' => $user->id,
                    'module_id' => Module::first()->id,
                    'title' => 'First Steps',
                    'description' => 'Completed your first coaching session!',
                    'type' => 'completion',
                    'points' => 100,
                    'badge_icon' => 'star',
                    'badge_color' => '#f59e0b',
                    'is_unlocked' => true,
                    'unlocked_at' => now()->subDays(7),
                    'progress_percentage' => 100.00,
                ],
                [
                    'user_id' => $user->id,
                    'title' => 'Task Master',
                    'description' => 'Completed 5 action items successfully.',
                    'type' => 'milestone',
                    'points' => 250,
                    'badge_icon' => 'target',
                    'badge_color' => '#10b981',
                    'is_unlocked' => true,
                    'unlocked_at' => now()->subDays(2),
                    'progress_percentage' => 100.00,
                ],
                [
                    'user_id' => $user->id,
                    'module_id' => Module::skip(1)->first()->id,
                    'title' => 'Sales Champion',
                    'description' => 'Achieved excellent performance in sales training.',
                    'type' => 'score',
                    'points' => 500,
                    'badge_icon' => 'trophy',
                    'badge_color' => '#3b82f6',
                    'is_unlocked' => false,
                    'progress_percentage' => 75.00,
                ],
            ];

            foreach ($achievements as $achievementData) {
                Achievement::create($achievementData);
            }
        }
    }
}
