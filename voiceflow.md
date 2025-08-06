# Voiceflow Integration Documentation

## Overview

The BTCS Coach application integrates with Voiceflow to provide AI-powered coaching sessions. This document outlines how the integration works, what data is passed to Voiceflow, and how the system initializes.

## Integration Location

The Voiceflow integration is implemented in:
- **Frontend Component:** `resources/js/pages/module-chat.tsx`
- **Backend Controller:** `app/Http/Controllers/ModuleController.php` (chat method)
- **Route:** `/modules/{module}/chat`

## Voiceflow Configuration

### Project Details
- **Project ID:** `686331bc96acfa1dd62f6fd5` 
- **Runtime URL:** `https://general-runtime.voiceflow.com`
- **Voice API URL:** `https://runtime-api.voiceflow.com`
- **Version:** `production`
- **CDN Script:** `https://cdn.voiceflow.com/widget-next/bundle.mjs`

### Render Configuration
- **Mode:** `embedded` (renders within our application)
- **Target Element:** `#btcs-chat` div container
- **Stylesheet:** `/voiceflow.css` (custom styling)
- **Autostart:** `true` (automatically starts conversation)

## Voiceflow Initialization Script

```javascript
// Initialize Voiceflow when component mounts
const initializeVoiceflow = () => {
    setTimeout(() => {
        console.log('Starting Voiceflow initialization...');
        const chatElement = document.getElementById('btcs-chat');
        
        // Dynamic script loading
        (function(d, t) {
            var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
            v.onload = function() {
                if (window.voiceflow && window.voiceflow.chat) {
                    const targetElement = document.getElementById('btcs-chat');
                    
                    window.voiceflow.chat.load({
                        verify: { projectID: '686331bc96acfa1dd62f6fd5' },
                        url: 'https://general-runtime.voiceflow.com',
                        versionID: 'production',
                        voice: {
                            url: "https://runtime-api.voiceflow.com"
                        },
                        render: {
                            mode: 'embedded',
                            target: targetElement
                        },
                        assistant: {
                            stylesheet: '/voiceflow.css'
                        },
                        autostart: true,
                        launch: {
                            event: {
                                type: 'launch',
                                payload: {
                                    // PAYLOAD DATA DOCUMENTED BELOW
                                }
                            }
                        }
                    });
                }
            };
            v.onerror = function() {
                // Error handling for failed script loading
            };
            v.src = "https://cdn.voiceflow.com/widget-next/bundle.mjs";
            v.type = "text/javascript";
            s.parentNode.insertBefore(v, s);
        })(document, 'script');
    }, 100);
};
```

## Data Payload Structure

### Complete Launch Event Payload

```javascript
launch: {
    event: {
        type: 'launch',
        payload: {
            route: {
                name: 'modules.chat',
                path: `/modules/${module.slug}/chat`,
                params: {
                    slug: module.slug
                }
            },
            module: {
                // MODULE DATA - See below
            },
            user: {
                // USER DATA - See below  
            },
            session_context: 'pi_ssl_coaching'
        }
    }
}
```

## Module Data

All coaching module information passed to Voiceflow:

```javascript
module: {
    id: number,                    // Unique module identifier
    title: string,                 // Module title (e.g., "Core Coaching Scenarios")
    description: string,           // Module description
    goal: string,                  // What the module demonstrates/teaches
    type: string,                  // 'coaching' | 'training' | 'assessment'
    slug: string,                  // URL-friendly identifier
    topics: string[],              // Array of topic strings
    topics_string: string,         // Topics joined as comma-separated string
    sample_questions: string[],    // Array of example questions for the module
    learning_objectives: string,   // What users should learn/achieve
    expected_outcomes: string,     // What good responses should include
    estimated_duration: number,   // Duration in minutes
    difficulty: string            // 'beginner' | 'intermediate' | 'advanced'
}
```

