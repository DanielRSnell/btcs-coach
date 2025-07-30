<?php

namespace Database\Seeders;

use App\Models\PiBehavioralPattern;
use Illuminate\Database\Seeder;

class PiBehavioralPatternSeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($patterns as $pattern) {
            PiBehavioralPattern::create($pattern);
        }
    }
}