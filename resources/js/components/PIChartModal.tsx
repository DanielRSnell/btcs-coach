import { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogPortal, DialogOverlay } from '@/components/ui/dialog';
import { FileImage } from 'lucide-react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

// Extend window interface
declare global {
    interface Window {
        profile?: {
            chart?: {
                show: (user?: any | any[]) => void; // Can accept single user or array of users
                hide: () => void;
            };
        };
    }
}

interface PIChartModalProps {
    user?: any;
}

export default function PIChartModal({ user: initialUser }: PIChartModalProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [users, setUsers] = useState<any[]>([initialUser]);
    const [selectedUserId, setSelectedUserId] = useState<number | null>(initialUser?.id || null);

    // Set up window functions
    useEffect(() => {
        // Initialize the profile object if it doesn't exist
        if (!window.profile) {
            window.profile = {};
        }

        // Create the chart object with show/hide functions
        window.profile.chart = {
            show: (userData?: any) => {
                console.log('📊 PI Chart modal show() called', userData);

                // Handle multiple users (array of user IDs or user objects)
                if (Array.isArray(userData)) {
                    console.log('📊 Multiple users provided:', userData);
                    setUsers(userData);
                    setSelectedUserId(userData[0]?.id || userData[0]);
                } else {
                    // Single user or no user data - use current user as fallback
                    const userToShow = userData || initialUser;
                    console.log('📊 Using single user data:', userToShow);
                    setUsers([userToShow]);
                    setSelectedUserId(userToShow?.id);
                }

                setIsOpen(true);
            },
            hide: () => {
                console.log('📊 PI Chart modal hide() called');
                setIsOpen(false);
            }
        };

        console.log('✅ window.profile.chart functions registered');

        // Cleanup function
        return () => {
            if (window.profile?.chart) {
                delete window.profile.chart;
                console.log('🧹 Cleaned up window.profile.chart');
            }
        };
    }, []);

    // Update users when prop changes
    useEffect(() => {
        if (initialUser) {
            setUsers([initialUser]);
            setSelectedUserId(initialUser.id);
        }
    }, [initialUser]);

    // Get the currently selected user
    const currentUser = users.find(user => user?.id === selectedUserId) || users[0];

    // Check if current user has PI chart
    const hasChart = currentUser?.pi_chart_image;
    const chartUrl = currentUser?.pi_chart_image;
    const piPattern = currentUser?.pi_behavioral_pattern || currentUser?.piBehavioralPattern;
    const assessedAt = currentUser?.pi_assessed_at;

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogPortal>
                <DialogOverlay
                    className="backdrop-blur-md bg-black/90"
                    style={{ zIndex: 99999999999999998 }}
                />
                <DialogContent className="max-w-6xl max-h-[90vh] overflow-y-auto" style={{ zIndex: 99999999999999999 }}>
                <DialogHeader>
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-blue-100 rounded-lg">
                            <FileImage className="h-5 w-5 text-blue-600" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl">
                                {currentUser?.name ? `${currentUser.name}'s PI Chart` : 'PI Behavioral Chart'}
                            </DialogTitle>
                            <DialogDescription>
                                Predictive Index behavioral assessment visualization
                            </DialogDescription>
                        </div>
                    </div>
                </DialogHeader>

                {users.length > 1 ? (
                    <Tabs value={selectedUserId?.toString()} onValueChange={(value) => setSelectedUserId(parseInt(value))}>
                        <div className="flex gap-6">
                            {/* Tabs on the left */}
                            <div className="w-64 flex-shrink-0">
                                <TabsList className="flex flex-col h-auto w-full">
                                    {users.map((user) => (
                                        <TabsTrigger
                                            key={user?.id}
                                            value={user?.id?.toString()}
                                            className="w-full justify-start text-left"
                                        >
                                            {user?.name || 'Unknown User'}
                                        </TabsTrigger>
                                    ))}
                                </TabsList>
                            </div>

                            {/* Chart content on the right */}
                            <div className="flex-1">
                                {users.map((user) => (
                                    <TabsContent key={user?.id} value={user?.id?.toString()} className="mt-0">
                                        <div className="text-center">
                                            {user?.pi_chart_image ? (
                                                <div className="flex justify-center">
                                                    <div className="relative bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                                        <img
                                                            src={user.pi_chart_image}
                                                            alt={`PI Chart for ${user.name}`}
                                                            className="max-w-full max-h-[600px] object-contain"
                                                            onError={(e) => {
                                                                console.error('Failed to load PI chart image:', user.pi_chart_image);
                                                                const target = e.target as HTMLImageElement;
                                                                target.style.display = 'none';
                                                                const parent = target.parentElement;
                                                                if (parent) {
                                                                    parent.innerHTML = `
                                                                        <div class="flex items-center justify-center h-64 bg-gray-50 text-gray-500">
                                                                            <div class="text-center">
                                                                                <p class="text-lg">Failed to load chart image</p>
                                                                                <p class="text-sm mt-1">Image may not be accessible</p>
                                                                            </div>
                                                                        </div>
                                                                    `;
                                                                }
                                                            }}
                                                            onLoad={() => {
                                                                console.log('✅ PI chart image loaded successfully:', user.pi_chart_image);
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className="text-center py-12">
                                                    <div className="flex flex-col items-center space-y-4">
                                                        <div className="p-6 bg-gray-50 rounded-full">
                                                            <FileImage className="h-12 w-12 text-gray-300" />
                                                        </div>
                                                        <div className="space-y-2">
                                                            <h4 className="font-medium text-lg text-gray-700">No PI Chart Available</h4>
                                                            <p className="text-sm text-gray-500">
                                                                {user?.name} doesn't have a PI chart image uploaded yet.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </TabsContent>
                                ))}
                            </div>
                        </div>
                    </Tabs>
                ) : (
                    // Single user view
                    <div className="text-center">
                        {hasChart ? (
                            <div className="flex justify-center">
                                <div className="relative bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                    <img
                                        src={chartUrl}
                                        alt={`PI Chart for ${currentUser?.name}`}
                                        className="max-w-full max-h-[600px] object-contain"
                                        onError={(e) => {
                                            console.error('Failed to load PI chart image:', chartUrl);
                                            const target = e.target as HTMLImageElement;
                                            target.style.display = 'none';
                                            const parent = target.parentElement;
                                            if (parent) {
                                                parent.innerHTML = `
                                                    <div class="flex items-center justify-center h-64 bg-gray-50 text-gray-500">
                                                        <div class="text-center">
                                                            <p class="text-lg">Failed to load chart image</p>
                                                            <p class="text-sm mt-1">Image may not be accessible</p>
                                                        </div>
                                                    </div>
                                                `;
                                            }
                                        }}
                                        onLoad={() => {
                                            console.log('✅ PI chart image loaded successfully:', chartUrl);
                                        }}
                                    />
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-12">
                                <div className="flex flex-col items-center space-y-4">
                                    <div className="p-6 bg-gray-50 rounded-full">
                                        <FileImage className="h-12 w-12 text-gray-300" />
                                    </div>
                                    <div className="space-y-2">
                                        <h4 className="font-medium text-lg text-gray-700">No PI Chart Available</h4>
                                        <p className="text-sm text-gray-500">
                                            {currentUser?.name} doesn't have a PI chart image uploaded yet.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )}
                </DialogContent>
            </DialogPortal>
        </Dialog>
    );
}