/**
 * Voiceflow Session Sync
 * Standalone script to sync voiceflow localStorage sessions to the database
 * No React dependencies - just plain JavaScript
 */

(function() {
    'use strict';

    console.log('🔧 Loading Voiceflow Session Sync...');

    // Cache to track last known turn counts
    const lastKnownTurns = {};

    // API helper function
    async function apiCall(endpoint, data) {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('❌ API call failed:', error);
            return { error: error.message };
        }
    }

    // Process a single localStorage session
    async function processSession(key, sessionValue, forceSync = false) {
        const projectId = key.replace('voiceflow-session-', '');

        try {
            const sessionData = JSON.parse(sessionValue);
            const userID = sessionData.userID;

            if (!userID) {
                console.warn(`⚠️ No userID found in ${key}`);
                return { success: false, message: 'No userID' };
            }

            const currentTurns = sessionData.turns?.length || 0;
            const cachedTurns = lastKnownTurns[userID];

            // Skip if turn count hasn't changed (unless force sync)
            if (!forceSync && cachedTurns === currentTurns) {
                console.log(`⏭️ Skipping session ${userID} - no changes (${currentTurns} turns)`);
                return { success: true, action: 'skipped', userID, turns: currentTurns };
            }

            console.log(`🔄 Syncing session ${userID} (${cachedTurns || 0} → ${currentTurns} turns)`);

            // Check if session exists
            const checkResult = await apiCall('/api/sessions/check', {
                project_id: projectId,
                voiceflow_user_id: userID
            });

            if (checkResult.error) {
                console.error(`❌ Check failed:`, checkResult.error);
                return { success: false, error: checkResult.error };
            }

            // Update or register
            if (checkResult.exists) {
                const updateResult = await apiCall('/api/sessions/update', {
                    project_id: projectId,
                    session_data: sessionData,
                    source: 'localStorage_sync'
                });

                if (updateResult.success) {
                    console.log(`✅ Updated session ${userID}`);
                    // Update cache
                    lastKnownTurns[userID] = currentTurns;
                    return { success: true, action: 'updated', userID, turns: currentTurns };
                } else {
                    console.error(`❌ Update failed:`, updateResult.error);
                    return { success: false, error: updateResult.error };
                }
            } else {
                const registerResult = await apiCall('/api/sessions/register', {
                    project_id: projectId,
                    session_data: sessionData,
                    source: 'localStorage_sync',
                    detected_at: new Date().toISOString()
                });

                if (registerResult.success) {
                    console.log(`✅ Registered new session ${userID}`);
                    // Update cache
                    lastKnownTurns[userID] = currentTurns;
                    return { success: true, action: 'registered', userID, turns: currentTurns };
                } else {
                    console.error(`❌ Registration failed:`, registerResult.error);
                    return { success: false, error: registerResult.error };
                }
            }
        } catch (error) {
            console.error(`❌ Error processing ${key}:`, error);
            return { success: false, error: error.message };
        }
    }

    // Main save function
    window.session = window.session || {};

    window.session.save = async function() {
        console.log('💾 window.session.save() triggered');

        const voiceflowKeys = Object.keys(localStorage).filter(key =>
            key.startsWith('voiceflow-session-')
        );

        if (voiceflowKeys.length === 0) {
            console.warn('⚠️ No voiceflow sessions found');
            return { success: false, message: 'No sessions to save' };
        }

        console.log(`📦 Found ${voiceflowKeys.length} session(s) to sync`);

        const results = [];
        for (const key of voiceflowKeys) {
            const sessionValue = localStorage.getItem(key);
            if (sessionValue) {
                const result = await processSession(key, sessionValue);
                results.push({ key, ...result });
            }
        }

        const successful = results.filter(r => r.success).length;
        console.log(`✅ Sync complete: ${successful}/${results.length} successful`);

        return {
            success: successful > 0,
            total: results.length,
            successful,
            results
        };
    };

    console.log('✅ window.session.save() is ready');

    // Auto-sync every 3 seconds
    setInterval(async () => {
        console.log('⏰ Auto-sync triggered (every 3 seconds)');
        await window.session.save();
    }, 3000);

    console.log('✅ Auto-sync enabled (every 3 seconds)');
})();