### Example Module Data
```javascript
// Example for "Core Coaching Scenarios" module
module: {
    id: 10,
    title: "Core Coaching Scenarios",
    description: "Demonstrate the agent's capability: synthesizing multiple frameworks—PI, SLII, and Courageous Conversations—to solve a complex, human-centric problem.",
    goal: "Demonstrate the agent's capability: synthesizing multiple frameworks—PI, SLII, and Courageous Conversations—to solve a complex, human-centric problem.",
    type: "coaching",
    slug: "core-coaching-scenarios",
    topics: [
        "Manager coaching",
        "Employee coaching", 
        "Performance management",
        "SLII framework",
        "Courageous Conversations",
        "PI-based coaching"
    ],
    topics_string: "Manager coaching, Employee coaching, Performance management, SLII framework, Courageous Conversations, PI-based coaching",
    sample_questions: [
        "I need help preparing for a talk with my direct report, Sarah. She's been missing deadlines on the new marketing campaign. Her work quality is still good when she turns it in, but her timeliness is slipping. Her PI is a Collaborator. How should I handle this conversation?",
        "My manager is micromanaging me. I'm leading the 'Project Phoenix' roll-out, and I feel confident, but he asks for updates multiple times a day. It's slowing me down. I know he is a Controller PI. How can I ask for more autonomy without sounding like I'm complaining?"
    ],
    learning_objectives: "Test the agent's ability to synthesize multiple coaching frameworks to provide comprehensive, personalized coaching advice.",
    expected_outcomes: "This is the most important test. The agent should not give generic advice. A great response will: Acknowledge the situation with empathy. Diagnose using the frameworks: \"Based on SLII, Sarah sounds like a D3 (Capable but Cautious Performer) on this task...\" \"Since your manager is a Controller, their need for data and control is high...\" Structure the advice using the Courageous Conversations model (state facts, share intent, listen, partner on a solution). Tailor the advice to the PI profile (e.g., \"For a Collaborator, be sure to start by reinforcing your trust in them as a person before discussing the task.\"). Cite the source documents it used for the frameworks.",
    estimated_duration: 20,
    difficulty: "advanced"
}
```

## User Data

Complete user profile and PI assessment data:

```javascript
user: {
    // Basic User Information
    id: number,                    // User ID
    name: string,                  // Full name
    email: string,                 // Email address
    role: string,                  // 'admin' | 'member'
    
    // PI Assessment Data
    pi_behavioral_pattern_id: number | null,    // ID of assigned PI pattern
    pi_behavioral_pattern: {                    // Complete PI pattern details
        id: number,                             // Pattern ID
        name: string,                           // Pattern name (e.g., "Persuader", "Captain")
        code: string,                           // Pattern code (e.g., "PERSUADER", "CAPTAIN") 
        description: string                     // Pattern description
    } | null,
    
    // Individual PI Scores (0-100)
    pi_raw_scores: {
        dominance: number,        // Dominance score (A factor)
        extraversion: number,     // Extraversion score (B factor)
        patience: number,         // Patience score (C factor)
        formality: number        // Formality score (D factor)
    } | null,
    
    // Assessment Metadata
    pi_assessed_at: string | null,    // Assessment date (YYYY-MM-DD format)
    pi_notes: string | null,          // Assessment notes
    
    // Performance Index Profile (Complete detailed profile)
    pi_profile: {
        basic_info: {
            assessment_date: string,     // YYYY-MM-DD
            report_date: string,         // YYYY-MM-DD
            profile_type: string,        // e.g., "Persuader", "Captain"
            profile_description: string  // Brief profile description
        },
        behavioral_traits: {
            strongest_behaviors: Array<{behavior: string}>,  // Array of behavior statements
            summary: string                                  // Comprehensive behavioral summary
        },
        management_strategies: {
            optimal_conditions: Array<{condition: string}>,     // Ideal work conditions
            avoid_conditions: Array<{condition: string}>        // Conditions to avoid
        },
        work_preferences: {
            pace: string,                    // Work pace preference
            decision_making: string,         // Decision-making style
            communication_style: string,     // Communication approach
            focus: string,                   // Focus areas
            team_orientation: string,        // Team interaction style
            risk_tolerance: string          // Risk-taking approach
        },
        social_style: {
            formality: string,              // Social formality level
            trust_building: string,         // How they build trust
            relationship_focus: string      // Relationship priorities
        },
        motivation_drivers: {
            recognition: string,            // Recognition needs
            security: string,               // Security preferences
            autonomy: string,               // Independence needs
            advancement: string,            // Growth motivation
            collaboration: string          // Collaboration drive
        },
        technical_orientation: {
            detail_orientation: string,     // Detail focus level
            innovation_tolerance: string,   // Change/innovation comfort
            process_adherence: string      // Process-following tendency
        }
    } | null,
    
    // Assessment Status Flags
    has_pi_assessment: boolean,    // True if user has completed PI assessment
    has_pi_profile: boolean       // True if user has detailed PI profile
}
```

