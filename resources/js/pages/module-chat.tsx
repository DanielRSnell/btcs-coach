import { Head } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { ArrowLeft, Clock, BookOpen, Target, CheckCircle, Circle, AlertCircle } from "lucide-react";
import { Link, router, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";

// TypeScript declaration for Voiceflow global object
declare global {
    interface Window {
        voiceflow: {
            chat: {
                load: (config: any) => void;
                destroy: () => void;
            };
        };
    }
}

interface Module {
    id: number;
    title: string;
    description: string;
    goal: string;
    slug: string;
    type: 'coaching' | 'training' | 'assessment';
    topics: string[];
    sample_questions: string[];
    learning_objectives: string;
    expected_outcomes: string;
    estimated_duration: number;
    difficulty: 'beginner' | 'intermediate' | 'advanced';
}

interface ActionItem {
    id: number;
    title: string;
    description: string;
    priority: 'low' | 'medium' | 'high';
    status: 'pending' | 'in_progress' | 'completed';
    due_date: string | null;
    context: string | null;
}

interface ModuleChatPageProps {
    module: Module;
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
        pi_behavioral_pattern_id: number | null;
        pi_behavioral_pattern: {
            id: number;
            name: string;
            code: string;
            description: string;
        } | null;
        pi_raw_scores: {
            dominance: number;
            extraversion: number;
            patience: number;
            formality: number;
        } | null;
        pi_assessed_at: string | null;
        pi_notes: string | null;
        pi_profile: any | null;
        has_pi_assessment: boolean;
        has_pi_profile: boolean;
    } | null;
    actionItems?: ActionItem[];
}

const typeColors = {
    coaching: 'bg-green-100 text-green-800',
    training: 'bg-yellow-100 text-yellow-800',
    assessment: 'bg-blue-100 text-blue-800',
};

const difficultyColors = {
    beginner: 'bg-emerald-100 text-emerald-800',
    intermediate: 'bg-amber-100 text-amber-800',
    advanced: 'bg-red-100 text-red-800',
};

const priorityColors = {
    low: 'bg-blue-100 text-blue-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-red-100 text-red-800',
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'completed':
            return <CheckCircle className="h-4 w-4 text-green-500" />;
        case 'in_progress':
            return <AlertCircle className="h-4 w-4 text-yellow-500" />;
        default:
            return <Circle className="h-4 w-4 text-gray-400" />;
    }
};

