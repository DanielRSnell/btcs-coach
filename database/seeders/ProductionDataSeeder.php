<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use App\Models\ActionItem;
use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds for production deployment
     * This includes all current modules, users, and action items from development
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminUser = User::where('email', 'admin@btcs.com')->first();
        if ($adminUser) {
            $this->command->info("Admin user already exists with role: {$adminUser->role}");
            
            // Ensure admin has correct role
            if ($adminUser->role !== 'admin') {
                $adminUser->update(['role' => 'admin']);
                $this->command->info('Updated admin user role to admin.');
            }
            
            return;
        }

        $this->command->info('Creating production data...');

        // Create main users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@btcs.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@btcs.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Victor Morales',
                'email' => 'victor@umbral.ai',
                'password' => Hash::make('password'),
                'role' => 'member',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Matt Putman',
                'email' => 'matt.putman@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'email_verified_at' => now(),
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[$userData['email']] = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create all current modules
        $modules = [
            [
                'title' => 'Factual & Policy Retrieval',
                'description' => 'Demonstrate the agent\'s reliability for finding specific, objective information from the knowledge base.',
                'slug' => 'factual-policy-retrieval',
                'type' => 'coaching',
                'topics' => ['Company policies', 'Employee benefits', 'Internal processes', 'Employee handbook', 'Knowledge base retrieval'],
                'learning_objectives' => 'Test the agent\'s ability to accurately retrieve and summarize company policies, benefits, and procedures from documentation.',
                'expected_outcomes' => 'A direct, concise summary of the policy or program, likely pulled from the Employee Handbook. The agent should cite the source document.',
                'estimated_duration' => 5,
                'difficulty' => 'beginner',
                'sample_questions' => [
                    'What is the company\'s policy on working from home or flexible schedules?',
                    'Can you tell me about the apartment discount program for employees?',
                    'What are the key steps for submitting an internal job interest form?'
                ],
                'goal' => 'Demonstrate the agent\'s reliability for finding specific, objective information from the knowledge base.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Personalization & Self-Awareness (Predictive Index)',
                'description' => 'Showcase the agent\'s ability to handle personalized data and connect it to practical application.',
                'slug' => 'personalization-predictive-index',
                'type' => 'coaching',
                'topics' => ['Predictive Index profiles', 'Behavioral drives', 'Personal strengths', 'PI application', 'Self-awareness coaching'],
                'learning_objectives' => 'Evaluate the agent\'s capability to retrieve personal PI data and provide actionable, personalized coaching advice.',
                'expected_outcomes' => 'For the first question, it should retrieve the correct PI profile and summarize its behavioral drives. For the application question, it should go beyond the summary and provide actionable advice, linking the Analyzer\'s need for data and introspection to a concrete action (e.g., preparing a data-driven agenda).',
                'estimated_duration' => 10,
                'difficulty' => 'intermediate',
                'sample_questions' => [
                    'What\'s my Predictive Index profile? Can you tell me my key strengths?',
                    'My PI profile is an Analyzer. How can I use this knowledge to better prepare for my one-on-one meetings with my manager?'
                ],
                'goal' => 'Showcase the agent\'s ability to handle personalized data and connect it to practical application.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Core Coaching Scenarios',
                'description' => 'Demonstrate the agent\'s capability: synthesizing multiple frameworks—PI, SLII, and Courageous Conversations—to solve a complex, human-centric problem.',
                'slug' => 'core-coaching-scenarios',
                'type' => 'coaching',
                'topics' => ['Manager coaching', 'Employee coaching', 'Performance management', 'SLII framework', 'Courageous Conversations', 'PI-based coaching'],
                'learning_objectives' => 'Test the agent\'s ability to synthesize multiple coaching frameworks to provide comprehensive, personalized coaching advice.',
                'expected_outcomes' => 'This is the most important test. The agent should not give generic advice. A great response will: Acknowledge the situation with empathy. Diagnose using the frameworks: "Based on SLII, Sarah sounds like a D3 (Capable but Cautious Performer) on this task..." "Since your manager is a Controller, their need for data and control is high..." Structure the advice using the Courageous Conversations model (state facts, share intent, listen, partner on a solution). Tailor the advice to the PI profile (e.g., "For a Collaborator, be sure to start by reinforcing your trust in them as a person before discussing the task."). Cite the source documents it used for the frameworks.',
                'estimated_duration' => 20,
                'difficulty' => 'advanced',
                'sample_questions' => [
                    'I need help preparing for a talk with my direct report, Sarah. She\'s been missing deadlines on the new marketing campaign. Her work quality is still good when she turns it in, but her timeliness is slipping. Her PI is a Collaborator. How should I handle this conversation?',
                    'My manager is micromanaging me. I\'m leading the \'Project Phoenix\' roll-out, and I feel confident, but he asks for updates multiple times a day. It\'s slowing me down. I know he is a Controller PI. How can I ask for more autonomy without sounding like I\'m complaining?'
                ],
                'goal' => 'Demonstrate the agent\'s capability: synthesizing multiple frameworks—PI, SLII, and Courageous Conversations—to solve a complex, human-centric problem.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Performance & Growth Cycle',
                'description' => 'Show how the agent supports specific, high-value business processes like performance reviews.',
                'slug' => 'performance-growth-cycle',
                'type' => 'coaching',
                'topics' => ['Performance reviews', 'Compensation planning', 'Bonus criteria', 'Annual compensation toolkit', 'Performance evaluation'],
                'learning_objectives' => 'Test the agent\'s ability to find and synthesize specific criteria from detailed business process documents.',
                'expected_outcomes' => 'The agent should pull very specific criteria from the 2025 Annual Compensation & Bonus Toolkit and Performance Bonus Plan Criteria documents, demonstrating it can find granular details in long-form documents.',
                'estimated_duration' => 15,
                'difficulty' => 'intermediate',
                'sample_questions' => [
                    'I\'m getting ready for my annual compensation and bonus meeting for my team. Can you summarize the key factors I should consider when determining compensation changes, according to the toolkit?',
                    'My employee really knocked it out of the park and innovated a new process this year. According to the "Performance Bonus Plan Criteria", what kind of bonus percentage could that justify?'
                ],
                'goal' => 'Show how the agent supports specific, high-value business processes like performance reviews.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Advanced Team & Culture Application',
                'description' => 'Test the agent\'s ability to think more abstractly and connect high-level concepts.',
                'slug' => 'advanced-team-culture-application',
                'type' => 'coaching',
                'topics' => ['Team dynamics', 'Culture integration', 'PI profile interactions', 'Leadership strategies', 'Company values', 'Team building'],
                'learning_objectives' => 'Evaluate the agent\'s sophisticated ability to connect multiple concepts and provide nuanced leadership advice.',
                'expected_outcomes' => 'A sophisticated ability to connect the dots. It should describe the clash between the PI profiles (e.g., the Maverick\'s risk-taking vs. the Specialist\'s caution) and then offer a concrete leadership strategy, grounding its advice in a Fenton value.',
                'estimated_duration' => 25,
                'difficulty' => 'advanced',
                'sample_questions' => [
                    'I\'m leading a new project team. I have a Maverick, a Specialist, and a Promoter. What potential friction points should I anticipate and how can I use the SLII model to lead them effectively as a group?',
                    'Our company value is to "beVULNERABLE". How can I apply that principle when I need to give my peer some difficult feedback about their part of our shared project?'
                ],
                'goal' => 'Test the agent\'s ability to think more abstractly and connect high-level concepts.',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        $createdModules = [];
        foreach ($modules as $moduleData) {
            $createdModules[$moduleData['slug']] = Module::firstOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );
        }

        // Assign modules to users (only if not already assigned)
        $johnUser = $createdUsers['john@btcs.com'];
        foreach ($createdModules as $module) {
            if (!$johnUser->accessibleModules()->where('module_id', $module->id)->exists()) {
                $johnUser->accessibleModules()->attach($module->id, [
                    'assigned_at' => now(),
                    'progress_data' => json_encode(['completion_percentage' => rand(0, 100)]),
                ]);
            }
        }

        // Create action items matching current development data
        $actionItems = [
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Practice Active Listening with Team Members',
                'description' => 'Focus on demonstrating active listening skills during team meetings and one-on-ones.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->addDays(7),
                'context' => 'Based on coaching session feedback about communication improvements',
                'completed_at' => now()->subDays(2),
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Adapt Communication Style for Analytical Team Members',
                'description' => 'Use data-driven communication approaches when working with team members who have analytical PI profiles.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(10),
                'context' => 'Identified during PI profile analysis session',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Assess Team Member Readiness Levels for Current Project',
                'description' => 'Evaluate each team member using the SLII framework to determine their development level for current project tasks.',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(5),
                'context' => 'Preparation for upcoming project milestone review',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Complete Personal Leadership Goals Review',
                'description' => 'Review and update personal leadership development goals for the quarter.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addWeeks(2),
                'context' => 'Quarterly development planning session',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Schedule Follow-up PI Assessment',
                'description' => 'Arrange follow-up PI assessment session to track development progress.',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addMonth(),
                'context' => '6-month follow-up recommendation from initial assessment',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Complete Initial PI Behavioral Assessment',
                'description' => 'Finish the comprehensive Predictive Index behavioral assessment.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subWeek(),
                'context' => 'Required for personalized coaching approach',
                'completed_at' => now()->subWeek(),
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Schedule team one-on-ones',
                'description' => 'Set up individual meetings with each team member this week.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->addDays(3),
                'context' => 'Follow-up from coaching session on team management',
                'completed_at' => now()->subDays(1),
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Research competitor pricing',
                'description' => 'Analyze competitor pricing strategies for Q4 planning.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addWeek(),
                'context' => 'Strategic planning initiative',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Update project documentation',
                'description' => 'Revise project documentation to reflect recent changes.',
                'priority' => 'low',
                'status' => 'completed',
                'due_date' => now()->subDays(1),
                'context' => 'Documentation maintenance task',
                'completed_at' => now()->subDays(2),
            ],
        ];

        foreach ($actionItems as $itemData) {
            ActionItem::create($itemData);
        }

        // Create sample achievements
        $achievements = [
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
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
                'user_id' => $johnUser->id,
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
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'PI Champion',
                'description' => 'Mastered Predictive Index applications in coaching.',
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

        $this->command->info('Production data seeding completed successfully!');
        $this->command->info('Created users:');
        foreach ($createdUsers as $email => $user) {
            $this->command->info("- {$user->name} ({$email}) - Role: {$user->role}");
        }
    }
}