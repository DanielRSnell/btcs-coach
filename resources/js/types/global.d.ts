import type { route as routeFn } from 'ziggy-js';

declare global {
    const route: typeof routeFn;
    
    namespace JSX {
        interface IntrinsicElements {
            'elevenlabs-convai': React.DetailedHTMLProps<React.HTMLAttributes<HTMLElement>, HTMLElement> & {
                'agent-id'?: string;
                'server-location'?: string;
                'variant'?: string;
                'avatar-image-url'?: string;
                'avatar-orb-color-1'?: string;
                'avatar-orb-color-2'?: string;
                'action-text'?: string;
                'start-call-text'?: string;
                'listening-text'?: string;
                'dynamic-variables'?: string;
                style?: React.CSSProperties | string;
            };
        }
    }
}
