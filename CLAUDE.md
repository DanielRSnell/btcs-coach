# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BTCS Coach - A Business Training & Coaching System built for H.G. Fenton Company using:
- **Backend**: Laravel 12 with Filament admin panel
- **Frontend**: React 19 + TypeScript via Inertia.js 
- **Styling**: TailwindCSS 4 + ShadCN UI components
- **Animation**: Framer Motion
- **Build Tools**: Vite with SSR support

## Development Commands

### Frontend Development
```bash
# Start development server (includes PHP server, queue, logs, and Vite)
composer dev

# SSR development with hot reload  
composer dev:ssr

# Frontend only commands
npm run dev        # Vite development server
npm run build      # Build for production
npm run build:ssr  # Build with SSR
```

### Code Quality & Testing
```bash
# Linting & formatting
npm run lint       # ESLint with auto-fix
npm run format     # Prettier formatting
npm run types      # TypeScript type checking

# PHP testing
composer test      # Run PHP tests with Pest
```

## Key Architecture Concepts

### Session Management System
The application centers around a sophisticated multi-session chat system:

**VoiceflowSession Model** (`app/Models/VoiceflowSession.php`):
- Tracks real-time chat sessions with localStorage synchronization
- Each session has a unique `session_id` (userID from Voiceflow)
- Sessions are stored with full chat history in `value_data` JSON field
- Status tracking: 'ACTIVE', 'COMPLETED', etc.

**Sessions Page** (`resources/js/pages/Sessions.tsx`):
- Main application landing page after login
- Implements localStorage monitoring for real-time session detection
- Automatically syncs new Voiceflow chat sessions to database
- Session switching with full browser navigation (not SPA routing)
- Complex state management for active session detection

**Key Session Flow**:
1. User starts chat → Voiceflow creates localStorage entry
2. Frontend monitors localStorage changes → Syncs to database
3. Sessions appear in sidebar automatically
4. Session switching loads new chat history via localStorage restoration

### Inertia.js Integration
- Uses React 19 with TypeScript for SPA-like experience
- SSR support enabled (`resources/js/ssr.tsx`)
- Custom layouts in `resources/js/layouts/`
- Path aliases: `@/*` maps to `resources/js/*`

### Component Architecture
- **App Layout**: Sidebar-based layout (`app-sidebar-layout.tsx`)
- **UI Components**: ShadCN UI in `resources/js/components/ui/`
- **Page Components**: Main pages in `resources/js/pages/`

### API Design
Session management uses dedicated API routes with JSON responses:
- `/api/sessions/register` - Create new session
- `/api/sessions/update` - Update session data  
- `/api/sessions/check` - Verify session exists

## File Structure Highlights

```
app/
├── Models/
│   ├── VoiceflowSession.php    # Chat session tracking
│   ├── CoachingSession.php     # Training session records
│   └── User.php                # Extended with PI assessments
├── Http/Controllers/
│   └── SessionsController.php  # Session management API
└── Filament/                   # Admin panel resources

resources/js/
├── pages/
│   └── Sessions.tsx           # Main session management page
├── layouts/
│   └── app/
│       └── app-sidebar-layout.tsx
├── components/
│   ├── ui/                    # ShadCN components
│   └── app-*.tsx             # App-specific components
└── types/                     # TypeScript definitions

routes/
├── web.php                    # Main application routes
└── api.php                    # API routes (session management)
```

## Important Development Notes

### Session Development Focus
Recent commits focus on multi-session chat functionality. When working on sessions:
- Monitor localStorage for `voiceflow-session-*` keys
- Session IDs are derived from Voiceflow's `userID` field
- Full page reloads used for session switching (not SPA navigation)
- Complex localStorage synchronization requires careful state management

### Code Standards
- **ESLint**: Modern flat config with React 19 + TypeScript rules
- **Prettier**: Automated formatting with Tailwind plugin
- **TypeScript**: Strict mode enabled, path aliases configured
- **PHP**: PSR-4 autoloading, Pest for testing

### H.G. Fenton Branding
- Primary Blue: `#1e4a72`
- Accent Orange/Gold: `#e67e00`
- Brand: H.G. Fenton Company
- Tagline: "Trust, Service and Innovation Since 1906"

## FluentBoards HTML Template for Task Descriptions

When creating tasks in FluentBoards, always use this HTML template structure for consistent, professional formatting:

```html
<div class="task-description">
  <h3>📋 Overview</h3>
  <p><strong>Brief summary of the task or feature</strong></p>
  
  <h3>🎯 Objectives</h3>
  <ul>
    <li>Primary objective 1</li>
    <li>Primary objective 2</li>
    <li>Primary objective 3</li>
  </ul>
  
  <h3>📝 Technical Details</h3>
  <ul>
    <li><strong>Technology Stack:</strong> Laravel 12, React 19, TypeScript, ShadCN UI, Framer Motion</li>
    <li><strong>Files Modified:</strong> <code>path/to/file.tsx</code>, <code>path/to/another.php</code></li>
    <li><strong>Key Components:</strong> ComponentName, AnotherComponent</li>
  </ul>
  
  <h3>✅ Acceptance Criteria</h3>
  <ul>
    <li>✓ Criterion 1 completed</li>
    <li>✓ Criterion 2 completed</li>
    <li>⏳ Criterion 3 in progress</li>
    <li>❌ Criterion 4 pending</li>
  </ul>
  
  <h3>🔗 Related Links</h3>
  <ul>
    <li><a href="http://btcs-coach.test/login" target="_blank">Live Demo</a></li>
    <li><a href="#" target="_blank">Documentation</a></li>
  </ul>
  
  <h3>📸 Screenshots</h3>
  <p><em>Add screenshots or visual references when applicable</em></p>
  
  <h3>💡 Notes</h3>
  <p><em>Additional context, blockers, or important considerations</em></p>
</div>
```