# Voiceflow Integration Documentation

## Overview
This document details how data is passed to Voiceflow based on different routes in the BTCS Coach application.

## Routes and Data Structure

### Module Chat Route: `/modules/{slug}/chat`

**Route Pattern**: `/modules/{module:slug}/chat`  
**Controller**: `ModuleController@chat`  
**Component**: `resources/js/pages/module-chat.tsx`

#### Data Passed to Voiceflow

```javascript
window.voiceflow.chat.load({
  verify: { projectID: '686331bc96acfa1dd62f6fd5' },
  url: 'https://general-runtime.voiceflow.com',
  versionID: 'production',
  voice: {
    url: "https://runtime-api.voiceflow.com"
  },
  render: {
    mode: 'embedded',
    target: document.getElementById('btcs-chat')
  },
  assistant: {
    title: `BTCS Coach - ${module.title}`,
    description: `${module.type} session focusing on PI and Situational Leadership`,
    stylesheet: '/voiceflow.css'
  },
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
          id: module.id,
          title: module.title,
          type: module.type,
          slug: module.slug,
          topics: module.topics.join(', '),
          learning_objectives: module.learning_objectives,
          estimated_duration: module.estimated_duration,
          difficulty: module.difficulty
        },
        user: {
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role
        },
        session_context: 'pi_ssl_coaching'
      }
    }
  },
  user: {
    name: user.name,
    image: null
  }
});
```

#### Variable Access in Voiceflow Agent

You can access these variables in your Voiceflow flows:

**Route Information:**
- `{route.name}` → "modules.chat"
- `{route.path}` → "/modules/understanding-pi-behavioral-pattern/chat"
- `{route.params.slug}` → "understanding-pi-behavioral-pattern"

**Module Information:**
- `{module.id}` → 1
- `{module.title}` → "Understanding Your PI Behavioral Pattern"
- `{module.type}` → "assessment"
- `{module.slug}` → "understanding-pi-behavioral-pattern" 
- `{module.topics}` → "behavioral drives, self-awareness, dominance, extraversion, patience, formality"
- `{module.learning_objectives}` → "Understand your unique PI pattern, recognize how your behavioral drives impact..."
- `{module.estimated_duration}` → 45
- `{module.difficulty}` → "beginner"

**User Information:**
- `{user.id}` → 2
- `{user.name}` → "John Doe"
- `{user.email}` → "john@btcs.com"
- `{user.role}` → "member"

**Session Context:**
- `{session_context}` → "pi_ssl_coaching"

## Custom Styling

The chat widget loads custom CSS from `/public/voiceflow.css` which includes:
- BTCS Coach branding (purple gradient theme)
- Custom message styling
- Mobile responsive design
- Professional animations and transitions

## Implementation Details

### Backend (Laravel)
- **Route**: Defined in `routes/web.php`
- **Controller**: `app/Http/Controllers/ModuleController.php`
- **Method**: `chat(Module $module)`
- **Returns**: Module data + authenticated user data to frontend

### Frontend (React/TypeScript)
- **Component**: `resources/js/pages/module-chat.tsx`
- **Initialization**: `useEffect` hook loads Voiceflow script
- **Target Element**: `<div id="btcs-chat">` for embedded chat
- **Data Flow**: Props → Voiceflow payload → Agent variables

### Database Context
When users start a module session, the system:
1. Records session start in `module_user` pivot table
2. Tracks progress with `assigned_at` timestamp
3. Stores progress data as JSON in `progress_data` column

## Example Module Session Flow

1. **User clicks "Start Session"** on dashboard or modules page
2. **Route**: `/modules/understanding-pi-behavioral-pattern/chat`
3. **Backend**: Loads module data, tracks session start
4. **Frontend**: Receives module + user data
5. **Voiceflow**: Initializes with full context
6. **Agent**: Has access to all module/user variables for personalized coaching

## Available Module Types
- `assessment` → PI behavioral assessments
- `training` → Skill-building modules  
- `coaching` → Interactive coaching sessions

## Available Module Difficulties
- `beginner` → Introductory level
- `intermediate` → Moderate complexity
- `advanced` → Expert level

## User Roles
- `admin` → Full system access
- `member` → Standard user access

## Future Route Implementations

As the application grows, additional routes can follow this pattern:

```javascript
payload: {
  route: {
    name: 'route.name',
    path: '/actual/route/path',
    params: { /* route parameters */ }
  },
  // Route-specific data objects
  session_context: 'context_identifier'
}
```

This ensures consistent data structure across all Voiceflow integrations throughout the application.