import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { MessageCircle, Mic, ArrowRight } from 'lucide-react';

export default function NewSession() {
    const clearLocalStorageAndNavigate = () => {
        // Clear all voiceflow session data from localStorage
        console.log('🧹 CLEARING: Starting new text session - removing all voiceflow localStorage data');
        
        const voiceflowKeys = Object.keys(localStorage).filter(key => 
            key.startsWith('voiceflow-session-')
        );
        
        console.log(`🗑️ Found ${voiceflowKeys.length} voiceflow sessions to clear:`, voiceflowKeys);
        
        voiceflowKeys.forEach(key => {
            console.log(`🗑️ Removing: ${key}`);
            localStorage.removeItem(key);
        });
        
        console.log('✅ localStorage cleared - navigating to sessions');
    };

    const handleStartAudioSession = () => {
        // Navigate to audio sessions page
        window.location.href = '/sessions/audio';
    };

    return (
        <AppLayout>
            <Head title="New Session" />
            
            <div className="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
                <div className="w-full max-w-4xl">
                    <div className="text-center mb-12">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">
                            Start a New Coaching Session
                        </h1>
                        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                            Choose how you'd like to interact with your AI coach. You can have a text-based conversation or try our new voice session feature.
                        </p>
                    </div>

                    <div className="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                        {/* Text Session Card */}
                        <Card className="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-2 hover:border-blue-300">
                            <CardHeader className="pb-4">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="p-3 bg-blue-100 rounded-full">
                                        <MessageCircle className="h-6 w-6 text-blue-600" />
                                    </div>
                                    <CardTitle className="text-xl">Text Session</CardTitle>
                                </div>
                                <CardDescription className="text-base">
                                    Start a conversation with your AI coach through text messages. Perfect for detailed discussions and getting comprehensive guidance.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="pt-2">
                                <div className="space-y-3 mb-6">
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Instant responses</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Session history saved</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Personalized coaching</span>
                                    </div>
                                </div>
                                <a 
                                    href="/sessions"
                                    onClick={clearLocalStorageAndNavigate}
                                    className="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 w-full group-hover:bg-blue-700 bg-blue-600 hover:bg-blue-700 text-white h-11 px-8"
                                >
                                    Start Text Session
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </a>
                            </CardContent>
                        </Card>

                        {/* Audio Session Card */}
                        <Card className="relative overflow-hidden group hover:shadow-xl transition-all duration-300 border-2 hover:border-orange-300">
                            <CardHeader className="pb-4">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className="p-3 bg-orange-100 rounded-full">
                                        <Mic className="h-6 w-6 text-orange-600" />
                                    </div>
                                    <CardTitle className="text-xl">Audio Session</CardTitle>
                                </div>
                                <CardDescription className="text-base">
                                    Have a natural voice conversation with your AI coach through audio interaction. Perfect for hands-free coaching and immersive guidance sessions.
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="pt-2">
                                <div className="space-y-3 mb-6">
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-orange-500 rounded-full"></div>
                                        <span>Natural conversation</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-orange-500 rounded-full"></div>
                                        <span>Voice recognition</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-gray-600">
                                        <div className="w-2 h-2 bg-orange-500 rounded-full"></div>
                                        <span>Hands-free interaction</span>
                                    </div>
                                </div>
                                <Button 
                                    onClick={handleStartAudioSession}
                                    className="w-full group-hover:bg-orange-700 bg-orange-600 hover:bg-orange-700 text-white"
                                    size="lg"
                                >
                                    Start Audio Session
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Button>
                            </CardContent>
                            <div className="absolute top-4 right-4">
                                <span className="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    Beta
                                </span>
                            </div>
                        </Card>
                    </div>

                    <div className="text-center mt-8">
                        <p className="text-sm text-gray-500">
                            Your coaching sessions are private and secure. All conversations are tailored to your personal development goals.
                        </p>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}