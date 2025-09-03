import { useState, useEffect, useRef } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
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
                launch?: (payload: any) => void;
                destroy?: () => void;
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
    project_id?: string; // The project ID (localStorage key without prefix)
    value?: any; // The full localStorage data for this session
    created_at: string;
    updated_at: string;
    source?: string;
}

interface SessionsProps {
    user: User;
    sessions: Record<string, Session>;
    currentSessionId?: string;
    currentSession?: Session;
}

export default function Sessions({ user, sessions, currentSessionId, currentSession }: SessionsProps) {
    // DEBUG: Log all props received from server
    console.log('🔍 Sessions component loaded with data:');
    console.log('👤 User received:', user?.name, user?.id);
    console.log('📊 Sessions received:', sessions);
    console.log('📊 Sessions type:', typeof sessions);
    console.log('📊 Sessions keys:', sessions ? Object.keys(sessions) : 'sessions is null/undefined');
    console.log('📊 Sessions count:', sessions ? Object.keys(sessions).length : 0);
    console.log('🎯 Current Session ID received:', currentSessionId);
    console.log('🎯 Current Session data received:', currentSession);
    
    const { props } = usePage();
    const [selectedSessionId, setSelectedSessionId] = useState<string | null>(null);
    const [localStorageListenerSetup, setLocalStorageListenerSetup] = useState(false);
    const [currentActiveSessionId, setCurrentActiveSessionId] = useState<string | null>(null);
    const [voiceflowInitialized, setVoiceflowInitialized] = useState(false);

    // Initialize Voiceflow script on component mount
    useEffect(() => {
        // Add Voiceflow script if not already present
        if (!document.querySelector('script[src*="widget-next/bundle.mjs"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdn.voiceflow.com/widget-next/bundle.mjs';
            script.async = true;
            script.type = 'text/javascript';
            document.head.appendChild(script);
        }
    }, []);

    // Initialize or reinitialize Voiceflow chat
    const initializeVoiceflow = () => {
        console.log('🔄 INITIALIZING VOICEFLOW: Starting initialization...');
        
        // Add a small delay to ensure DOM is ready
        setTimeout(() => {
            console.log('🎯 DOM READY: Starting Voiceflow initialization...');
            const chatElement = document.getElementById('main-voiceflow-chat');
            console.log('📍 Chat element found:', chatElement);
            
            // Check if Voiceflow is already loaded
            if (window.voiceflow && window.voiceflow.chat) {
                console.log('✅ VOICEFLOW AVAILABLE: Using existing Voiceflow instance');
                loadVoiceflowChat();
            } else {
                console.log('🚀 LOADING VOICEFLOW: Loading Voiceflow script...');
                // Use the provided Voiceflow embed script
                (function(d: Document, t: string) {
                    const v = d.createElement(t) as HTMLScriptElement;
                    const s = d.getElementsByTagName(t)[0];
                    v.onload = function() {
                        console.log('📦 SCRIPT LOADED: Voiceflow script loaded, window.voiceflow:', window.voiceflow);
                        loadVoiceflowChat();
                    };
                    v.src = 'https://cdn.voiceflow.com/widget-next/bundle.mjs';
                    v.type = 'text/javascript';
                    s.parentNode?.insertBefore(v, s);
                })(document, 'script');
            }
        }, 100);
    };

    // Load Voiceflow chat configuration
    const loadVoiceflowChat = () => {
        console.log('⚡ LOADING CHAT: Configuring Voiceflow chat...');
        
        if (window.voiceflow && window.voiceflow.chat) {
            const targetElement = document.getElementById('main-voiceflow-chat');
            console.log('🎯 Target element for Voiceflow:', targetElement);
            
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
                assistant: {
                    extensions: [window.AdaptiveCardExtension],
                    stylesheet: '/voiceflow.css?v=' + new Date().toISOString().replace(/[:.]/g, '-')
                },
                voice: {
                    url: "https://runtime-api.voiceflow.com"
                },
                render: {
                    mode: 'embedded',
                    target: targetElement
                },
                autostart: true,
                launch: {
                    event: {
                        type: 'launch',
                        payload: payload
                    }
                },
            });
            
            console.log('✅ CHAT LOADED: Voiceflow chat loaded successfully');
            setVoiceflowInitialized(true);
        } else {
            console.error('❌ VOICEFLOW MISSING: Voiceflow widget failed to load - missing window.voiceflow');
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

    // Initialize Voiceflow when component mounts
    useEffect(() => {
        console.log('🔄 COMPONENT MOUNT: Initializing Voiceflow...');
        console.log('👤 User:', user?.name, user?.id);
        console.log('📍 Current Session ID:', currentSessionId);
        initializeVoiceflow();
    }, [user]);

    // Note: Since we're using full page reloads for session switching (window.location.href),
    // we don't need complex reinitialization logic here. Each session switch will be a fresh page load.

    // API helper functions
    const apiCall = async (endpoint: string, data: any) => {
        try {
            console.log(`📡 Making API call to ${endpoint} with data:`, data);
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(data),
            });
            
            console.log(`📡 Response status: ${response.status} ${response.statusText}`);
            console.log(`📡 Response headers:`, Object.fromEntries(response.headers.entries()));
            
            // Get response text first to see what we're dealing with
            const responseText = await response.text();
            console.log(`📡 Raw response:`, responseText.substring(0, 200) + (responseText.length > 200 ? '...' : ''));
            
            // Try to parse as JSON
            let result;
            try {
                result = JSON.parse(responseText);
                console.log(`📡 Parsed JSON result:`, result);
            } catch (parseError) {
                console.error(`❌ Failed to parse response as JSON:`, parseError);
                console.log(`❌ Response was:`, responseText);
                return { error: 'Invalid JSON response', raw_response: responseText };
            }
            
            // Handle authentication errors
            if (result.error === 'Unauthenticated') {
                console.warn(`🔒 Authentication required for ${endpoint}. User may need to log in again.`);
                return result;
            }
            
            return result;
        } catch (error) {
            console.error(`❌ API call to ${endpoint} failed:`, error);
            return { error: 'Network error', details: error };
        }
    };

    // Function to identify current active session from localStorage
    const identifyCurrentActiveSession = () => {
        try {
            // Get all voiceflow localStorage keys
            const voiceflowKeys = Object.keys(localStorage).filter(key => key.startsWith('voiceflow-session-'));
            
            if (voiceflowKeys.length === 0) {
                setCurrentActiveSessionId(null);
                return;
            }

            // For each localStorage session, get the userID and check if it matches our database sessions
            for (const key of voiceflowKeys) {
                const sessionValue = localStorage.getItem(key);
                if (sessionValue) {
                    try {
                        const sessionData = JSON.parse(sessionValue);
                        const userID = sessionData.userID;
                        
                        // Check if this userID matches any of our database sessions
                        if (userID && sessions[userID]) {
                            setCurrentActiveSessionId(userID);
                            console.log(`🎯 Current active session identified: ${userID} from localStorage key: ${key}`);
                            return; // Found the active session, exit
                        }
                    } catch (parseError) {
                        console.warn(`⚠️ Could not parse localStorage session ${key}:`, parseError);
                        continue;
                    }
                }
            }
            
            // If no match found, clear the current active session
            setCurrentActiveSessionId(null);
        } catch (error) {
            console.error('❌ Error identifying current active session:', error);
            setCurrentActiveSessionId(null);
        }
    };

    // Process individual localStorage session
    // Key concept: We monitor localStorage for voiceflow-session-* keys
    // The VALUE contains a userID field - this userID is the actual session identifier
    // If userID changes, it's a new chat session that needs to be registered
    // If userID stays same but value changes, we update the existing session
    const processLocalStorageSession = async (key: string, sessionValue: string) => {
        // Extract project ID from localStorage key (remove 'voiceflow-session-' prefix)
        const projectId = key.replace('voiceflow-session-', '');
        
        try {
            console.log(`🔍 Monitoring localStorage key: ${key}`);
            console.log(`📦 Raw localStorage value:`, sessionValue.substring(0, 200) + '...');
            
            // Parse the localStorage value to extract the session data
            let sessionData;
            try {
                sessionData = JSON.parse(sessionValue);
            } catch (parseError) {
                console.error(`❌ Failed to parse localStorage JSON for ${key}:`, parseError);
                return;
            }
            
            // Extract the userID - this is THE session identifier we care about
            const sessionUserID = sessionData.userID;
            
            console.log(`🎯 Session Analysis:`, {
                localStorage_key: key,
                project_id: projectId,
                session_userID: sessionUserID, // This is the chat session ID
                status: sessionData.status,
                turns_count: sessionData.turns?.length || 0
            });
            
            if (!sessionUserID) {
                console.warn(`⚠️ No userID found in localStorage value. Available keys:`, Object.keys(sessionData));
                console.warn(`⚠️ Cannot process session without userID - skipping`);
                return;
            }
            
            console.log(`🔍 🔍 CHECKING: Does session with userID "${sessionUserID}" exist in database?`);
            
            // Check if we already have a session record for this userID
            const checkResult = await apiCall('/api/sessions/check', { 
                project_id: projectId,
                voiceflow_user_id: sessionUserID 
            });
            
            // Handle API errors
            if (checkResult && checkResult.error) {
                if (checkResult.error === 'Unauthenticated') {
                    console.warn(`🔒 ❌ CANNOT CHECK: User not authenticated - skipping session "${sessionUserID}"`);
                } else if (checkResult.error === 'Invalid JSON response') {
                    console.error(`🚨 ❌ API ERROR: Returning HTML instead of JSON - check routes/middleware`);
                } else {
                    console.error(`❌ API ERROR: Failed to check session "${sessionUserID}":`, checkResult.error);
                }
                return;
            }
            
            // ======================================
            // DECISION POINT: EXISTS OR NEW SESSION
            // ======================================
            
            if (checkResult && checkResult.exists) {
                // 🔄 SESSION EXISTS → UPDATE with current localStorage data
                console.log(`✅ ✅ SESSION EXISTS: Found userID "${sessionUserID}" in database`);
                console.log(`🔄 🔄 UPDATING: Syncing current localStorage value to existing session...`);
                
                const updateResult = await apiCall('/api/sessions/update', {
                    project_id: projectId,
                    session_data: {
                        last_turn: sessionData, // Update database with current localStorage value
                        source: 'localStorage_sync'
                    }
                });
                
                if (updateResult?.success) {
                    console.log(`✅ ✅ UPDATE SUCCESS: Session "${sessionUserID}" updated with latest localStorage data`);
                    console.log(`📊 Updated session now has ${sessionData.turns?.length || 0} messages`);
                } else if (updateResult?.error) {
                    console.error(`❌ ❌ UPDATE FAILED: Could not update session "${sessionUserID}":`, updateResult.error);
                }
                
            } else {
                // 🆕 NEW SESSION → REGISTER as new session  
                console.log(`🆕 🆕 NEW SESSION DETECTED: userID "${sessionUserID}" NOT found in database`);
                console.log(`📝 📝 REGISTERING: Creating new session record...`);
                
                const registrationData = {
                    project_id: projectId,
                    session_data: {
                        last_turn: sessionData, // Store the full localStorage value
                        source: 'localStorage_sync',
                        detected_at: new Date().toISOString()
                    }
                };
                
                console.log(`📋 Registration details:`, {
                    project_id: projectId,
                    userID: sessionUserID,
                    status: sessionData.status,
                    message_count: sessionData.turns?.length || 0
                });
                
                const registerResult = await apiCall('/api/sessions/register', registrationData);
                
                if (registerResult?.success) {
                    console.log(`✅ ✅ REGISTRATION SUCCESS: New session "${sessionUserID}" created in database`);
                    console.log(`🎉 🎉 NEW SESSION ADDED: Refreshing sidebar to show the new session...`);
                    
                    // Refresh the page to show new session in sidebar
                    router.reload({ only: ['sessions'] });
                } else if (registerResult?.error) {
                    console.error(`❌ ❌ REGISTRATION FAILED: Could not create session "${sessionUserID}":`, registerResult.error);
                }
            }
            
            console.log(`🏁 🏁 SYNC COMPLETE: Finished processing session "${sessionUserID}"`);
            console.log(`─────────────────────────────────────────────────────────────────`);
            
        } catch (error) {
            console.error(`❌ Error processing localStorage session:`, error);
        }
    };

    // MAIN LOGIC: Monitor localStorage for Voiceflow sessions
    // 
    // Flow:
    // 1. Scan localStorage for keys matching "voiceflow-session-*" 
    // 2. For each key, parse the JSON value to extract userID
    // 3. userID is the actual chat session identifier (e.g. "cmeu78kdg00003b6j3c91yb1d")
    // 4. Check if database has a session record with this userID
    // 5. If exists → Update the record with current localStorage data
    // 6. If not exists → Register as new session
    // 7. Continuously monitor for localStorage changes and repeat process
    //
    // Example:
    // Key: "voiceflow-session-686331bc96acfa1dd62f6fd5" 
    // Value: {"userID": "cmeu78kdg00003b6j3c91yb1d", "turns": [...], "status": "ACTIVE"}
    // Action: Check if userID "cmeu78kdg00003b6j3c91yb1d" exists → Update or Register
    
    useEffect(() => {
        const checkLocalStorageSessions = async () => {
            console.log('🔍 🚀 🚀 STARTING INITIAL LOCALSTORAGE SCAN...');
            console.log('🔍 Scanning for keys matching pattern: voiceflow-session-*');
            
            // Get all localStorage keys that match voiceflow-session-* pattern
            const voiceflowSessionKeys = Object.keys(localStorage).filter(key => 
                key.startsWith('voiceflow-session-')
            );
            
            console.log(`📦 📦 FOUND ${voiceflowSessionKeys.length} VOICEFLOW SESSION KEYS:`);
            voiceflowSessionKeys.forEach((key, index) => {
                console.log(`  ${index + 1}. ${key}`);
            });
            
            if (voiceflowSessionKeys.length === 0) {
                console.log('📭 No Voiceflow sessions found in localStorage - monitoring for new ones...');
            } else {
                console.log('🔄 🔄 PROCESSING EACH SESSION: Checking database and syncing...');
            }

            // Process each localStorage session
            for (let i = 0; i < voiceflowSessionKeys.length; i++) {
                const key = voiceflowSessionKeys[i];
                const sessionValue = localStorage.getItem(key);
                
                console.log(`\n🔄 [${i + 1}/${voiceflowSessionKeys.length}] PROCESSING: ${key}`);
                
                if (sessionValue) {
                    await processLocalStorageSession(key, sessionValue);
                } else {
                    console.warn(`⚠️ ⚠️ WARNING: Key ${key} has no value - skipping`);
                }
            }
            
            console.log(`\n✅ ✅ INITIAL SCAN COMPLETE: Processed ${voiceflowSessionKeys.length} sessions`);
            console.log('🎧 🎧 MONITORING ACTIVE: Watching for localStorage changes...');
            console.log('═══════════════════════════════════════════════════════════════════');
        };

        checkLocalStorageSessions();
    }, []);

    // Identify current active session when sessions change or component mounts
    useEffect(() => {
        if (sessions && Object.keys(sessions).length > 0) {
            identifyCurrentActiveSession();
        }
    }, [sessions]);

    // CONTINUOUS MONITORING: Set up localStorage change listeners
    // This monitors for real-time changes to voiceflow-session-* keys
    // When Voiceflow updates localStorage (new messages, status changes, etc.)
    // we automatically sync the changes to the database
    useEffect(() => {
        if (localStorageListenerSetup) return;

        console.log('🎧 Setting up CONTINUOUS localStorage monitoring...');

        const handleStorageChange = async (event: StorageEvent) => {
            // Only handle voiceflow-session-* keys
            if (!event.key?.startsWith('voiceflow-session-')) return;
            
            console.log(`🔄 🔄 LOCALSTORAGE CHANGE DETECTED!`);
            console.log(`📍 Key: ${event.key}`);
            console.log(`📦 New value length: ${event.newValue?.length || 0} characters`);
            console.log(`🚀 🚀 TRIGGERING SYNC: Processing localStorage change...`);
            
            // If the value was removed, we might want to handle deletion later
            // For now, only process when there's a new value
            if (event.newValue) {
                console.log(`📋 PROCESSING: localStorage change for ${event.key}`);
                // Use the same processing logic for consistency
                await processLocalStorageSession(event.key, event.newValue);
                // Re-identify current active session after processing
                identifyCurrentActiveSession();
            } else {
                console.log(`🗑️ VALUE REMOVED: localStorage key ${event.key} was deleted (not processing)`);
                // Re-identify current active session after a deletion
                identifyCurrentActiveSession();
            }
        };

        // Listen for storage events (changes from other tabs/windows)
        window.addEventListener('storage', handleStorageChange);
        
        // Custom event listener for changes within the same tab
        const originalSetItem = localStorage.setItem;
        localStorage.setItem = function(key: string, value: string) {
            const event = new CustomEvent('localStorageChange', {
                detail: { key, newValue: value, oldValue: localStorage.getItem(key) }
            });
            
            originalSetItem.apply(this, [key, value]);
            window.dispatchEvent(event);
        };

        const handleCustomStorageChange = (event: CustomEvent) => {
            const { key, newValue } = event.detail;
            if (key?.startsWith('voiceflow-session-')) {
                // Process the change using our unified logic
                if (newValue) {
                    processLocalStorageSession(key, newValue);
                    // Re-identify current active session after processing
                    identifyCurrentActiveSession();
                } else {
                    // Re-identify current active session after a deletion
                    identifyCurrentActiveSession();
                }
            }
        };

        window.addEventListener('localStorageChange', handleCustomStorageChange as EventListener);
        
        setLocalStorageListenerSetup(true);
        console.log('✅ localStorage listeners set up successfully');

        // Cleanup function
        return () => {
            window.removeEventListener('storage', handleStorageChange);
            window.removeEventListener('localStorageChange', handleCustomStorageChange as EventListener);
            
            // Restore original localStorage.setItem
            localStorage.setItem = originalSetItem;
        };
    }, [localStorageListenerSetup]);

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
        return session.value?.status || 'ACTIVE';
    };

    // Handle session switching: clear localStorage, create new session entry, navigate to session URL
    const handleSessionSwitch = (sessionId: string, session: Session) => {
        console.log(`🔄 SWITCHING TO SESSION: ${sessionId}`);
        console.log(`📊 Session data:`, session);

        try {
            // Step 1: Clear all existing voiceflow localStorage entries
            console.log(`🧹 CLEARING: Removing all voiceflow-session-* from localStorage`);
            const voiceflowKeys = Object.keys(localStorage).filter(key => key.startsWith('voiceflow-session-'));
            voiceflowKeys.forEach(key => {
                console.log(`🗑️ Removing: ${key}`);
                localStorage.removeItem(key);
            });

            // Step 2: Create new localStorage entry with selected session data
            const projectId = session.project_id || '686331bc96acfa1dd62f6fd5'; // Default project ID or from session
            const localStorageKey = `voiceflow-session-${projectId}`;
            const sessionData = session.value || {};
            
            console.log(`📝 CREATING: New localStorage entry ${localStorageKey}`);
            console.log(`📦 Session value data:`, sessionData);
            
            localStorage.setItem(localStorageKey, JSON.stringify(sessionData));
            
            // Step 3: Update the selected session state
            setSelectedSessionId(sessionId);
            
            // Step 4: Navigate to session-specific URL with full page reload
            const sessionUrl = `/sessions/${sessionId}`;
            console.log(`🧭 NAVIGATING: To ${sessionUrl} (full page reload)`);
            
            // Use browser navigation for full page reload instead of Inertia
            window.location.href = sessionUrl;
            
        } catch (error) {
            console.error('❌ Error switching sessions:', error);
        }
    };

    return (
        <AppLayout>
            <Head title="Sessions" />
            
            <div className="flex h-[calc(100vh-8rem)] gap-6 pt-6">
                {/* Sessions Sidebar */}
                <div className="w-80 flex-shrink-0">
                    <Card className="h-full flex flex-col gap-1 py-0 pt-6">
                        <CardHeader className="flex-shrink-0">
                            <CardTitle className="flex items-center gap-2">
                                <MessageCircle className="h-5 w-5" />
                                Your Sessions
                            </CardTitle>
                            <CardDescription>
                                {Object.keys(sessions).length} active session{Object.keys(sessions).length !== 1 ? 's' : ''}
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="p-0 flex-1 flex flex-col min-h-0">
                            <div className="flex-1 overflow-y-auto no-scrollbar">
                                {Object.keys(sessions).length === 0 ? (
                                    <div className="h-full flex flex-col items-center justify-center p-6 text-center">
                                        <div className="flex flex-col items-center space-y-4">
                                            <div className="p-6 bg-gray-50 rounded-full">
                                                <MessageCircle className="h-10 w-10 text-gray-300" />
                                            </div>
                                            <div className="space-y-2">
                                                <h3 className="text-lg font-semibold text-gray-700">No active sessions yet</h3>
                                                <p className="text-sm text-gray-500 max-w-sm">
                                                    Start a conversation in the chat area to begin your coaching journey and create your first session.
                                                </p>
                                            </div>
                                            <div className="flex items-center space-x-2 text-xs text-gray-400 mt-6">
                                                <div className="flex items-center space-x-1">
                                                    <div className="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                                    <span>Ready to chat</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="p-4 space-y-3">
                                        {Object.entries(sessions).map(([sessionId, session]) => (
                                            <div
                                                key={sessionId}
                                                className={`p-3 rounded-lg border cursor-pointer transition-colors ${
                                                    currentActiveSessionId === sessionId
                                                        ? 'bg-green-50 border-green-300 shadow-sm ring-2 ring-green-200'
                                                        : selectedSessionId === sessionId
                                                        ? 'bg-blue-50 border-blue-200'
                                                        : 'bg-white hover:bg-gray-50'
                                                }`}
                                                onClick={() => handleSessionSwitch(sessionId, session)}
                                            >
                                                <div className="flex items-start justify-between mb-2">
                                                    <div className="font-medium text-sm truncate flex-1">
                                                        Session {sessionId.substring(sessionId.length - 8)}
                                                        {currentActiveSessionId === sessionId && (
                                                            <span className="ml-2 text-xs bg-green-500 text-white px-2 py-0.5 rounded-full">
                                                                Current
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-500">
                                                    <Calendar className="h-3 w-3" />
                                                    <span>{formatSessionDate(session.created_at)}</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                                    <Clock className="h-3 w-3" />
                                                    <span>Updated {formatSessionDate(session.updated_at)}</span>
                                                </div>
                                                <div className="flex items-center gap-2 text-xs text-gray-400 mt-1">
                                                    <MessageCircle className="h-3 w-3" />
                                                    <span>{session.value?.turns?.length || 0} messages</span>
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
                    <Card className="h-full py-0">
                        <CardContent className="p-0 h-full">
                            <div 
                                id="main-voiceflow-chat" 
                                className="h-full w-full rounded-lg overflow-hidden"
                            >
                                <div className="flex items-center justify-center h-full bg-gray-50">
                                    <div className="text-center">
                                        <MessageCircle className="h-8 w-8 mx-auto mb-4 text-gray-300" />
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