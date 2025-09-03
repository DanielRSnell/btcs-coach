import { useState, useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Mic, Volume2 } from 'lucide-react';

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

interface AudioSessionsProps {
    user: User;
}

export default function AudioSessions({ user }: AudioSessionsProps) {
    useEffect(() => {
        // Load ElevenLabs ConvAI widget script
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/@elevenlabs/convai-widget-embed';
        script.async = true;
        script.type = 'text/javascript';
        document.body.appendChild(script);

        return () => {
            // Cleanup script on unmount
            if (document.body.contains(script)) {
                document.body.removeChild(script);
            }
        };
    }, []);

    return (
        <AppLayout>
            <Head title="Audio Sessions" />
            
            <div className="flex h-[calc(100vh-8rem)] gap-6 pt-6">
                {/* Sessions Sidebar */}
                <div className="w-80 flex-shrink-0">
                    <Card className="h-full flex flex-col gap-1 py-0 pt-6">
                        <CardHeader className="flex-shrink-0">
                            <CardTitle className="flex items-center gap-2">
                                <Volume2 className="h-5 w-5" />
                                Audio Sessions
                            </CardTitle>
                            <CardDescription>
                                No audio sessions available yet
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="p-0 flex-1 flex flex-col min-h-0">
                            <div className="flex-1 overflow-y-auto no-scrollbar">
                                <div className="h-full flex flex-col items-center justify-center p-6 text-center">
                                    <div className="flex flex-col items-center space-y-4">
                                        <div className="p-6 bg-gray-50 rounded-full">
                                            <Mic className="h-10 w-10 text-gray-300" />
                                        </div>
                                        <div className="space-y-2">
                                            <h3 className="text-lg font-semibold text-gray-700">No audio sessions yet</h3>
                                            <p className="text-sm text-gray-500 max-w-sm">
                                                Audio session functionality is coming soon. Start a conversation to begin your voice coaching experience.
                                            </p>
                                        </div>
                                        <div className="flex items-center space-x-2 text-xs text-gray-400 mt-6">
                                            <div className="flex items-center space-x-1">
                                                <div className="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                                <span>Voice ready</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Content Area */}
                <div className="flex-1">
                    <Card className="h-full">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Volume2 className="h-5 w-5" />
                                Voice Coaching Assistant
                            </CardTitle>
                            <CardDescription>
                                Experience natural voice conversations with your AI coach
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="h-[calc(100%-5rem)] relative">
                            {/* ElevenLabs ConvAI Widget Container */}
                            <div className="w-full h-full flex items-center justify-center">
                                <elevenlabs-convai 
                                    agent-id="agent_0901k31ke64mf0w8me1gdwygb7ze"
                                    style={{
                                        opacity: '0',
                                        position: 'relative',
                                        width: '100%',
                                        height: '100%',
                                        minHeight: '400px',
                                        border: 'none',
                                        borderRadius: '8px'
                                    }}
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}