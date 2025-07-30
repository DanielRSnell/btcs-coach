<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use App\Models\CoachingSession;
use Illuminate\Database\Seeder;

class CoachingSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing sessions
        CoachingSession::truncate();

        // Get John Doe user
        $johnDoe = User::where('email', 'john@btcs.com')->first();
        if (!$johnDoe) {
            return;
        }

        // Get all modules
        $modules = Module::all();

        $sessions = [
            [
                'user_id' => $johnDoe->id,
                'module_id' => $modules->where('slug', 'understanding-pi-behavioral-pattern')->first()?->id,
                'session_id' => 'vf_session_001',
                'topic' => 'Understanding My PI Behavioral Pattern',
                'summary' => 'Completed PI assessment and discovered I have a strong Dominance drive with moderate Extraversion. Learned how this affects my leadership approach and communication style.',
                'duration' => 28,
                'interactions' => 15,
                'status' => 'completed',
                'satisfaction_score' => 4.8,
                'started_at' => now()->subDays(3)->subHours(2),
                'completed_at' => now()->subDays(3)->subHours(1)->subMinutes(32),
                'voiceflow_data' => json_encode([
                    'pi_pattern' => 'Dominance-Extraversion',
                    'key_insights' => ['Direct communication style', 'Results-oriented approach', 'Comfortable with risk-taking'],
                    'action_items' => ['Practice active listening', 'Consider team input before decisions']
                ])
            ],
            [
                'user_id' => $johnDoe->id,
                'module_id' => $modules->where('slug', 'situational-leadership-fundamentals')->first()?->id,
                'session_id' => 'vf_session_002',
                'topic' => 'Situational Leadership Fundamentals',
                'summary' => 'Explored the four leadership styles (Directing, Coaching, Supporting, Delegating) and learned to assess follower readiness levels.',
                'duration' => 35,
                'interactions' => 22,
                'status' => 'completed',
                'satisfaction_score' => 4.6,
                'started_at' => now()->subDays(1)->subHours(3),
                'completed_at' => now()->subDays(1)->subHours(2)->subMinutes(25),
                'voiceflow_data' => json_encode([
                    'leadership_styles_practiced' => ['Coaching', 'Supporting'],
                    'follower_scenarios' => 3,
                    'confidence_level' => 'intermediate'
                ])
            ],
            [
                'user_id' => $johnDoe->id,
                'module_id' => $modules->where('slug', 'pi-communication-strategies')->first()?->id,
                'session_id' => 'vf_session_003',
                'topic' => 'PI-Driven Communication Strategies',
                'summary' => 'Learned to adapt communication style based on team members\' PI patterns. Practiced scenarios with different behavioral drives.',
                'duration' => 42,
                'interactions' => 28,
                'status' => 'completed',
                'satisfaction_score' => 4.9,
                'started_at' => now()->subHours(5),
                'completed_at' => now()->subHours(4)->subMinutes(18),
                'voiceflow_data' => json_encode([
                    'communication_scenarios' => 5,
                    'pi_patterns_covered' => ['Analyzer', 'Promoter', 'Controller', 'Supporter'],
                    'improvement_areas' => ['Patience with analytical types', 'Providing structure for supporters']
                ])
            ],
            [
                'user_id' => $johnDoe->id,
                'module_id' => $modules->where('slug', 'situational-self-leadership')->first()?->id,
                'session_id' => 'vf_session_004',
                'topic' => 'Situational Self Leadership (SSL)',
                'summary' => 'Currently exploring self-leadership techniques and personal development strategies based on situational contexts.',
                'duration' => 18,
                'interactions' => 12,
                'status' => 'active',
                'started_at' => now()->subMinutes(18),
                'voiceflow_data' => json_encode([
                    'self_assessment_completed' => true,
                    'current_focus' => 'Goal setting and accountability',
                    'progress' => '60%'
                ])
            ]
        ];

        foreach ($sessions as $sessionData) {
            CoachingSession::create($sessionData);
        }

        echo "Created " . count($sessions) . " coaching sessions for John Doe.\n";
    }
}