export default function ModuleChat({ module, user, actionItems = [] }: ModuleChatPageProps) {
    const [selectedActionItem, setSelectedActionItem] = useState<ActionItem | null>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isUpdating, setIsUpdating] = useState(false);
    const { props } = usePage();
    const successMessage = (props as any).flash?.success;

    // Handle flash success message for user feedback
    useEffect(() => {
        if (successMessage) {
            // You could replace this with a toast notification system if preferred
            console.log('Success:', successMessage);
        }
    }, [successMessage]);

    const handleActionItemClick = (item: ActionItem) => {
        setSelectedActionItem(item);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedActionItem(null);
        setIsUpdating(false); // Reset updating state
    };

    const handleMarkAsCompleted = () => {
        if (!selectedActionItem) return;
        
        setIsUpdating(true);
        router.put(`/action-items/${selectedActionItem.id}/complete`, {}, {
            preserveState: false, // Allow state refresh to show updated action items
            preserveScroll: true,
            onSuccess: (page) => {
                // Close modal immediately after successful response
                setIsModalOpen(false);
                setSelectedActionItem(null);
                setIsUpdating(false);
                
                // Provide user feedback
                console.log('Action item marked as completed successfully');
            },
            onError: (errors) => {
                console.error('Failed to update action item:', errors);
                alert('Failed to update action item. Please try again.');
                setIsUpdating(false);
            },
            onFinish: () => {
                setIsUpdating(false);
            }
        });
    };
    useEffect(() => {
        // Initialize Voiceflow when component mounts
        const initializeVoiceflow = () => {
            // Add a small delay to ensure DOM is ready
            setTimeout(() => {
                console.log('Starting Voiceflow initialization...');
                const chatElement = document.getElementById('btcs-chat');
                console.log('Chat element found:', chatElement);
                
                // Use the provided Voiceflow embed script
                (function(d, t) {
                    var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
                    v.onload = function() {
                        console.log('Voiceflow script loaded, window.voiceflow:', window.voiceflow);
                        if (window.voiceflow && window.voiceflow.chat) {
                            const targetElement = document.getElementById('btcs-chat');
                            console.log('Target element for Voiceflow:', targetElement);
                            
                            // Debug: Log the complete payload being sent to Voiceflow
                            const payload = {
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
                                    description: module.description,
                                    goal: module.goal,
                                    type: module.type,
                                    slug: module.slug,
                                    topics: module.topics,
                                    topics_string: module.topics.join(', '),
                                    sample_questions: module.sample_questions,
                                    learning_objectives: module.learning_objectives,
                                    expected_outcomes: module.expected_outcomes,
                                    estimated_duration: module.estimated_duration,
                                    difficulty: module.difficulty
                                },
                                user: {
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
                                },
                                session_context: 'pi_ssl_coaching'
                            };
                            
                            console.log('🚀 Voiceflow Payload:', JSON.stringify(payload, null, 2));
                            
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
                                },
                            });
                            console.log('Voiceflow chat loaded for module:', module.title);
                        } else {
                            console.error('Voiceflow widget failed to load - missing window.voiceflow');
                            // Show fallback message
                            const chatDiv = document.getElementById('btcs-chat');
                            if (chatDiv) {
                                chatDiv.innerHTML = `
                                    <div class="flex items-center justify-center h-full text-gray-500">
                                        <div class="text-center">
                                            <p class="text-lg font-medium text-red-600 mb-2">Chat Temporarily Unavailable</p>
                                            <p class="text-sm">Please refresh the page or try again later.</p>
                                            <p class="text-xs mt-2 text-gray-400">Error: Voiceflow widget failed to initialize</p>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    };
                    v.onerror = function() {
                        console.error('Failed to load Voiceflow script from CDN');
                        // Show error message in chat div
                        const chatDiv = document.getElementById('btcs-chat');
                        if (chatDiv) {
                            chatDiv.innerHTML = `
                                <div class="flex items-center justify-center h-full text-gray-500">
                                    <div class="text-center">
                                        <p class="text-lg font-medium text-red-600 mb-2">Chat Service Unavailable</p>
                                        <p class="text-sm">Unable to connect to chat service.</p>
                                        <p class="text-xs mt-2 text-gray-400">Please contact support if this issue persists.</p>
                                    </div>
                                </div>
                            `;
                        }
                    };
                    v.src = "https://cdn.voiceflow.com/widget-next/bundle.mjs";
                    v.type = "text/javascript";
                    console.log('Adding Voiceflow script to DOM...');
                    s.parentNode.insertBefore(v, s);
                })(document, 'script');
            }, 100); // 100ms delay to ensure DOM is ready
            
            console.log('Voiceflow initialization started for module:', module.title);
        };

        // Clean up any existing Voiceflow instances
        const cleanup = () => {
            if (window.voiceflow && window.voiceflow.chat) {
                try {
                    window.voiceflow.chat.destroy();
                    console.log('Voiceflow chat destroyed');
                } catch (e) {
                    console.warn('Error cleaning up Voiceflow:', e);
                }
            }
        };

        cleanup();
        initializeVoiceflow();

        // Cleanup on unmount
        return cleanup;
    }, [module, user]);

    return (
        <AppLayout>
            <Head title={`${module.title} - Interactive Session`} />
            
            <div className="flex gap-6" style={{ height: 'calc(100vh - 200px)' }}>
                {/* Main Chat Area */}
                <div className="flex-1 flex flex-col">
                    {/* Chat Header */}
                    <div className="flex items-center gap-4 mb-4">
                        <Link href="/modules">
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="h-4 w-4 mr-2" />
                                Back to Modules
                            </Button>
                        </Link>
                        <div className="flex-1">
                            <h1 className="text-xl font-bold text-gray-900">{module.title}</h1>
                            <p className="text-sm text-gray-600">Interactive Coaching Session</p>
                        </div>
                        <div className="flex gap-2">
                            <Button variant="outline" size="sm">
                                Save Progress
                            </Button>
                            <Button variant="outline" size="sm">
                                End Session
                            </Button>
                        </div>
                    </div>

                    {/* Chat Interface Container - Full Height */}
                    <Card className="flex-1 flex flex-col py-0" style={{ minHeight: '500px', maxHeight: 'calc(100vh - 280px)' }}>
                        <CardContent className="p-0 flex-1">
                            {/* Voiceflow Chat Container */}
                            <div 
                                id="btcs-chat" 
                                className="w-full h-full"
                                style={{ maxHeight: 'calc(100vh - 200px)' }}
                            >
                                {/* This div will be populated by Voiceflow's embed script */}
                                <div className="flex items-center justify-center h-full text-gray-500">
                                    <div className="text-center">
                                        <div className="animate-pulse">
                                            <div className="w-8 h-8 bg-blue-200 rounded-full mx-auto mb-4"></div>
                                        </div>
                                        <p>Initializing your coaching session...</p>
                                        <p className="text-sm mt-2">Your AI coach will appear here shortly.</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Right Sidebar */}
                <div className="w-80">
                    <Tabs defaultValue="info" className="w-full">
                        <TabsList className="grid w-full grid-cols-2">
                            <TabsTrigger value="info">Info</TabsTrigger>
                            <TabsTrigger value="actions">Actions</TabsTrigger>
                        </TabsList>
                        
                        <TabsContent value="info" className="space-y-4 mt-4">
                            {/* Module Info Card */}
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardTitle className="text-lg">{module.title}</CardTitle>
                                    <CardDescription className="text-sm">
                                        {module.description}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div className="flex items-center gap-2">
                                        <Badge className={typeColors[module.type]}>
                                            {module.type}
                                        </Badge>
                                        <Badge variant="outline" className={difficultyColors[module.difficulty]}>
                                            {module.difficulty}
                                        </Badge>
                                    </div>
                                    <div className="flex items-center gap-1 text-sm text-gray-500">
                                        <Clock className="h-4 w-4" />
                                        <span>{module.estimated_duration} minutes</span>
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Session Overview Card */}
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardTitle className="flex items-center gap-2 text-lg">
                                        <Target className="h-5 w-5" />
                                        Session Overview
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {module.topics && module.topics.length > 0 && (
                                        <div>
                                            <div className="flex items-center gap-2 mb-2">
                                                <BookOpen className="h-4 w-4 text-gray-500" />
                                                <span className="text-sm font-medium text-gray-700">Key Topics:</span>
                                            </div>
                                            <div className="flex flex-wrap gap-1">
                                                {module.topics.map((topic, index) => (
                                                    <Badge key={index} variant="secondary" className="text-xs">
                                                        {topic}
                                                    </Badge>
                                                ))}
                                            </div>
                                        </div>
                                    )}

                                    {module.learning_objectives && (
                                        <div>
                                            <div className="flex items-center gap-2 mb-2">
                                                <Target className="h-4 w-4 text-gray-500" />
                                                <span className="text-sm font-medium text-gray-700">Learning Objectives:</span>
                                            </div>
                                            <p className="text-sm text-gray-600 leading-relaxed">
                                                {module.learning_objectives}
                                            </p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            {/* Progress Card */}
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardTitle className="text-lg">Session Progress</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-2">
                                        <div className="flex justify-between text-sm">
                                            <span className="text-gray-600">Status</span>
                                            <span className="font-medium text-green-600">In Progress</span>
                                        </div>
                                        <div className="flex justify-between text-sm">
                                            <span className="text-gray-600">Started</span>
                                            <span className="font-medium">Just now</span>
                                        </div>
                                        <div className="flex justify-between text-sm">
                                            <span className="text-gray-600">Estimated Time</span>
                                            <span className="font-medium">{module.estimated_duration} min</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                        
                        <TabsContent value="actions" className="space-y-4 mt-4">
                            <Card>
                                <CardHeader className="pb-3">
                                    <CardTitle className="text-lg">Module Action Items</CardTitle>
                                    <CardDescription className="text-sm">
                                        Your personalized tasks and objectives for {module.title}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {actionItems.length === 0 ? (
                                        <div className="text-center py-6 text-gray-500">
                                            <Target className="h-8 w-8 mx-auto mb-2 text-gray-300" />
                                            <p className="text-sm">No action items for this module yet</p>
                                            <p className="text-xs mt-1">Complete coaching sessions to generate personalized tasks</p>
                                        </div>
                                    ) : (
                                        <div className="space-y-3">
                                            {actionItems.map((item) => (
                                                <motion.div 
                                                    key={item.id} 
                                                    className="border rounded-lg p-3 space-y-2 cursor-pointer hover:bg-gray-50 transition-colors"
                                                    onClick={() => handleActionItemClick(item)}
                                                    whileHover={{ scale: 1.01 }}
                                                    whileTap={{ scale: 0.99 }}
                                                >
                                                    <div className="flex items-start gap-2">
                                                        {getStatusIcon(item.status)}
                                                        <div className="flex-1 min-w-0">
                                                            <h4 className="text-sm font-medium text-gray-900 truncate">
                                                                {item.title}
                                                            </h4>
                                                            <p className="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                {item.description}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div className="flex items-center justify-between">
                                                        <Badge variant="outline" className={`text-xs ${priorityColors[item.priority]}`}>
                                                            {item.priority}
                                                        </Badge>
                                                        {item.due_date && (
                                                            <span className="text-xs text-gray-500">
                                                                Due {new Date(item.due_date).toLocaleDateString()}
                                                            </span>
                                                        )}
                                                    </div>
                                                    
                                                    {item.context && (
                                                        <div className="text-xs text-gray-500 italic truncate">
                                                            {item.context}
                                                        </div>
                                                    )}
                                                </motion.div>
                                            ))}
                                        </div>
                                    )}
                                </CardContent>
                            </Card>
                        </TabsContent>
                    </Tabs>
                </div>
            </div>

            {/* Action Item Modal */}
            <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
                <DialogContent className="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle className="flex items-center gap-2">
                            {selectedActionItem && getStatusIcon(selectedActionItem.status)}
                            {selectedActionItem?.title}
                        </DialogTitle>
                        <DialogDescription>
                            Action item details and progress tracking
                        </DialogDescription>
                    </DialogHeader>

                    {selectedActionItem && (
                        <div className="space-y-6">
                            {/* Status and Priority Info */}
                            <div className="flex items-center gap-4">
                                <div className="flex items-center gap-2">
                                    <span className="text-sm font-medium text-gray-700">Status:</span>
                                    <Badge variant={selectedActionItem.status === 'completed' ? 'default' : 'secondary'}>
                                        {selectedActionItem.status.replace('_', ' ')}
                                    </Badge>
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="text-sm font-medium text-gray-700">Priority:</span>
                                    <Badge variant="outline" className={priorityColors[selectedActionItem.priority]}>
                                        {selectedActionItem.priority}
                                    </Badge>
                                </div>
                            </div>

                            {/* Due Date */}
                            {selectedActionItem.due_date && (
                                <div className="flex items-center gap-2">
                                    <Clock className="h-4 w-4 text-gray-500" />
                                    <span className="text-sm text-gray-700">
                                        Due: {new Date(selectedActionItem.due_date).toLocaleDateString('en-US', {
                                            weekday: 'long',
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric'
                                        })}
                                    </span>
                                </div>
                            )}

                            {/* Description */}
                            <div>
                                <h4 className="text-sm font-medium text-gray-900 mb-2">Description</h4>
                                <p className="text-sm text-gray-700 leading-relaxed">
                                    {selectedActionItem.description}
                                </p>
                            </div>

                            {/* Context */}
                            {selectedActionItem.context && (
                                <div>
                                    <h4 className="text-sm font-medium text-gray-900 mb-2">Context</h4>
                                    <p className="text-sm text-gray-600 italic">
                                        {selectedActionItem.context}
                                    </p>
                                </div>
                            )}

                            {/* Module Info */}
                            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div className="flex items-center gap-2 mb-2">
                                    <BookOpen className="h-4 w-4 text-blue-600" />
                                    <h4 className="text-sm font-medium text-blue-900">Related Module</h4>
                                </div>
                                <p className="text-sm text-blue-800">{module.title}</p>
                                <p className="text-xs text-blue-600 mt-1">{module.description}</p>
                            </div>
                        </div>
                    )}

                    <DialogFooter>
                        <Button variant="outline" onClick={handleCloseModal}>
                            Close
                        </Button>
                        {selectedActionItem?.status !== 'completed' && (
                            <Button 
                                onClick={handleMarkAsCompleted}
                                disabled={isUpdating}
                            >
                                {isUpdating ? 'Updating...' : 'Mark as Completed'}
                            </Button>
                        )}
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}