### Example User Data
```javascript
// Example for Victor Morales (Persuader)
user: {
    id: 4,
    name: "Victor Morales",
    email: "victor.morales@example.com",
    role: "member",
    pi_behavioral_pattern_id: 5,
    pi_behavioral_pattern: {
        id: 5,
        name: "Persuader",
        code: "PERSUADER",
        description: "Influential, optimistic, and people-focused. They excel at building relationships and influencing others."
    },
    pi_raw_scores: {
        dominance: 75,
        extraversion: 88,
        patience: 32,
        formality: 58
    },
    pi_assessed_at: "2021-08-04",
    pi_notes: null,
    pi_profile: {
        basic_info: {
            assessment_date: "2021-08-04",
            report_date: "2021-09-03", 
            profile_type: "Persuader",
            profile_description: "A Persuader is a risk-taking, socially poised and motivating team builder."
        },
        behavioral_traits: {
            strongest_behaviors: [
                {behavior: "Proactively connects quickly to others; open and sharing. Builds and leverages relationships to get work done."},
                {behavior: "Comfortably fluent and fast talk, in volume. Enthusiastically persuades and motivates others by considering their point of view and adjusting delivery."},
                // ... more behaviors
            ],
            summary: "Victor is a congenial, friendly communicator, capable of projecting enthusiasm and warmth, and motivating others. Works at a fast pace with an emphasis on getting results by working cooperatively with and through people..."
        },
        management_strategies: {
            optimal_conditions: [
                {condition: "Clear definition of responsibility, authority, and organizational relationships"},
                {condition: "Specific training in the job"},
                // ... more conditions
            ],
            avoid_conditions: [
                {condition: "Highly repetitive, routine work with minimal social interaction"},
                {condition: "Lack of clarity in responsibilities or organizational support"}
            ]
        },
        work_preferences: {
            pace: "Faster-than-average pace; prefers active, dynamic environments",
            decision_making: "Collaborative approach; seeks input from trusted advisors when outside established policies",
            communication_style: "Warm, persuasive, and enthusiastic communicator",
            focus: "Team-oriented with emphasis on results through collaboration",
            team_orientation: "Highly collaborative; enjoys working with and through others",
            risk_tolerance: "Moderate; prefers to mitigate risk by consulting experts when making unusual decisions"
        },
        social_style: {
            formality: "Friendly, approachable, and confident in groups",
            trust_building: "Earns trust through openness, collaboration, and delivering results",
            relationship_focus: "Strong emphasis on building relationships to drive team and organizational success"
        },
        motivation_drivers: {
            recognition: "High; values social and status recognition as rewards for achievement",
            security: "Moderate; values management support during change",
            autonomy: "Moderate; comfortable with autonomy when responsibilities are clearly defined",
            advancement: "Motivated by opportunities to influence and lead teams",
            collaboration: "Strong driver; thrives in team-focused environments"
        },
        technical_orientation: {
            detail_orientation: "Moderate; follows plans and tracks details but becomes less effective if work is overly repetitive",
            innovation_tolerance: "Moderate; willing to adapt methods to improve efficiency while respecting established policies", 
            process_adherence: "High; operates well within established frameworks and company policies"
        }
    },
    has_pi_assessment: true,
    has_pi_profile: true
}
```

## Additional Context Data

```javascript
// Route information for context
route: {
    name: 'modules.chat',                    // Laravel route name
    path: `/modules/${module.slug}/chat`,    // Full URL path
    params: {
        slug: module.slug                    // Module slug parameter
    }
}

// Session context identifier
session_context: 'pi_ssl_coaching'          // Fixed identifier for coaching sessions
```

## Error Handling

The integration includes comprehensive error handling:

1. **Script Loading Failures**: Shows "Chat Service Unavailable" message
2. **Widget Initialization Failures**: Shows "Chat Temporarily Unavailable" message  
3. **Loading States**: Shows animated loading message while initializing
4. **Cleanup**: Properly destroys Voiceflow instances on component unmount

## Usage in Voiceflow

With this comprehensive data payload, the Voiceflow agent can:

1. **Personalize coaching** based on user's PI profile and scores
2. **Reference specific module goals** and expected outcomes
3. **Use sample questions** as conversation starters or examples
4. **Tailor advice** to individual behavioral traits and work preferences
5. **Apply appropriate coaching frameworks** (PI, SLII, Courageous Conversations)
6. **Understand user context** including role, assessment status, and preferences

## Data Privacy & Security

- All PI assessment data is personal and confidential
- Data is passed securely through HTTPS connections
- No sensitive data is logged in browser console (except during development)
- User consent should be obtained before sharing PI profile data

## Development & Debugging

Console logging is enabled for development:
- Voiceflow initialization steps
- Target element verification  
- Error states and fallbacks
- Widget loading confirmation

Remove or disable console logging in production for security.