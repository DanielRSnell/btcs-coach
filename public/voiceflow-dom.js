/**
 * Voiceflow DOM Observer
 *
 * This script handles modifications to the Voiceflow widget DOM,
 * including the audio button class replacement and click handling.
 * Runs independently of other Voiceflow customizations.
 */

(function() {
    'use strict';

    console.log('🎤 Voiceflow DOM Observer initialized');

    /**
     * Find the audio button inside the Voiceflow shadow DOM
     */
    const findAudioButtonInShadow = () => {
        // First, find the main Voiceflow chat element
        const voiceflowChat = document.querySelector('#main-voiceflow-chat');

        if (!voiceflowChat) {
            console.log('🔍 [STEP 1/3] Checking for #main-voiceflow-chat... NOT FOUND');
            return null;
        }

        console.log('✅ [STEP 1/3] #main-voiceflow-chat element found!');

        // Check if shadow DOM exists
        if (!voiceflowChat.shadowRoot) {
            console.log('🔍 [STEP 2/3] Checking for shadow DOM... NOT ATTACHED YET');
            console.log('   → Element:', voiceflowChat);
            console.log('   → Has shadowRoot property:', 'shadowRoot' in voiceflowChat);
            return null;
        }

        console.log('✅ [STEP 2/3] Shadow DOM attached to #main-voiceflow-chat!');
        console.log('   → Shadow root:', voiceflowChat.shadowRoot);

        // Search for the audio button inside the shadow DOM
        const audioButton = voiceflowChat.shadowRoot.querySelector('.vfrc-chat-input__audio-input');

        if (!audioButton) {
            console.log('🔍 [STEP 3/3] Checking for audio button in shadow DOM... NOT FOUND');
            console.log('   → Searching for selector: .vfrc-chat-input__audio-input');

            // Debug: Show what IS in the shadow DOM
            const allButtons = voiceflowChat.shadowRoot.querySelectorAll('button');
            console.log('   → Buttons found in shadow DOM:', allButtons.length);
            allButtons.forEach((btn, i) => {
                console.log(`   → Button ${i + 1}:`, btn.className, btn.title || btn.getAttribute('aria-label'));
            });

            return null;
        }

        console.log('✅ [STEP 3/3] Audio button found in shadow DOM!');
        console.log('   → Button element:', audioButton);
        console.log('   → Button classes:', audioButton.className);
        return audioButton;
    };

    /**
     * Find and modify the audio button class
     */
    const modifyAudioButtonClass = () => {
        const audioButton = findAudioButtonInShadow();

        if (audioButton && !audioButton.classList.contains('audio-switcher')) {
            console.log('🎤 Replacing button classes with audio-switcher');

            // Remove the original Voiceflow class
            audioButton.classList.remove('vfrc-chat-input__audio-input');

            // Add our custom class
            audioButton.classList.add('audio-switcher');

            console.log('✅ Button classes updated:', audioButton.className);
            return true; // Found and modified
        }

        if (audioButton && audioButton.classList.contains('audio-switcher')) {
            // Already modified, this is success
            return true;
        }

        return false; // Not found or already modified
    };

    /**
     * Handle audio button clicks
     */
    const handleAudioButtonClick = (e) => {
        const target = e.target;

        // For shadow DOM, we need to check both the regular DOM and composed path
        let audioButton = null;

        // First check if we can find it with closest (works in shadow DOM)
        if (target.closest) {
            audioButton = target.closest('.audio-switcher');
        }

        // If not found, check the composed path (for shadow DOM events)
        if (!audioButton && e.composedPath) {
            const path = e.composedPath();
            for (const el of path) {
                if (el.classList && el.classList.contains('audio-switcher')) {
                    audioButton = el;
                    break;
                }
            }
        }

        if (audioButton) {
            console.log('🎤 Audio switcher button clicked, preventing default and navigating...');
            e.preventDefault();
            e.stopPropagation();

            // Get current URL and add/update mode parameter
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('mode', 'audio');

            console.log('🎤 Navigating to:', currentUrl.toString());
            window.location.href = currentUrl.toString();
        }
    };

    /**
     * Wait for Voiceflow widget to load and shadow DOM to be ready
     */
    const waitForVoiceflowWidget = () => {
        console.log('🔍 Starting Voiceflow widget polling...');
        console.log('   → Will check every 100ms for up to 10 seconds');

        let checkCount = 0;
        const maxChecks = 100; // Max 10 seconds (100 * 100ms)
        const logEveryNChecks = 10; // Log every 1 second

        // Poll for the shadow DOM and button
        const pollInterval = setInterval(() => {
            checkCount++;

            // Log progress every second
            if (checkCount % logEveryNChecks === 0) {
                console.log(`⏳ Polling attempt ${checkCount}/${maxChecks} (${checkCount / 10}s elapsed)...`);
            }

            if (modifyAudioButtonClass()) {
                console.log('✅ Audio button found and modified after', checkCount, 'attempts!');
                console.log('   → Total time:', (checkCount * 100) + 'ms');
                clearInterval(pollInterval);
                setupShadowDOMObserver();
                return;
            }

            if (checkCount >= maxChecks) {
                console.warn('⚠️ Max polling attempts reached (10 seconds)');
                console.warn('   → Switching to MutationObserver for continuous monitoring');
                clearInterval(pollInterval);
                setupShadowDOMObserver();
            }
        }, 100); // Check every 100ms
    };

    /**
     * Set up observer specifically for shadow DOM changes
     */
    const setupShadowDOMObserver = () => {
        const voiceflowChat = document.querySelector('#main-voiceflow-chat');

        if (!voiceflowChat) {
            console.log('🔍 #main-voiceflow-chat not found, setting up document observer');

            // Watch for #main-voiceflow-chat to appear
            const documentObserver = new MutationObserver(() => {
                const chat = document.querySelector('#main-voiceflow-chat');
                if (chat) {
                    console.log('✅ #main-voiceflow-chat appeared!');
                    documentObserver.disconnect();
                    setupShadowDOMObserver(); // Recursive call
                }
            });

            documentObserver.observe(document.body, {
                childList: true,
                subtree: true
            });

            return;
        }

        if (!voiceflowChat.shadowRoot) {
            console.log('🔍 Shadow DOM not ready, polling...');

            // Poll for shadow DOM to be attached
            const shadowPoll = setInterval(() => {
                if (voiceflowChat.shadowRoot) {
                    console.log('✅ Shadow DOM attached!');
                    clearInterval(shadowPoll);
                    setupShadowDOMObserver(); // Recursive call
                }
            }, 100);

            return;
        }

        console.log('👀 Setting up MutationObserver on shadow DOM');

        let isButtonFound = false;
        let debounceTimeout;

        // Observe changes inside the shadow DOM
        const shadowObserver = new MutationObserver(() => {
            // Skip if we already found the button
            if (isButtonFound) return;

            // Debounce to avoid excessive checks
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                if (modifyAudioButtonClass()) {
                    isButtonFound = true;
                    console.log('🎉 Button found via shadow DOM observer - stopping searches');
                }
            }, 50);
        });

        shadowObserver.observe(voiceflowChat.shadowRoot, {
            childList: true,
            subtree: true,
            attributes: false
        });

        // Also do an immediate check in case button is already there
        if (modifyAudioButtonClass()) {
            isButtonFound = true;
            console.log('🎉 Button found immediately after observer setup');
        }

        console.log('✅ Shadow DOM observer active - watching for button');
    };

    /**
     * Set up click event listener for audio button
     */
    const setupClickListener = () => {
        // Use event delegation with capture phase to intercept clicks early
        document.addEventListener('click', handleAudioButtonClick, true);
        console.log('🎯 Audio button click listener registered');
    };

    /**
     * Initialize the observer when DOM is ready
     */
    const init = () => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('📄 DOM loaded, starting Voiceflow observer');
                waitForVoiceflowWidget();
                setupClickListener();
            });
        } else {
            // DOM already loaded
            console.log('📄 DOM already loaded, starting Voiceflow observer immediately');
            waitForVoiceflowWidget();
            setupClickListener();
        }
    };

    // Start initialization
    init();

})();
