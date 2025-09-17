import { useState, useEffect } from 'react';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetPortal, SheetOverlay } from '@/components/ui/sheet';
import { FileImage } from 'lucide-react';

// Extend window interface
declare global {
    interface Window {
        profile?: {
            drawer?: {
                show: (user?: any) => void;
                hide: () => void;
            };
        };
    }
}

interface PIDrawerProps {
    user?: any;
}

export default function PIDrawer({ user: initialUser }: PIDrawerProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [user, setUser] = useState(initialUser);

    // Set up window functions
    useEffect(() => {
        // Initialize the profile object if it doesn't exist
        if (!window.profile) {
            window.profile = {};
        }

        // Create the drawer object with show/hide functions
        window.profile.drawer = {
            show: (userData?: any) => {
                console.log('📋 PI Drawer show() called', userData);
                const userToShow = userData || initialUser;
                setUser(userToShow);
                setIsOpen(true);
            },
            hide: () => {
                console.log('📋 PI Drawer hide() called');
                setIsOpen(false);
            }
        };

        console.log('✅ window.profile.drawer functions registered');

        // Cleanup function
        return () => {
            if (window.profile?.drawer) {
                delete window.profile.drawer;
                console.log('🧹 Cleaned up window.profile.drawer');
            }
        };
    }, []);

    // Update user when prop changes
    useEffect(() => {
        if (initialUser) {
            setUser(initialUser);
        }
    }, [initialUser]);

    return (
        <Sheet open={isOpen} onOpenChange={setIsOpen}>
            <SheetPortal>
                <SheetOverlay
                    className="backdrop-blur-md bg-black/90"
                    style={{ zIndex: 99999999999999998 }}
                />
                <SheetContent
                    side="left"
                    className="w-96 sm:w-[540px]"
                    style={{ zIndex: 99999999999999999 }}
                >
                <SheetHeader>
                    <div className="flex items-center gap-3">
                        <div className="p-2 bg-blue-100 rounded-lg">
                            <FileImage className="h-5 w-5 text-blue-600" />
                        </div>
                        <div>
                            <SheetTitle className="text-xl">
                                {user?.name ? `${user.name}'s PI Chart` : 'PI Behavioral Chart'}
                            </SheetTitle>
                            <SheetDescription>
                                Predictive Index behavioral assessment visualization
                            </SheetDescription>
                        </div>
                    </div>
                </SheetHeader>

                <div className="mt-6">
                    {/* PI Chart Image Display */}
                    <div className="flex justify-center h-[calc(100vh-200px)] overflow-y-auto">
                        {user?.pi_chart_image ? (
                            <div className="relative bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                <img
                                    src={user.pi_chart_image}
                                    alt={`PI Chart for ${user.name}`}
                                    className="max-w-full max-h-full object-contain"
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
                </div>
                </SheetContent>
            </SheetPortal>
        </Sheet>
    );
}