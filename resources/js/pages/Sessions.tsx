import { useState, useEffect, useRef } from 'react';
import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { MessageCircle, Calendar, Clock } from 'lucide-react';

// Extend window interface for Voiceflow
declare global {
    interface Window {
        voiceflow?: {
            chat?: {
                load: (config: any) => void;
            };
        };
    }
}

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    pi_behavioral_pattern_id?: number | null;
    pi_behavioral_pattern?: any | null;
    pi_raw_scores?: any | null;
    pi_assessed_at?: string | null;
    pi_notes?: string | null;
    pi_profile?: any | null;
    has_pi_assessment?: boolean;
    has_pi_profile?: boolean;
}

interface Session {
    session_id: string;
    last_turn?: any;
    created_at: string;
    updated_at: string;
}

interface SessionsProps {
    user: User;
    sessions: Record<string, Session>;
}

export default function Sessions({ user, sessions }: SessionsProps) {
    const { props } = usePage();
    const [selectedSessionId, setSelectedSessionId] = useState<string | null>(null);

    // Initialize Voiceflow script on component mount
    useEffect(() => {
        // Add Voiceflow script if not already present
        if (!document.querySelector('script[src*="widget.bundle.mjs"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.voiceflow.com/widget/bundle.mjs';
            script.async = true;
            script.type = 'module';
            script.crossOrigin = 'anonymous';
            document.head.appendChild(script);
        }
    }, []);

    useEffect(() => {
        // Initialize Voiceflow when component mounts
        const initializeVoiceflow = () => {
            // Add a small delay to ensure DOM is ready
            setTimeout(() => {
                console.log('Starting Voiceflow initialization...');
                const chatElement = document.getElementById('main-voiceflow-chat');
                console.log('Chat element found:', chatElement);
                
                // Use the provided Voiceflow embed script
                (function(d: Document, t: string) {
                    const v = d.createElement(t) as HTMLScriptElement;
                    const s = d.getElementsByTagName(t)[0];
                    v.onload = function() {
                        console.log('Voiceflow script loaded, window.voiceflow:', window.voiceflow);
                        if (window.voiceflow && window.voiceflow.chat) {
                            const targetElement = document.getElementById('main-voiceflow-chat');
                            console.log('Target element for Voiceflow:', targetElement);
                            
                            // Debug: Log the user payload being sent to Voiceflow
                            const payload = {
                                id: user?.id || 0,
                                name: user?.name || 'Anonymous',
                                email: user?.email || '',
                                role: user?.role || 'member',
                                pi_behavioral_pattern_id: user?.pi_behavioral_pattern_id || null,
                                pi_behavioral_pattern: user?.pi_behavioral_pattern || null,
                                pi_raw_scores: user?.pi_raw_scores || null,
                                pi_assessed_at: user?.pi_assessed_at || null,
                                pi_notes: user?.pi_notes || null,
                                pi_profile: user?.pi_profile || null,
                                has_pi_assessment: user?.has_pi_assessment || false,
                                has_pi_profile: user?.has_pi_profile || false
                            };
                            
                            console.log('🚀 Voiceflow User Payload:', JSON.stringify(payload, null, 2));
                            
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
                                    // Generate a date string to avoid caching
                                    stylesheet: '/voiceflow.css?v=' + new Date().toISOString().replace(/[:.]/g, '-')
                                },
                                autostart: true,
                                launch: {
                                    event: {
                                        type: 'launch',
                                        payload: payload
                                    }
                                }
                            });
                            
                            console.log('Voiceflow chat loaded');
                        } else {
                            console.error('Voiceflow widget failed to load - missing window.voiceflow');
                            const chatElement = document.getElementById('main-voiceflow-chat');
                            if (chatElement) {
                                chatElement.innerHTML = `
                                    <div class="flex items-center justify-center h-96 bg-gray-50 rounded-lg">
                                        <div class="text-center">
                                            <div class="text-gray-500 mb-2">Chat widget failed to load</div>
                                            <button onclick="location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                                Retry
                                            </button>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    };
                    v.src = 'https://cdn.voiceflow.com/widget/bundle.mjs';
                    v.type = 'module';
                    v.crossOrigin = 'anonymous';
                    s.parentNode?.insertBefore(v, s);
                })(document, 'script');
            }, 100);
        };

        // Initialize Voiceflow
        initializeVoiceflow();
    }, [user]);

    const formatSessionDate = (dateString: string) => {
        try {
            return new Date(dateString).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
        } catch {
            return 'Unknown';
        }
    };

    const getSessionStatus = (session: Session) => {
        return session.last_turn?.status || 'Unknown';
    };

    return (
        <AppLayout>
            <Head title="Sessions" />
            
            <div className="flex h-[calc(100vh-8rem)] gap-6">
                {/* Sessions Sidebar */}
                <div className="w-80 flex-shrink-0">
                    <Card className="h-full">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MessageCircle className="h-5 w-5" />
                                Your Sessions
                            </CardTitle>
                            <CardDescription>
                                {Object.keys(sessions).length} active session{Object.keys(sessions).length !== 1 ? 's' : ''}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="p-0">
                            <div className="h-[calc(100%-120px)] overflow-y-auto">
                                {Object.keys(sessions).length === 0 ? (
                                    <div className="p-6 text-center text-gray-500">
                                        <MessageCircle className="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                        <p>No active sessions yet.</p>
                                        <p className="text-sm mt-1">Start a conversation to create your first session!</p>
                                    </div>
                                ) : (
                                    <div className="p-4 space-y-3">
                                        {Object.entries(sessions).map(([sessionId, session]) => (
                                            <div
                                                key={sessionId}
                                                className={`p-3 rounded-lg border cursor-pointer transition-colors ${
                                                    selectedSessionId === sessionId
                                                        ? 'bg-blue-50 border-blue-200'
                                                        : 'bg-white hover:bg-gray-50'
                                                }`}
                                                onClick={() => setSelectedSessionId(sessionId)}
                                            >
                                                <div className="flex items-start justify-between mb-2">
                                                    <div className="font-medium text-sm truncate flex-1">
                                                        {sessionId.substring(0, 12)}...
                                                    </div>
                                                    <Badge
                                                        variant={getSessionStatus(session) === 'ACTIVE' ? 'default' : 'secondary'}
                                                        className="ml-2 text-xs"
                                                    >
                                                        {getSessionStatus(session)}
                                                    </Badge>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-500">
                                                    <Calendar className="h-3 w-3" />
                                                    <span>{formatSessionDate(session.created_at)}</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                                    <Clock className="h-3 w-3" />
                                                    <span>Updated {formatSessionDate(session.updated_at)}</span>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Main Chat Area */}
                <div className="flex-1">
                    <Card className="h-full">
                        <CardContent className="p-0 h-full">
                            <div 
                                id="main-voiceflow-chat" 
                                className="h-full w-full rounded-lg overflow-hidden"
                            >
                                <div className="flex items-center justify-center h-full bg-gray-50">
                                    <div className="text-center">
                                        <MessageCircle className="h-16 w-16 mx-auto mb-4 text-gray-300" />
                                        <p className="text-gray-500">Loading chat interface...</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}