# Unused TSX Files Analysis

**Date:** 2025-10-08 (Updated)
**Purpose:** Document TSX files that are no longer used in the primary application after functionality has been reduced to focus on the Sessions-based chat system.

**Status:** ✅ Cleanup completed - unused files moved to `_archive/`

## Executive Summary

The BTCS Coach application has been streamlined to focus on a multi-session chat interface powered by Voiceflow and ElevenLabs. Unused UI components and pages from the original scaffolding have been archived.

## Current Active Routes & Pages

### Primary Application Routes (web.php)
- **/** → Redirects to login
- **/sessions** → Main sessions page (Sessions.tsx)
- **/sessions/new** → New session page (NewSession.tsx)
- **/sessions/audio** → Audio session page (sessions/audio.tsx)
- **/sessions/{sessionId}** → Session detail page (Sessions.tsx with session context)
- **/analytics** → Analytics page (analytics.tsx)

### Settings Routes (settings.php)
- **/settings/profile** → Profile settings (settings/profile.tsx)
- **/settings/password** → Password settings (settings/password.tsx)
- **/settings/appearance** → Appearance settings (settings/appearance.tsx)

### Auth Routes (auth.php)
- **/login** → Login page (auth/login.tsx)
- **/register** → Registration page (auth/register.tsx)
- **/forgot-password** → Password reset request (auth/forgot-password.tsx)
- **/reset-password/{token}** → Password reset form (auth/reset-password.tsx)
- **/verify-email** → Email verification (auth/verify-email.tsx)
- **/confirm-password** → Password confirmation (auth/confirm-password.tsx)

## Active Files & Their Purpose

### Core Application Files
- ✅ **app.tsx** - Inertia.js app initialization
- ✅ **ssr.tsx** - Server-side rendering entry point

### Primary Pages (ACTIVE)
- ✅ **pages/Sessions.tsx** - Main sessions management page with Voiceflow/ElevenLabs integration
- ✅ **pages/NewSession.tsx** - Create new session page
- ✅ **pages/sessions/audio.tsx** - Audio-focused session page
- ✅ **pages/analytics.tsx** - Analytics dashboard

### Settings Pages (ACTIVE)
- ✅ **pages/settings/profile.tsx** - User profile management
- ✅ **pages/settings/password.tsx** - Password change form
- ✅ **pages/settings/appearance.tsx** - Theme/appearance settings

### Auth Pages (ACTIVE)
- ✅ **pages/auth/login.tsx** - Login form
- ✅ **pages/auth/register.tsx** - Registration form
- ✅ **pages/auth/forgot-password.tsx** - Password reset request
- ✅ **pages/auth/reset-password.tsx** - Password reset form
- ✅ **pages/auth/verify-email.tsx** - Email verification prompt
- ✅ **pages/auth/confirm-password.tsx** - Password confirmation

### Active Layouts
- ✅ **layouts/app-layout.tsx** - Main app wrapper
- ✅ **layouts/auth-layout.tsx** - Auth pages wrapper
- ✅ **layouts/settings/layout.tsx** - Settings pages layout
- ✅ **layouts/auth/auth-split-layout.tsx** - Split auth layout
- ✅ **layouts/auth/auth-card-layout.tsx** - Card-style auth layout
- ✅ **layouts/auth/auth-simple-layout.tsx** - Simple auth layout
- ✅ **layouts/app/app-sidebar-layout.tsx** - Sidebar-based app layout (used by Sessions.tsx)
- ✅ **layouts/app/app-header-layout.tsx** - Header-based app layout

