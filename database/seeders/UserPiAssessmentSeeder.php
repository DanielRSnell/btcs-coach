<?php

namespace Database\Seeders;

use App\Models\PiBehavioralPattern;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserPiAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $patterns = PiBehavioralPattern::all();

        if ($users->count() > 0 && $patterns->count() > 0) {
            // Assign different PI patterns to existing users
            $sampleAssignments = [
                [
                    'pattern_code' => 'CAPTAIN',
                    'raw_scores' => [
                        'dominance' => 85,
                        'extraversion' => 75,
                        'patience' => 50,
                        'formality' => 50
                    ],
                    'assessor' => 'Dr. Sarah Johnson',
                    'notes' => 'Strong leadership capabilities with excellent team-building skills. Shows natural ability to balance results with people focus.'
                ],
                [
                    'pattern_code' => 'ANALYZER',
                    'raw_scores' => [
                        'dominance' => 25,
                        'extraversion' => 25,
                        'patience' => 75,
                        'formality' => 85
                    ],
                    'assessor' => 'Dr. Sarah Johnson',
                    'notes' => 'Highly analytical with strong attention to detail. Prefers thorough analysis before making decisions.'
                ],
                [
                    'pattern_code' => 'PERSUADER',
                    'raw_scores' => [
                        'dominance' => 75,
                        'extraversion' => 85,
                        'patience' => 50,
                        'formality' => 25
                    ],
                    'assessor' => 'Dr. Michael Chen',
                    'notes' => 'Excellent interpersonal skills with strong influence abilities. Thrives in people-oriented environments.'
                ]
            ];

            foreach ($users->take(3) as $index => $user) {
                if (isset($sampleAssignments[$index])) {
                    $assignment = $sampleAssignments[$index];
                    $pattern = $patterns->where('code', $assignment['pattern_code'])->first();
                    
                    if ($pattern) {
                        $user->update([
                            'pi_behavioral_pattern_id' => $pattern->id,
                            'pi_raw_scores' => $assignment['raw_scores'],
                            'pi_assessed_at' => now()->subDays(rand(30, 90)),
                            'pi_assessor_name' => $assignment['assessor'],
                            'pi_notes' => $assignment['notes']
                        ]);
                        
                        $this->command->info("Assigned {$pattern->name} pattern to user: {$user->name}");
                    }
                }
            }
        }
    }
}