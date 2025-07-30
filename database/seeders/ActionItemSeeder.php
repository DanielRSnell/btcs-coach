<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ActionItem;
use App\Models\CoachingSession;
use Illuminate\Database\Seeder;

class ActionItemSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing action items
        ActionItem::truncate();

        // Get John Doe user
        $johnDoe = User::where('email', 'john@btcs.com')->first();
        if (!$johnDoe) {
            return;
        }

        // Get some coaching sessions
        $sessions = CoachingSession::where('user_id', $johnDoe->id)->get();

        $actionItems = [
            // High priority items
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => $sessions->where('status', 'completed')->first()?->id,
                'title' => 'Practice Active Listening with Team Members',
                'description' => 'Based on your PI assessment showing strong Dominance, focus on pausing to truly listen before responding in team meetings. Practice the SOLER technique (Square shoulders, Open posture, Lean in, Eye contact, Relax).',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => now()->addDays(3)->toDateString(),
                'context' => 'PI Behavioral Assessment - High Dominance pattern requires development of active listening skills'
            ],
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => $sessions->where('topic', 'like', '%Communication%')->first()?->id,
                'title' => 'Adapt Communication Style for Analytical Team Members',
                'description' => 'Identified two team members with high Formality/low Extraversion patterns. Prepare detailed data and documentation before presenting new initiatives to them.',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(5)->toDateString(),
                'context' => 'PI Communication Training - Adapting to Analyzer and Controller patterns'
            ],
            
            // Medium priority items
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => $sessions->where('topic', 'like', '%Leadership%')->first()?->id,
                'title' => 'Assess Team Member Readiness Levels for Current Project',
                'description' => 'Apply situational leadership model to evaluate each team member\'s competence and commitment levels for the Q4 project deliverables.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addWeek()->toDateString(),
                'context' => 'Situational Leadership Training - Team readiness assessment for Q4 project'
            ],
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => null,
                'title' => 'Complete Personal Leadership Goals Review',
                'description' => 'Review and update personal development goals based on recent PI insights and SSL learning. Focus on areas identified during coaching sessions.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(10)->toDateString(),
                'context' => 'Self-Leadership Development - Goal setting and strategic planning'
            ],
            
            // Low priority items
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => null,
                'title' => 'Schedule Follow-up PI Assessment',
                'description' => 'Book follow-up PI behavioral assessment in 6 months to track behavioral development and adaptation progress.',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addMonths(6)->toDateString(),
                'context' => 'Assessment Follow-up - 6-month behavioral development review'
            ],
            
            // Completed items
            [
                'user_id' => $johnDoe->id,
                'coaching_session_id' => $sessions->first()?->id,
                'title' => 'Complete Initial PI Behavioral Assessment',
                'description' => 'Take the Predictive Index behavioral assessment to understand personal behavioral drives and leadership tendencies.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subDays(5)->toDateString(),
                'completed_at' => now()->subDays(3),
                'context' => 'PI Assessment - Initial behavioral pattern identification'
            ]
        ];

        foreach ($actionItems as $itemData) {
            ActionItem::create($itemData);
        }

        echo "Created " . count($actionItems) . " contextual action items for John Doe.\n";
    }
}