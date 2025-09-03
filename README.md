# BTCS Coach Application

A Business Training & Coaching System built for H.G. Fenton Company using Laravel 12 with full React 19/TypeScript frontend via Inertia.js, complete H.G. Fenton branding, and comprehensive admin panel with Filament.

## Features

- **Text-based Coaching Sessions** - Interactive chat interface with Voiceflow integration
- **Audio Coaching Sessions** - Voice conversations using ElevenLabs ConvAI widget
- **Adaptive Card Extensions** - Custom Voiceflow extensions for enhanced user experience
- **User Management** - Authentication, roles, and profile management
- **Analytics Dashboard** - Comprehensive analytics and reporting
- **Module System** - Structured coaching modules and content
- **Action Items** - Task management and completion tracking

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: React 19 with TypeScript
- **Build Tool**: Vite
- **Styling**: Tailwind CSS + ShadCN UI components
- **Animations**: Framer Motion
- **Admin Panel**: Filament
- **Database**: MySQL/PostgreSQL
- **Voice AI**: ElevenLabs ConvAI
- **Chat AI**: Voiceflow integration

## Key Components

### Audio Sessions
- Full-screen ElevenLabs ConvAI widget integration
- Custom shadow DOM manipulation for seamless UI integration
- Automatic widget styling and footer removal
- Smooth fade-in animations after processing

### Voiceflow Extensions
- Adaptive card system for interactive coaching prompts
- Dynamic DOM manipulation for enhanced chat experience
- CSS variable-based theming system
- Overflow management and content detection

### User Experience
- H.G. Fenton branded interface
- Responsive design for all devices
- Session history and management
- Progressive enhancement with fallbacks

## Brand Colors

- **Primary Blue**: #1e4a72
- **Accent Orange/Gold**: #e67e00
- **Brand**: H.G. Fenton Company - Trust, Service and Innovation Since 1906

## Development

### Requirements
- PHP 8.2+
- Node.js 18+
- Composer
- MySQL/PostgreSQL

### Setup
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
php artisan serve
```

### Development Commands
```bash
# Watch and rebuild assets
npm run dev

# Build for production
npm run build

# Run tests
php artisan test

# Clear caches
php artisan optimize:clear
```

## Architecture

### Frontend Structure
- `/resources/js/pages/` - React page components
- `/resources/js/components/` - Reusable UI components
- `/resources/js/layouts/` - Layout wrapper components
- `/public/` - Static assets and custom scripts

### Backend Structure
- `/app/Http/Controllers/` - Request handling
- `/app/Models/` - Eloquent models
- `/database/migrations/` - Database schema
- `/routes/` - Application routing

### Custom Scripts
- `/public/adaptive-card-extension.js` - Voiceflow integration
- `/public/eleven-labs.js` - ElevenLabs widget customization
- `/public/voiceflow.css` - Custom styling for chat interface

## Deployment

The application is configured for deployment on Railway and other cloud platforms with proper asset building and environment configuration.

## License

Proprietary - H.G. Fenton Company