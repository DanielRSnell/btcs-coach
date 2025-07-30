<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing module assignments
        DB::table('module_user')->truncate();

        // Get John Doe user
        $johnDoe = User::where('email', 'john@btcs.com')->first();
        if (!$johnDoe) {
            return;
        }

        // Get all modules
        $modules = Module::all();

        // Assign modules to John Doe with different progress states
        $moduleAssignments = [
            // Completed modules
            [
                'module_id' => $modules->where('slug', 'understanding-pi-behavioral-pattern')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subDays(5),
                'completed_at' => now()->subDays(3),
                'progress_data' => json_encode([
                    'session_started' => now()->subDays(5),
                    'completion_percentage' => 100,
                    'quiz_score' => 92,
                    'key_takeaways' => [
                        'Identified as Dominance-Extraversion pattern',
                        'Learned about behavioral drives impact on leadership',
                        'Completed self-reflection exercises'
                    ],
                    'session_count' => 3,
                    'total_time_spent' => 85 // minutes
                ])
            ],
            [
                'module_id' => $modules->where('slug', 'situational-leadership-fundamentals')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subDays(3),
                'completed_at' => now()->subDays(1),
                'progress_data' => json_encode([
                    'session_started' => now()->subDays(3),
                    'completion_percentage' => 100,
                    'leadership_styles_practiced' => ['Directing', 'Coaching', 'Supporting', 'Delegating'],
                    'scenarios_completed' => 8,
                    'confidence_rating' => 4.2,
                    'session_count' => 2,
                    'total_time_spent' => 67
                ])
            ],
            
            // In progress modules
            [
                'module_id' => $modules->where('slug', 'pi-communication-strategies')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subDays(2),
                'completed_at' => null,
                'progress_data' => json_encode([
                    'session_started' => now()->subDays(2),
                    'completion_percentage' => 75,
                    'current_lesson' => 'Adapting to Analytical Types',
                    'scenarios_completed' => 5,
                    'scenarios_total' => 7,
                    'session_count' => 2,
                    'total_time_spent' => 52,
                    'last_activity' => now()->subHours(5)
                ])
            ],
            [
                'module_id' => $modules->where('slug', 'situational-self-leadership')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subHours(4),
                'completed_at' => null,
                'progress_data' => json_encode([
                    'session_started' => now()->subHours(4),
                    'completion_percentage' => 25,
                    'current_lesson' => 'Self-Assessment and Goal Setting',
                    'self_assessment_completed' => true,
                    'goals_set' => 3,
                    'session_count' => 1,
                    'total_time_spent' => 18,
                    'last_activity' => now()->subMinutes(15)
                ])
            ],
            
            // Assigned but not started
            [
                'module_id' => $modules->where('slug', 'leading-change-ssl')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subDays(1),
                'completed_at' => null,
                'progress_data' => json_encode([
                    'completion_percentage' => 0,
                    'status' => 'assigned',
                    'estimated_start_date' => now()->addDays(2)
                ])
            ],
            [
                'module_id' => $modules->where('slug', 'high-performance-teams-pi')->first()?->id,
                'user_id' => $johnDoe->id,
                'assigned_at' => now()->subHours(12),
                'completed_at' => null,
                'progress_data' => json_encode([
                    'completion_percentage' => 0,
                    'status' => 'assigned',
                    'estimated_start_date' => now()->addDays(3)
                ])
            ]
        ];

        foreach ($moduleAssignments as $assignment) {
            if ($assignment['module_id']) {
                DB::table('module_user')->insert([
                    'module_id' => $assignment['module_id'],
                    'user_id' => $assignment['user_id'],
                    'assigned_at' => $assignment['assigned_at'],
                    'completed_at' => $assignment['completed_at'],
                    'progress_data' => $assignment['progress_data'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        echo "Assigned " . count(array_filter($moduleAssignments, fn($a) => $a['module_id'])) . " modules to John Doe.\n";
    }
}