### Active Components (Referenced in Sessions.tsx)
- ✅ **components/ui/*** - All ShadCN UI components (actively used)
- ✅ **components/app-sidebar.tsx** - Application sidebar
- ✅ **components/app-sidebar-header.tsx** - Sidebar header
- ✅ **components/user-menu-content.tsx** - User menu dropdown
- ✅ **components/app-logo.tsx** - Application logo
- ✅ **components/app-logo-icon.tsx** - Logo icon variant
- ✅ **components/new-session-modal.tsx** - New session creation modal
- ✅ **components/PIChartModal.tsx** - PI assessment chart modal
- ✅ **components/PIChatDrawer.tsx** - PI chat drawer component

### Active Components (Verified in Forms/Settings)
- ✅ **components/input-error.tsx** - Used in auth and settings forms
- ✅ **components/delete-user.tsx** - Used in profile settings
- ✅ **components/appearance-tabs.tsx** - Used in settings/appearance.tsx

### Active Hooks
- ✅ **hooks/use-mobile.tsx** - Mobile detection hook
- ✅ **hooks/use-initials.tsx** - User initials generator
- ✅ **hooks/use-appearance.tsx** - Theme/appearance management

## ARCHIVED Files (Moved to `_archive/`)

### Archived Pages → `_archive/pages/`
- ❌ **welcome.tsx** - Original welcome/landing page (replaced by Sessions.tsx)
- ❌ **Dashboard.tsx** - Original dashboard (replaced by Sessions.tsx)
- ❌ **modules.tsx** - Training modules page (feature removed)
- ❌ **module-detail.tsx** - Module detail view (feature removed)
- ❌ **module-chat.tsx** - Module-based chat (replaced by unified Sessions chat)

### Archived Components → `_archive/components/`
- ❌ **user-info.tsx** - User info display component
- ❌ **text-link.tsx** - Text link component
- ❌ **nav-user.tsx** - Navigation user component (replaced by sidebar)
- ❌ **nav-main.tsx** - Main navigation component (replaced by sidebar)
- ❌ **nav-footer.tsx** - Navigation footer
- ❌ **icon.tsx** - Icon wrapper component (using lucide-react directly)
- ❌ **heading.tsx** - Heading component
- ❌ **heading-small.tsx** - Small heading variant
- ❌ **breadcrumbs.tsx** - Breadcrumb navigation
- ❌ **app-shell.tsx** - App shell wrapper (replaced by layouts)
- ❌ **app-content.tsx** - App content wrapper (replaced by layouts)
- ❌ **app-header.tsx** - App header component (replaced by sidebar layout)

## Architecture Notes

### Session-Centric Design
The application has evolved from a modular training system to a unified session-based chat interface:

1. **Old Flow:** Users → Modules → Module Chat → Training Sessions
2. **New Flow:** Users → Sessions → Unified Chat (Text/Audio modes)

### Key Architectural Decisions
- **Removed Module System:** Training modules are no longer separate entities
- **Unified Chat Interface:** Single Sessions.tsx handles both text (Voiceflow) and audio (ElevenLabs)
- **localStorage Synchronization:** Real-time session tracking via localStorage monitoring
- **Full Page Reloads:** Session switching uses browser navigation instead of SPA routing

### Technologies Still in Use
- React 19 + TypeScript
- Inertia.js (SSR support)
- ShadCN UI components
- TailwindCSS 4
- Framer Motion (for animations)
- Voiceflow (chat)
- ElevenLabs (audio)

## Cleanup Summary

### Files Moved to Archive
```bash
resources/js/_archive/
├── pages/
│   ├── welcome.tsx
│   ├── Dashboard.tsx
│   ├── modules.tsx
│   ├── module-detail.tsx
│   └── module-chat.tsx
└── components/
    ├── user-info.tsx
    ├── text-link.tsx
    ├── nav-user.tsx
    ├── nav-main.tsx
    ├── nav-footer.tsx
    ├── icon.tsx
    ├── heading.tsx
    ├── heading-small.tsx
    ├── breadcrumbs.tsx
    ├── app-shell.tsx
    ├── app-content.tsx
    └── app-header.tsx
```

### Files Kept (Verified Active)
- All files in `components/ui/` (ShadCN components)
- All auth pages and layouts
- All settings pages and layouts
- Active session-related pages
- All hooks
- Form-related components (input-error, delete-user, appearance-tabs)

## Migration Path

If you need to restore module-based functionality:

1. Files are preserved in `_archive` folder
2. Database tables still exist (modules, coaching_sessions)
3. Filament admin can still manage module data
4. Restore from archive and update routes

## Notes for Future Development

- **Component Library:** Consider creating a formal component library for reusable UI elements
- **Dead Code Elimination:** Run build analysis to identify truly unused imports
- **TypeScript Cleanup:** Remove type definitions for archived components
- **Build Optimization:** Vite tree-shaking should handle most unused code automatically

---

**Last Updated:** 2025-10-08
**Maintained By:** Development Team
**Review Frequency:** Quarterly or after major architectural changes
