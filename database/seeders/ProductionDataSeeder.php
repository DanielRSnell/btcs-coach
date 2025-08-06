<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use App\Models\ActionItem;
use App\Models\Achievement;
use App\Models\PiBehavioralPattern;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds for production deployment
     * This replicates the EXACT local database state including passwords
     */
    public function run(): void
    {
        $this->command->info('Creating complete production data from local database...');

        // First create PI Behavioral Patterns (all 11 patterns from PiBehavioralPatternSeeder)
        $this->seedPiBehavioralPatterns();
        
        // Check if we need to skip user creation (but continue with other data)
        $adminExists = User::where('email', 'admin@btcs.com')->exists();
        if ($adminExists) {
            $this->command->info('Admin user already exists - updating seeder to be idempotent...');
        }
        
        // Create exact users with their actual password hashes from local DB
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@btcs.com',
                'password' => '$2y$12$iD25foS5hU4yRftLNIEZRee0SnUv7knUtUbTGLv/nLfTZAvBQ3fTK', // password
                'role' => 'admin',
                'email_verified_at' => '2025-08-06 03:25:29',
                'pi_behavioral_pattern_id' => null,
                'pi_raw_scores' => null,
                'pi_assessed_at' => null,
                'pi_assessor_name' => null,
                'pi_notes' => null,
                'pi_profile' => null,
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@btcs.com',
                'password' => '$2y$12$8x7R49wHycdTlwdoivZ7kO6FqW5Om6pvbXgLYazjuIQDsM0ZjcFY2', // password
                'role' => 'admin',
                'email_verified_at' => '2025-08-06 03:25:29',
                'pi_behavioral_pattern_id' => null,
                'pi_raw_scores' => null,
                'pi_assessed_at' => null,
                'pi_assessor_name' => null,
                'pi_notes' => null,
                'pi_profile' => null,
            ],
            [
                'name' => 'Victor Morales',
                'email' => 'victor@umbral.ai',
                'password' => Hash::make('password'), // password
                'role' => 'admin',
                'email_verified_at' => '2025-08-06 03:25:29',
                'pi_behavioral_pattern_id' => null, // Will be set after patterns are created
                'pi_raw_scores' => [
                    'dominance' => 75,
                    'extraversion' => 85,
                    'patience' => 50,
                    'formality' => 25
                ],
                'pi_assessed_at' => '2021-08-04 00:00:00',
                'pi_assessor_name' => 'H.G. Fenton HR Team',
                'pi_notes' => 'A Persuader is a risk-taking, socially poised and motivating team builder.',
                'pi_profile' => [
                    'profile' => [
                        'name' => 'Victor Morales',
                        'basic_info' => [
                            'assessment_date' => '2021-08-04',
                            'report_date' => '2021-09-03',
                            'profile_type' => 'Persuader',
                            'profile_description' => 'A Persuader is a risk-taking, socially poised and motivating team builder.'
                        ],
                        'behavioral_traits' => [
                            'strongest_behaviors' => [
                                'Proactively connects quickly to others; open and sharing. Builds and leverages relationships to get work done.',
                                'Comfortably fluent and fast talk, in volume. Enthusiastically persuades and motivates others by considering their point of view and adjusting delivery.',
                                'Collaborative; usually works with and through others. Intuitive understanding of team cohesion, dynamics, and interpersonal relations.',
                                'Works at a faster-than-average pace, producing results in general accordance with schedules and \'the book.\'',
                                'Detail-oriented; typically makes and follows a plan to keep track of things and usually follows up to ensure completion.',
                                'Focused on operational efficiencies: thinks about what needs to be done and how it can be done quickly without losing quality. Impatient with routines.'
                            ],
                            'summary' => 'Victor is a congenial, friendly communicator, capable of projecting enthusiasm and warmth, and motivating others. Works at a fast pace with an emphasis on getting results by working cooperatively with and through people. Understands people well and uses that insight to motivate and persuade effectively. Functions dependably within established plans and policies, and consults trusted advisors when handling decisions outside of established norms. Takes pride in bringing out the best in others and contributing to business success.'
                        ],
                        'management_strategies' => [
                            'optimal_conditions' => [
                                'Clear definition of responsibility, authority, and organizational relationships',
                                'Specific training in the job',
                                'Opportunities for involvement, interaction, and communication with people as a major aspect of the work',
                                'Assurance of support and guidance of management, trusted advisors, or team during periods of change or new developments',
                                'Social and status recognition as rewards for achievement and demonstration of team spirit'
                            ],
                            'avoid_conditions' => [
                                'Highly repetitive, routine work with minimal social interaction',
                                'Lack of clarity in responsibilities or organizational support'
                            ]
                        ],
                        'work_preferences' => [
                            'pace' => 'Faster-than-average pace; prefers active, dynamic environments',
                            'decision_making' => 'Collaborative approach; seeks input from trusted advisors when outside established policies',
                            'communication_style' => 'Warm, persuasive, and enthusiastic communicator',
                            'focus' => 'Team-oriented with emphasis on results through collaboration',
                            'team_orientation' => 'Highly collaborative; enjoys working with and through others',
                            'risk_tolerance' => 'Moderate; prefers to mitigate risk by consulting experts when making unusual decisions'
                        ],
                        'social_style' => [
                            'formality' => 'Friendly, approachable, and confident in groups',
                            'trust_building' => 'Earns trust through openness, collaboration, and delivering results',
                            'relationship_focus' => 'Strong emphasis on building relationships to drive team and organizational success'
                        ],
                        'motivation_drivers' => [
                            'recognition' => 'High; values social and status recognition as rewards for achievement',
                            'security' => 'Moderate; values management support during change',
                            'autonomy' => 'Moderate; comfortable with autonomy when responsibilities are clearly defined',
                            'advancement' => 'Motivated by opportunities to influence and lead teams',
                            'collaboration' => 'Strong driver; thrives in team-focused environments'
                        ],
                        'technical_orientation' => [
                            'detail_orientation' => 'Moderate; follows plans and tracks details but becomes less effective if work is overly repetitive',
                            'innovation_tolerance' => 'Moderate; willing to adapt methods to improve efficiency while respecting established policies',
                            'process_adherence' => 'High; operates well within established frameworks and company policies'
                        ]
                    ]
                ],
            ],
            [
                'name' => 'Matt Putman',
                'email' => 'matt.putman@example.com',
                'password' => Hash::make('password'), // password
                'role' => 'admin',
                'email_verified_at' => '2025-08-06 03:25:30',
                'pi_behavioral_pattern_id' => null, // Will be set after patterns are created
                'pi_raw_scores' => [
                    'dominance' => 85,
                    'extraversion' => 75,
                    'patience' => 50,
                    'formality' => 50
                ],
                'pi_assessed_at' => '2016-10-21 00:00:00',
                'pi_assessor_name' => 'H.G. Fenton HR Team',
                'pi_notes' => 'A Captain is a problem solver who likes change and innovation while controlling the big picture.',
                'pi_profile' => [
                    'profile' => [
                        'name' => 'Matt Putman',
                        'basic_info' => [
                            'assessment_date' => '2016-10-21',
                            'report_date' => '2022-08-04',
                            'profile_type' => 'Captain',
                            'profile_description' => 'A Captain is a problem solver who likes change and innovation while controlling the big picture.'
                        ],
                        'behavioral_traits' => [
                            'strongest_behaviors' => [
                                'Proactivity, assertiveness, and sense of urgency in driving to reach personal goals. Openly challenges the world.',
                                'Independent in putting forth their own ideas, which are often innovative and, if implemented, cause change. Resourcefully works through or around obstacles to accomplish goals; aggressive when challenged.',
                                'Impatient for results; puts pressure on themself and others for rapid implementation, less productive when doing routine work.',
                                'Proactively connects quickly to others; open and sharing. Builds and leverages relationships to get work done.',
                                'Comfortably fluent and fast talker. Enthusiastically persuades and motivates others by considering their point of view and adjusting delivery.',
                                'Collaborative; usually works with and through others. Intuitive understanding of team cohesion, dynamics, and interpersonal relations.'
                            ],
                            'summary' => 'Matt is a confident, independent self-starter with competitive drive, initiative, a sense of urgency, and the ability to make decisions and take responsibility for them. Responds quickly to changing conditions, comes up with ideas to address them, and acts decisively. Outgoing, poised, and a lively communicator, Matt prefers fast-paced work, delegates routine details, and focuses follow-up on results rather than process. Makes quick decisions, comfortable acting without complete information, and maintains progress toward goals through adaptive course corrections. Sets high standards for achievement and is driven by ambition and new challenges.'
                        ],
                        'management_strategies' => [
                            'optimal_conditions' => [
                                'As much independence and flexibility in activities as possible',
                                'Opportunities to learn and advance',
                                'Opportunities for expression of, and action on, ideas and initiatives',
                                'Variety and challenge in responsibilities',
                                'Opportunities to demonstrate skills, and recognition and reward for doing so',
                                'Freedom from routines and repetitive details, balanced by accountability for results'
                            ],
                            'avoid_conditions' => [
                                'Highly structured, repetitive, or routine work without room for change',
                                'Micromanagement or lack of autonomy'
                            ]
                        ],
                        'work_preferences' => [
                            'pace' => 'Distinctly faster-than-average pace; thrives in high-energy, dynamic environments',
                            'decision_making' => 'Quick, confident decisions based on available information; comfortable acting without complete data',
                            'communication_style' => 'Lively, authoritative, persuasive communicator',
                            'focus' => 'Big-picture goals with flexibility on methods to achieve them',
                            'team_orientation' => 'Collaborative with trusted individuals; delegates to capable team members',
                            'risk_tolerance' => 'High; comfortable taking calculated risks and adapting as needed'
                        ],
                        'social_style' => [
                            'formality' => 'Confident, outgoing, and poised',
                            'trust_building' => 'Builds trust through decisive leadership and consistent follow-up on results',
                            'relationship_focus' => 'Values relationships that support achieving results and innovation'
                        ],
                        'motivation_drivers' => [
                            'recognition' => 'High; values recognition for achievements and demonstrated skills',
                            'security' => 'Moderate; prefers stability in leadership expectations but thrives on change in tasks',
                            'autonomy' => 'Very high; seeks independence and flexibility',
                            'advancement' => 'Strong driver; motivated by opportunities for growth and competition',
                            'collaboration' => 'Moderate; works collaboratively but values decisive action and independence'
                        ],
                        'technical_orientation' => [
                            'detail_orientation' => 'Low to moderate; prefers to delegate details and focus on big-picture outcomes',
                            'innovation_tolerance' => 'High; actively seeks and implements change and innovative solutions',
                            'process_adherence' => 'Moderate; adapts processes when necessary to achieve results'
                        ]
                    ]
                ],
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[$userData['email']] = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
        
        // Assign PI Behavioral Pattern IDs after patterns are created
        $persuaderPattern = PiBehavioralPattern::where('code', 'PERSUADER')->first();
        $captainPattern = PiBehavioralPattern::where('code', 'CAPTAIN')->first();
        
        if ($persuaderPattern) {
            $createdUsers['victor@umbral.ai']->update(['pi_behavioral_pattern_id' => $persuaderPattern->id]);
        }
        
        if ($captainPattern) {
            $createdUsers['matt.putman@example.com']->update(['pi_behavioral_pattern_id' => $captainPattern->id]);
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

        // Assign modules to John Doe exactly as in local DB
        $johnUser = $createdUsers['john@btcs.com'];
        foreach ($createdModules as $module) {
            if (!$johnUser->accessibleModules()->where('module_id', $module->id)->exists()) {
                $johnUser->accessibleModules()->attach($module->id, [
                    'assigned_at' => now(), // Set to current time since NULL not allowed
                    'progress_data' => null, // Matches local DB  
                ]);
            }
        }
        
        // Assign some modules to Victor (Persuader profile)
        $victorUser = $createdUsers['victor@umbral.ai'];
        $victorModules = ['personalization-predictive-index', 'core-coaching-scenarios', 'advanced-team-culture-application'];
        foreach ($victorModules as $moduleSlug) {
            if (isset($createdModules[$moduleSlug]) && !$victorUser->accessibleModules()->where('module_id', $createdModules[$moduleSlug]->id)->exists()) {
                $victorUser->accessibleModules()->attach($createdModules[$moduleSlug]->id, [
                    'assigned_at' => now(),
                    'progress_data' => json_encode(['completion_percentage' => rand(25, 75)]),
                ]);
            }
        }
        
        // Assign some modules to Matt (Captain profile)  
        $mattUser = $createdUsers['matt.putman@example.com'];
        $mattModules = ['personalization-predictive-index', 'performance-growth-cycle', 'advanced-team-culture-application'];
        foreach ($mattModules as $moduleSlug) {
            if (isset($createdModules[$moduleSlug]) && !$mattUser->accessibleModules()->where('module_id', $createdModules[$moduleSlug]->id)->exists()) {
                $mattUser->accessibleModules()->attach($createdModules[$moduleSlug]->id, [
                    'assigned_at' => now(),
                    'progress_data' => json_encode(['completion_percentage' => rand(25, 75)]),
                ]);
            }
        }

        // Create action items exactly as they exist in local DB
        $actionItems = [
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Practice Active Listening with Team Members',
                'description' => 'Focus on demonstrating active listening skills during team meetings and one-on-ones.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => '2025-08-13 00:00:00',
                'context' => 'Based on coaching session feedback about communication improvements',
                'completed_at' => '2025-08-04 03:25:30',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Adapt Communication Style for Analytical Team Members',
                'description' => 'Use data-driven communication approaches when working with team members who have analytical PI profiles.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => '2025-08-16 00:00:00',
                'context' => 'Identified during PI profile analysis session',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Assess Team Member Readiness Levels for Current Project',
                'description' => 'Evaluate each team member using the SLII framework to determine their development level for current project tasks.',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => '2025-08-11 00:00:00',
                'context' => 'Preparation for upcoming project milestone review',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Complete Personal Leadership Goals Review',
                'description' => 'Review and update personal leadership development goals for the quarter.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => '2025-08-20 00:00:00',
                'context' => 'Quarterly development planning session',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Schedule Follow-up PI Assessment',
                'description' => 'Arrange follow-up PI assessment session to track development progress.',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => '2025-09-06 00:00:00',
                'context' => '6-month follow-up recommendation from initial assessment',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Complete Initial PI Behavioral Assessment',
                'description' => 'Finish the comprehensive Predictive Index behavioral assessment.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => '2025-07-30 00:00:00',
                'context' => 'Required for personalized coaching approach',
                'completed_at' => '2025-07-30 03:25:30',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Schedule team one-on-ones',
                'description' => 'Set up individual meetings with each team member this week.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => '2025-08-09 00:00:00',
                'context' => 'Follow-up from coaching session on team management',
                'completed_at' => '2025-08-05 03:25:30',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Research competitor pricing',
                'description' => 'Analyze competitor pricing strategies for Q4 planning.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => '2025-08-13 00:00:00',
                'context' => 'Strategic planning initiative',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Update project documentation',
                'description' => 'Revise project documentation to reflect recent changes.',
                'priority' => 'low',
                'status' => 'completed',
                'due_date' => '2025-08-05 00:00:00',
                'context' => 'Documentation maintenance task',
                'completed_at' => '2025-08-04 03:25:30',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            // Victor's Action Items (Persuader profile)
            [
                'user_id' => $createdUsers['victor@umbral.ai']->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Review Personal PI Persuader Profile',
                'description' => 'Study Persuader behavioral traits and apply insights to current team interactions.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => '2025-08-10 00:00:00',
                'context' => 'Initial PI assessment review and application',
                'completed_at' => '2025-08-08 10:30:00',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-08 10:30:00',
            ],
            [
                'user_id' => $createdUsers['victor@umbral.ai']->id,
                'module_id' => $createdModules['core-coaching-scenarios']->id,
                'title' => 'Practice Motivational Communication Techniques',
                'description' => 'Apply persuasive communication skills learned in coaching scenarios with team members.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => '2025-08-15 00:00:00',
                'context' => 'Leveraging Persuader strengths in team motivation',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            // Matt's Action Items (Captain profile)
            [
                'user_id' => $createdUsers['matt.putman@example.com']->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Analyze Captain Leadership Style Impact',
                'description' => 'Evaluate how Captain behavioral drives affect team dynamics and decision-making speed.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => '2025-08-12 00:00:00',
                'context' => 'Strategic leadership assessment based on PI Captain profile',
                'completed_at' => '2025-08-11 14:15:00',
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-11 14:15:00',
            ],
            [
                'user_id' => $createdUsers['matt.putman@example.com']->id,
                'module_id' => $createdModules['performance-growth-cycle']->id,
                'title' => 'Implement Results-Focused Team Metrics',
                'description' => 'Develop and deploy performance metrics that align with Captain focus on results and innovation.',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => '2025-08-18 00:00:00',
                'context' => 'Performance management strategy utilizing Captain strengths',
                'completed_at' => null,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
        ];

        foreach ($actionItems as $itemData) {
            ActionItem::firstOrCreate(
                [
                    'user_id' => $itemData['user_id'],
                    'title' => $itemData['title']
                ],
                $itemData
            );
        }

        // Create achievements exactly as they exist in local DB
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
                'unlocked_at' => '2025-07-30 03:25:30',
                'progress_percentage' => 100.00,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            [
                'user_id' => $johnUser->id,
                'module_id' => null, // Global achievement
                'title' => 'Task Master',
                'description' => 'Completed 5 action items successfully.',
                'type' => 'milestone',
                'points' => 250,
                'badge_icon' => 'target',
                'badge_color' => '#10b981',
                'is_unlocked' => true,
                'unlocked_at' => '2025-08-04 03:25:30',
                'progress_percentage' => 100.00,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
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
                'unlocked_at' => null,
                'progress_percentage' => 75.00,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-06 03:25:30',
            ],
            // Victor's Achievements (Persuader profile)
            [
                'user_id' => $createdUsers['victor@umbral.ai']->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Persuader Insights',
                'description' => 'Successfully applied Persuader profile insights to team motivation.',
                'type' => 'completion',
                'points' => 150,
                'badge_icon' => 'users',
                'badge_color' => '#8b5cf6',
                'is_unlocked' => true,
                'unlocked_at' => '2025-08-08 10:30:00',
                'progress_percentage' => 100.00,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-08 10:30:00',
            ],
            // Matt's Achievements (Captain profile)
            [
                'user_id' => $createdUsers['matt.putman@example.com']->id,
                'module_id' => $createdModules['personalization-predictive-index']->id,
                'title' => 'Strategic Leader',
                'description' => 'Demonstrated Captain leadership qualities in strategic planning.',
                'type' => 'completion',
                'points' => 200,
                'badge_icon' => 'shield',
                'badge_color' => '#ef4444',
                'is_unlocked' => true,
                'unlocked_at' => '2025-08-11 14:15:00',
                'progress_percentage' => 100.00,
                'created_at' => '2025-08-06 03:25:30',
                'updated_at' => '2025-08-11 14:15:00',
            ],
        ];

        foreach ($achievements as $achievementData) {
            Achievement::firstOrCreate(
                [
                    'user_id' => $achievementData['user_id'],
                    'title' => $achievementData['title']
                ],
                $achievementData
            );
        }

        $this->command->info('Production data seeding completed successfully!');
        $this->command->info('Created users:');
        foreach ($createdUsers as $email => $user) {
            $this->command->info("- {$user->name} ({$email}) - Role: {$user->role}");
        }
    }

    private function seedPiBehavioralPatterns()
    {
        $this->command->info('Creating all 11 PI Behavioral Patterns...');
        
        // All 11 patterns from PiBehavioralPatternSeeder.php
        $patterns = [
            [
                'name' => 'Analyzer',
                'code' => 'ANALYZER',
                'description' => 'Analytical, thorough, and systematic. They have a strong drive to gather and analyze information before making decisions.',
                'behavioral_drives' => [
                    'dominance' => 25,
                    'extraversion' => 25,
                    'patience' => 75,
                    'formality' => 85
                ],
                'strengths' => 'Thorough analysis, attention to detail, systematic approach, quality-focused, methodical problem-solving',
                'challenges' => 'May over-analyze, slow decision-making, resistance to change, perfectionism, may miss deadlines',
                'work_style' => 'Prefers structured environments, detailed planning, time to analyze, minimal interruptions, clear expectations',
                'communication_style' => 'Factual, detailed, written communication preferred, asks clarifying questions, wants complete information',
                'leadership_style' => 'Lead by expertise, methodical approach, thorough planning, quality-focused, consultative',
                'ideal_work_environment' => 'Quiet, organized workspace with minimal distractions, access to information and resources, structured processes',
                'motivation_factors' => 'Quality work, expertise recognition, learning opportunities, detailed information, stable environment',
                'stress_factors' => 'Tight deadlines, incomplete information, frequent changes, high-pressure situations, interruptions',
                'compatible_patterns' => ['CONTROLLER', 'SPECIALIST', 'CRAFTSMAN']
            ],
            [
                'name' => 'Controller',
                'code' => 'CONTROLLER',
                'description' => 'Independent, results-oriented, and decisive. They prefer to work alone and maintain control over their work.',
                'behavioral_drives' => [
                    'dominance' => 85,
                    'extraversion' => 25,
                    'patience' => 25,
                    'formality' => 75
                ],
                'strengths' => 'Results-oriented, independent, decisive, goal-focused, efficient, problem-solving',
                'challenges' => 'May be impatient, can be seen as demanding, may not delegate well, resistance to being controlled',
                'work_style' => 'Independent work, clear goals, minimal supervision, control over methods, results-focused',
                'communication_style' => 'Direct, brief, bottom-line focused, prefers written communication, task-oriented',
                'leadership_style' => 'Authoritative, results-focused, sets high standards, direct communication, goal-oriented',
                'ideal_work_environment' => 'Private workspace, minimal meetings, control over schedule, clear objectives, results-based evaluation',
                'motivation_factors' => 'Achievement, independence, control, challenging goals, recognition for results',
                'stress_factors' => 'Micromanagement, unclear expectations, bureaucracy, forced collaboration, loss of control',
                'compatible_patterns' => ['ANALYZER', 'SPECIALIST', 'VENTURER']
            ],
            [
                'name' => 'Venturer',
                'code' => 'VENTURER',
                'description' => 'Innovative, risk-taking, and entrepreneurial. They thrive on variety, change, and new challenges.',
                'behavioral_drives' => [
                    'dominance' => 85,
                    'extraversion' => 85,
                    'patience' => 25,
                    'formality' => 25
                ],
                'strengths' => 'Innovative, adaptable, risk-taking, entrepreneurial, energetic, inspiring',
                'challenges' => 'May lack follow-through, impatient with details, can be impulsive, may neglect routine tasks',
                'work_style' => 'Variety, new challenges, minimal routine, creative freedom, fast-paced environment',
                'communication_style' => 'Enthusiastic, big-picture focused, verbal communication, inspiring, conceptual',
                'leadership_style' => 'Visionary, inspiring, delegates details, focuses on innovation, change-oriented',
                'ideal_work_environment' => 'Dynamic, flexible, variety in tasks, opportunities for innovation, minimal bureaucracy',
                'motivation_factors' => 'New challenges, innovation, recognition, variety, growth opportunities',
                'stress_factors' => 'Routine tasks, micromanagement, detailed procedures, slow pace, bureaucracy',
                'compatible_patterns' => ['PERSUADER', 'CAPTAIN', 'CONTROLLER']
            ],
            [
                'name' => 'Captain',
                'code' => 'CAPTAIN',
                'description' => 'Natural leaders who are both results-oriented and people-focused. They excel at building teams and driving results.',
                'behavioral_drives' => [
                    'dominance' => 85,
                    'extraversion' => 75,
                    'patience' => 50,
                    'formality' => 50
                ],
                'strengths' => 'Natural leadership, team building, results-oriented, strategic thinking, motivational',
                'challenges' => 'May be impatient, can overwhelm others, may take on too much, resistance to being managed',
                'work_style' => 'Leading teams, strategic focus, variety in tasks, goal-oriented, collaborative leadership',
                'communication_style' => 'Direct but diplomatic, motivational, verbal communication, team-focused, strategic',
                'leadership_style' => 'Transformational, team-oriented, results-focused, strategic, empowering',
                'ideal_work_environment' => 'Team-based, strategic role, variety, leadership opportunities, goal-focused culture',
                'motivation_factors' => 'Leadership opportunities, team success, strategic challenges, recognition, growth',
                'stress_factors' => 'Micromanagement, individual contributor role, bureaucracy, lack of influence',
                'compatible_patterns' => ['VENTURER', 'PERSUADER', 'PROMOTER']
            ],
            [
                'name' => 'Persuader',
                'code' => 'PERSUADER',
                'description' => 'Influential, optimistic, and people-focused. They excel at building relationships and influencing others.',
                'behavioral_drives' => [
                    'dominance' => 75,
                    'extraversion' => 85,
                    'patience' => 50,
                    'formality' => 25
                ],
                'strengths' => 'Influential, optimistic, relationship-building, persuasive, energetic, inspiring',
                'challenges' => 'May over-commit, can be disorganized, may neglect details, impatient with process',
                'work_style' => 'People interaction, variety, influence opportunities, flexible schedule, team collaboration',
                'communication_style' => 'Enthusiastic, persuasive, verbal, relationship-focused, optimistic',
                'leadership_style' => 'Inspirational, people-focused, motivational, collaborative, influence-based',
                'ideal_work_environment' => 'People-oriented, flexible, collaborative, variety, minimal routine',
                'motivation_factors' => 'People interaction, influence, recognition, variety, positive relationships',
                'stress_factors' => 'Isolation, detailed tasks, rigid structure, negative environment, conflict',
                'compatible_patterns' => ['VENTURER', 'CAPTAIN', 'PROMOTER']
            ],
            [
                'name' => 'Promoter',
                'code' => 'PROMOTER',
                'description' => 'Enthusiastic, optimistic, and socially driven. They excel at promoting ideas and building enthusiasm.',
                'behavioral_drives' => [
                    'dominance' => 50,
                    'extraversion' => 85,
                    'patience' => 25,
                    'formality' => 25
                ],
                'strengths' => 'Enthusiastic, optimistic, creative, energetic, relationship-building, inspiring',
                'challenges' => 'May lack follow-through, can be disorganized, impatient with details, may over-commit',
                'work_style' => 'People interaction, creativity, variety, flexible deadlines, collaborative environment',
                'communication_style' => 'Enthusiastic, creative, verbal, people-focused, inspiring',
                'leadership_style' => 'Inspirational, creative, people-focused, motivational, collaborative',
                'ideal_work_environment' => 'Creative, people-oriented, flexible, variety, positive atmosphere',
                'motivation_factors' => 'Creativity, people interaction, recognition, variety, positive feedback',
                'stress_factors' => 'Detailed tasks, isolation, rigid deadlines, negative criticism, routine work',
                'compatible_patterns' => ['PERSUADER', 'CAPTAIN', 'COLLABORATOR']
            ],
            [
                'name' => 'Collaborator',
                'code' => 'COLLABORATOR',
                'description' => 'Team-oriented, supportive, and relationship-focused. They excel at bringing people together and facilitating cooperation.',
                'behavioral_drives' => [
                    'dominance' => 25,
                    'extraversion' => 75,
                    'patience' => 75,
                    'formality' => 50
                ],
                'strengths' => 'Team-oriented, supportive, diplomatic, good listener, facilitating, relationship-building',
                'challenges' => 'May avoid conflict, can be indecisive, may not assert own needs, resistance to change',
                'work_style' => 'Team collaboration, supportive role, relationship-focused, consensus-building, stable environment',
                'communication_style' => 'Diplomatic, supportive, good listener, relationship-focused, collaborative',
                'leadership_style' => 'Servant leadership, supportive, consensus-building, relationship-focused, team-oriented',
                'ideal_work_environment' => 'Team-based, supportive culture, stable, collaborative, positive relationships',
                'motivation_factors' => 'Team harmony, helping others, relationships, stability, recognition for support',
                'stress_factors' => 'Conflict, high pressure, individual competition, frequent changes, isolation',
                'compatible_patterns' => ['PROMOTER', 'GUARDIAN', 'SPECIALIST']
            ],
            [
                'name' => 'Guardian',
                'code' => 'GUARDIAN',
                'description' => 'Steady, reliable, and service-oriented. They provide stability and support to their teams and organizations.',
                'behavioral_drives' => [
                    'dominance' => 25,
                    'extraversion' => 50,
                    'patience' => 85,
                    'formality' => 75
                ],
                'strengths' => 'Reliable, steady, service-oriented, loyal, supportive, consistent',
                'challenges' => 'May resist change, can be overly cautious, may not assert own needs, slow to make decisions',
                'work_style' => 'Stable environment, clear procedures, supportive role, team-oriented, predictable routine',
                'communication_style' => 'Steady, supportive, good listener, diplomatic, relationship-focused',
                'leadership_style' => 'Supportive, steady, service-oriented, consensus-building, relationship-focused',
                'ideal_work_environment' => 'Stable, supportive, team-oriented, clear procedures, positive culture',
                'motivation_factors' => 'Helping others, stability, team harmony, recognition for service, clear expectations',
                'stress_factors' => 'Frequent changes, conflict, high pressure, unclear expectations, isolation',
                'compatible_patterns' => ['COLLABORATOR', 'SPECIALIST', 'CRAFTSMAN']
            ],
            [
                'name' => 'Specialist',
                'code' => 'SPECIALIST',
                'description' => 'Expert-focused, thorough, and quality-oriented. They excel in their area of expertise and prefer depth over breadth.',
                'behavioral_drives' => [
                    'dominance' => 50,
                    'extraversion' => 25,
                    'patience' => 75,
                    'formality' => 85
                ],
                'strengths' => 'Subject matter expertise, quality-focused, thorough, analytical, reliable, precise',
                'challenges' => 'May be too focused on details, resistance to change, may not see big picture, perfectionism',
                'work_style' => 'Expertise-based work, quality-focused, minimal interruptions, clear standards, depth over breadth',
                'communication_style' => 'Technical, detailed, expert-focused, prefers written communication, precise',
                'leadership_style' => 'Expert leadership, quality-focused, methodical, consultative, standards-oriented',
                'ideal_work_environment' => 'Expertise-focused, quality-oriented, minimal distractions, clear standards, stable',
                'motivation_factors' => 'Expertise recognition, quality work, learning, clear standards, professional development',
                'stress_factors' => 'Tight deadlines, frequent interruptions, poor quality standards, frequent changes',
                'compatible_patterns' => ['ANALYZER', 'CONTROLLER', 'GUARDIAN']
            ],
            [
                'name' => 'Craftsman',
                'code' => 'CRAFTSMAN',
                'description' => 'Detail-oriented, quality-focused, and methodical. They take pride in producing high-quality work.',
                'behavioral_drives' => [
                    'dominance' => 25,
                    'extraversion' => 25,
                    'patience' => 85,
                    'formality' => 85
                ],
                'strengths' => 'Quality-focused, detail-oriented, methodical, reliable, thorough, careful',
                'challenges' => 'May be slow to complete tasks, perfectionism, resistance to change, may miss deadlines',
                'work_style' => 'Quality-focused, methodical approach, minimal pressure, clear standards, stable environment',
                'communication_style' => 'Precise, detailed, prefers written communication, methodical, quality-focused',
                'leadership_style' => 'Lead by example, quality-focused, methodical, standards-oriented, careful',
                'ideal_work_environment' => 'Quality-oriented, stable, minimal pressure, clear standards, organized',
                'motivation_factors' => 'Quality recognition, craftsmanship, clear standards, stability, expertise development',
                'stress_factors' => 'Tight deadlines, pressure for speed, poor quality standards, frequent changes',
                'compatible_patterns' => ['ANALYZER', 'GUARDIAN', 'SPECIALIST']
            ]
        ];

        foreach ($patterns as $patternData) {
            PiBehavioralPattern::firstOrCreate(
                ['code' => $patternData['code']],
                $patternData
            );
        }
        
        $this->command->info('All 11 PI Behavioral Patterns created successfully.');
    }
}