import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: async (name) => {
        console.log('Inertia resolving:', name);
        console.log('Environment:', import.meta.env.MODE);
        const allPages = import.meta.glob('./pages/**/*.tsx');
        console.log('Available pages:', Object.keys(allPages));
        
        try {
            return await resolvePageComponent(`./pages/${name}.tsx`, allPages);
        } catch (error) {
            console.error('Failed to resolve page:', name, error);
            
            // Try common variations for Dashboard specifically
            if (name === 'Dashboard') {
                console.log('Trying Dashboard fallbacks...');
                
                const fallbacks = [
                    './pages/dashboard.tsx',
                    './pages/Dashboard.tsx',
                    'Dashboard',
                    'dashboard'
                ];
                
                for (const fallback of fallbacks) {
                    try {
                        console.log('Trying fallback:', fallback);
                        if (fallback.includes('./')) {
                            return await resolvePageComponent(fallback, allPages);
                        } else {
                            return await resolvePageComponent(`./pages/${fallback}.tsx`, allPages);
                        }
                    } catch (fallbackError) {
                        console.log('Fallback failed:', fallback, fallbackError);
                    }
                }
            }
            
            throw error;
        }
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
