<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing modules to avoid constraint violations
        Module::truncate();
        $modules = [
            [
                'title' => 'Understanding Your PI Behavioral Pattern',
                'slug' => 'understanding-pi-behavioral-pattern',
                'type' => 'assessment',
                'description' => 'Explore your Predictive Index behavioral drives and learn how they influence your leadership style and decision-making.',
                'topics' => ['behavioral drives', 'self-awareness', 'dominance', 'extraversion', 'patience', 'formality'],
                'learning_objectives' => 'Understand your unique PI pattern, recognize how your behavioral drives impact your interactions, and learn to leverage your natural strengths in leadership situations.',
                'estimated_duration' => 45,
                'difficulty' => 'beginner',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Situational Leadership Fundamentals',
                'slug' => 'situational-leadership-fundamentals',
                'type' => 'coaching',
                'description' => 'Master the core principles of Situational Leadership and learn to adapt your leadership style based on follower readiness.',
                'topics' => ['situational leadership', 'follower readiness', 'directing', 'coaching', 'supporting', 'delegating'],
                'learning_objectives' => 'Learn the four leadership styles, assess follower development levels, and apply the appropriate leadership approach for maximum effectiveness.',
                'estimated_duration' => 60,
                'difficulty' => 'beginner',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Situational Self Leadership (SSL)',
                'slug' => 'situational-self-leadership',
                'type' => 'coaching',
                'description' => 'Develop self-leadership skills by understanding how to adapt your approach based on your competence and commitment levels.',
                'topics' => ['self-leadership', 'competence assessment', 'commitment levels', 'self-direction', 'goal achievement'],
                'learning_objectives' => 'Learn to assess your own development level for different tasks, identify what type of direction and support you need, and take ownership of your professional growth.',
                'estimated_duration' => 50,
                'difficulty' => 'intermediate',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'PI-Driven Communication Strategies',
                'slug' => 'pi-communication-strategies',
                'type' => 'training',
                'description' => 'Learn to communicate effectively with different PI behavioral patterns and adapt your communication style for maximum impact.',
                'topics' => ['behavioral adaptation', 'communication styles', 'influence techniques', 'conflict resolution', 'team dynamics'],
                'learning_objectives' => 'Recognize different PI patterns in others, adapt your communication approach, resolve conflicts using behavioral insights, and build stronger professional relationships.',
                'estimated_duration' => 75,
                'difficulty' => 'intermediate',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Leading Through Change with SSL',
                'slug' => 'leading-change-ssl',
                'type' => 'coaching',
                'description' => 'Apply Situational Self Leadership principles to navigate personal and organizational change effectively.',
                'topics' => ['change management', 'adaptability', 'resilience', 'self-regulation', 'goal adjustment'],
                'learning_objectives' => 'Develop change resilience, learn to adjust your leadership approach during transitions, and help others navigate change using SSL principles.',
                'estimated_duration' => 65,
                'difficulty' => 'advanced',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Building High-Performance Teams with PI',
                'slug' => 'high-performance-teams-pi',
                'type' => 'training',
                'description' => 'Use Predictive Index insights to build, lead, and optimize team performance by understanding team behavioral dynamics.',
                'topics' => ['team composition', 'behavioral diversity', 'conflict resolution', 'performance optimization', 'team leadership'],
                'learning_objectives' => 'Learn to analyze team behavioral composition, identify potential conflict areas, leverage behavioral diversity, and create strategies for sustained high performance.',
                'estimated_duration' => 90,
                'difficulty' => 'advanced',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Advanced Situational Leadership Applications',
                'slug' => 'advanced-situational-leadership',
                'type' => 'coaching',
                'description' => 'Master advanced Situational Leadership techniques for complex organizational challenges and cross-functional team leadership.',
                'topics' => ['advanced leadership styles', 'organizational complexity', 'cross-functional leadership', 'remote team leadership', 'leadership flexibility'],
                'learning_objectives' => 'Apply Situational Leadership in complex scenarios, lead diverse and remote teams effectively, and develop advanced diagnostic skills for follower readiness assessment.',
                'estimated_duration' => 85,
                'difficulty' => 'advanced',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }

        $this->command->info('Created ' . count($modules) . ' modules successfully.');
    }
}