<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing achievements
        Achievement::truncate();

        // Get John Doe user
        $johnDoe = User::where('email', 'john@btcs.com')->first();
        if (!$johnDoe) {
            return;
        }

        // Create PI/SSL contextual achievements for John Doe
        $achievements = [
            [
                'user_id' => $johnDoe->id,
                'title' => 'PI Pattern Discovery',
                'description' => 'Successfully completed your initial Predictive Index behavioral assessment and identified your behavioral pattern.',
                'type' => 'completion',
                'badge_color' => '#4F46E5',
                'points' => 100,
                'criteria' => json_encode(['complete_pi_assessment' => true]),
                'progress_percentage' => 100.00,
                'is_unlocked' => true,
                'unlocked_at' => now()->subDays(3)
            ],
            [
                'user_id' => $johnDoe->id,
                'title' => 'Communication Adapter',
                'description' => 'Demonstrated ability to adapt communication style based on team members\' PI patterns in coaching scenarios.',
                'type' => 'milestone',
                'badge_color' => '#059669',
                'points' => 150,
                'criteria' => json_encode([
                    'complete_communication_module' => true,
                    'practice_scenarios' => 5,
                    'satisfaction_score' => 4.5
                ]),
                'progress_percentage' => 100.00,
                'is_unlocked' => true,
                'unlocked_at' => now()->subHours(18)
            ],
            [
                'user_id' => $johnDoe->id,
                'title' => 'Situational Leadership Fundamentals',
                'description' => 'Mastered the four leadership styles and can assess follower readiness levels effectively.',
                'type' => 'milestone',
                'badge_color' => '#DC2626',
                'points' => 200,
                'criteria' => json_encode([
                    'complete_ssl_fundamentals' => true,
                    'leadership_scenarios' => 8,
                    'confidence_rating' => 4.0
                ]),
                'progress_percentage' => 100.00,
                'is_unlocked' => true,
                'unlocked_at' => now()->subDays(1)
            ],
            [
                'user_id' => $johnDoe->id,
                'title' => 'Active Learning Champion',
                'description' => 'Completed 3 consecutive coaching sessions with high engagement and satisfaction scores.',
                'type' => 'streak',
                'badge_color' => '#7C3AED',
                'points' => 75,
                'criteria' => json_encode([
                    'consecutive_sessions' => 3,
                    'average_satisfaction' => 4.5,
                    'engagement_interactions' => 15
                ]),
                'progress_percentage' => 100.00,
                'is_unlocked' => true,
                'unlocked_at' => now()->subHours(5)
            ],
            [
                'user_id' => $johnDoe->id,
                'title' => 'Self-Leadership Starter',
                'description' => 'Began your Situational Self Leadership journey with goal setting and self-assessment.',
                'type' => 'completion',
                'badge_color' => '#EA580C',
                'points' => 50,
                'criteria' => json_encode([
                    'start_ssl_module' => true,
                    'complete_self_assessment' => true,
                    'set_goals' => 3
                ]),
                'progress_percentage' => 80.00,
                'is_unlocked' => false,
                'unlocked_at' => null
            ]
        ];

        // Create achievements directly
        foreach ($achievements as $achievementData) {
            Achievement::create($achievementData);
        }

        echo "Created " . count($achievements) . " contextual achievements for John Doe.\n";
    }
}