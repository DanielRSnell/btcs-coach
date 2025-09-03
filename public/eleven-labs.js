// ElevenLabs ConvAI Shadow Root Style Injector
(function() {
    'use strict';
    
    console.log('🎙️ ElevenLabs style injector initializing...');
    
    const customStyles = `
        <style id="custom-elevenlabs-styles">
            /* Custom ElevenLabs widget styles */
            .hide-footer {
                display: none !important;
            }
            .widget-wrapper {
                height: 100% !important;
                width: 100% !important;
            }
        </style>
    `;
    
    let observer;
    let shadowObservers = new Map(); // Track shadow DOM observers for each shadow root
    let processedShadowRoots = new Set(); // Track which shadow roots we've already processed
    
    function waitForShadowDOMContent(shadowRoot, callback, maxAttempts = 10, attempt = 1) {
        console.log(`🔍 Checking shadow DOM content (attempt ${attempt}/${maxAttempts})...`);
        
        // Check if shadow DOM has meaningful content
        const hasElements = shadowRoot.children.length > 0;
        const hasTargetElements = shadowRoot.querySelectorAll('.shadow-lg, p.whitespace-nowrap').length > 0;
        
        if (hasElements && hasTargetElements) {
            console.log('✅ Shadow DOM content fully loaded!');
            callback(shadowRoot);
        } else if (attempt < maxAttempts) {
            console.log(`⏳ Shadow DOM content not ready yet, waiting... (${shadowRoot.children.length} children found)`);
            setTimeout(() => {
                waitForShadowDOMContent(shadowRoot, callback, maxAttempts, attempt + 1);
            }, 200); // Wait 200ms between attempts
        } else {
            console.log('⚠️ Max attempts reached, proceeding with current shadow DOM state');
            callback(shadowRoot);
        }
    }

    function processWidgetElements(shadowRoot) {
        if (!shadowRoot) return;
        
        // Create a unique identifier for this shadow root
        const shadowRootId = shadowRoot.host ? shadowRoot.host.getAttribute('data-processed-id') || 
            Date.now() + '_' + Math.random().toString(36).substr(2, 9) : 'unknown';
        
        // Check if we've already processed this specific shadow root
        if (processedShadowRoots.has(shadowRootId)) {
            console.log('📝 Shadow root already processed, skipping...');
            return;
        }
        
        console.log(`✨ Shadow root found! Waiting for content to load... (ID: ${shadowRootId})`);
        
        // Mark the host element so we can track this shadow root
        if (shadowRoot.host) {
            shadowRoot.host.setAttribute('data-processed-id', shadowRootId);
        }
        
        // Wait for shadow DOM content to fully load before processing
        waitForShadowDOMContent(shadowRoot, (loadedShadowRoot) => {
            console.log(`🎯 Processing shadow DOM elements... (ID: ${shadowRootId})`);
            processShadowDOMElements(loadedShadowRoot, shadowRootId);
        });
    }
    
    function processShadowDOMElements(shadowRoot, shadowRootId) {
        // Find all elements with .shadow-lg class
        const elementsWithShadow = shadowRoot.querySelectorAll('.shadow-lg');
        console.log(`🔍 Found ${elementsWithShadow.length} elements with .shadow-lg`);
        
        elementsWithShadow.forEach((element, index) => {
            console.log(`🎯 Processing element ${index + 1}: Adding .widget-wrapper, removing .shadow-lg`);
            
            // Add .widget-wrapper class
            element.classList.add('widget-wrapper');
            
            // Remove .shadow-lg class
            element.classList.remove('shadow-lg');
            
            // Remove height and width constraint classes from .widget-wrapper elements
            element.classList.remove('h-[calc(100%-80px)]', 'max-h-[550px]', 'max-w-[400px]');
            console.log(`🎨 Removed dimension classes from element ${index + 1}`);
        });
        
        // Find p elements with .whitespace-nowrap class that contain span and <a href children
        const footerElements = shadowRoot.querySelectorAll('p.whitespace-nowrap');
        console.log(`🔍 Found ${footerElements.length} p elements with .whitespace-nowrap`);
        
        footerElements.forEach((pElement, index) => {
            // Check if p element has both span and a[href] children
            const hasSpan = pElement.querySelector('span');
            const hasAnchor = pElement.querySelector('a[href]');
            
            if (hasSpan && hasAnchor) {
                console.log(`🎯 Processing p.whitespace-nowrap element ${index + 1}: Found span and <a href>, adding .hide-footer`);
                
                // Add .hide-footer class to the p element
                pElement.classList.add('hide-footer');
            } else {
                console.log(`⚠️ P element ${index + 1}: Missing span (${!!hasSpan}) or a[href] (${!!hasAnchor})`);
            }
        });
        
        // Inject the style element into shadow root (as a marker that we've processed this)
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = customStyles;
        const styleElement = tempDiv.firstElementChild;
        shadowRoot.appendChild(styleElement);
        
        // Mark this shadow root as processed
        processedShadowRoots.add(shadowRootId);
        console.log(`✅ Widget processing complete - added .widget-wrapper, removed .shadow-lg (ID: ${shadowRootId})`);
        
        // Fade in the elevenlabs-convai element after processing
        const elevenLabsElement = document.querySelector('elevenlabs-convai');
        if (elevenLabsElement) {
            console.log('🎭 Fading in ElevenLabs widget...');
            elevenLabsElement.style.transition = 'opacity 0.5s ease-in-out';
            elevenLabsElement.style.opacity = '1';
        }
        
        // Continue observing for new shadow roots (don't disconnect observer)
        
        // Set up continuous monitoring of this shadow root
        setupShadowDOMMonitoring(shadowRoot, shadowRootId);
    }
    
    function setupShadowDOMMonitoring(shadowRoot, shadowRootId) {
        if (shadowObservers.has(shadowRootId)) {
            console.log(`👁️ Shadow DOM monitoring already active for ${shadowRootId}`);
            return;
        }
        
        console.log(`👁️ Setting up continuous shadow DOM monitoring for ${shadowRootId}`);
        
        const shadowObserver = new MutationObserver((mutations) => {
            let needsReprocessing = false;
            
            mutations.forEach((mutation) => {
                // Check for added nodes that might need processing
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if new elements have classes we need to process
                        if (node.classList?.contains('shadow-lg') || 
                            node.querySelector?.('.shadow-lg') ||
                            (node.tagName === 'P' && node.classList?.contains('whitespace-nowrap')) ||
                            node.querySelector?.('p.whitespace-nowrap')) {
                            console.log(`🔄 New processable elements detected in shadow DOM ${shadowRootId}`);
                            needsReprocessing = true;
                        }
                    }
                });
                
                // Check for attribute changes on existing elements
                if (mutation.type === 'attributes' && mutation.target.nodeType === Node.ELEMENT_NODE) {
                    const target = mutation.target;
                    
                    // If an element gained .shadow-lg class or lost .widget-wrapper
                    if ((target.classList.contains('shadow-lg') && !target.classList.contains('widget-wrapper')) ||
                        (target.tagName === 'P' && target.classList.contains('whitespace-nowrap') && !target.classList.contains('hide-footer'))) {
                        console.log(`🔄 Element class changes detected in shadow DOM ${shadowRootId}`);
                        needsReprocessing = true;
                    }
                }
            });
            
            if (needsReprocessing) {
                console.log(`🔧 Reprocessing shadow DOM elements for ${shadowRootId}`);
                // Small delay to allow DOM to settle
                setTimeout(() => {
                    reprocessShadowDOMElements(shadowRoot, shadowRootId);
                }, 100);
            }
        });
        
        // Start observing the shadow root
        shadowObserver.observe(shadowRoot, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
        
        // Store the observer reference
        shadowObservers.set(shadowRootId, shadowObserver);
        console.log(`✅ Shadow DOM monitoring active for ${shadowRootId}`);
    }
    
    function reprocessShadowDOMElements(shadowRoot, shadowRootId) {
        console.log(`🔄 Reprocessing elements in shadow DOM ${shadowRootId}`);
        
        // Process .shadow-lg elements that don't have .widget-wrapper
        const newShadowElements = shadowRoot.querySelectorAll('.shadow-lg:not(.widget-wrapper)');
        if (newShadowElements.length > 0) {
            console.log(`🎯 Found ${newShadowElements.length} new .shadow-lg elements to process`);
            
            newShadowElements.forEach((element, index) => {
                console.log(`🎯 Reprocessing .shadow-lg element ${index + 1}`);
                
                // Add .widget-wrapper class
                element.classList.add('widget-wrapper');
                
                // Remove .shadow-lg class
                element.classList.remove('shadow-lg');
                
                // Remove dimension constraint classes
                element.classList.remove('h-[calc(100%-80px)]', 'max-h-[550px]', 'max-w-[400px]');
            });
        }
        
        // Process footer elements that don't have .hide-footer
        const newFooterElements = shadowRoot.querySelectorAll('p.whitespace-nowrap:not(.hide-footer)');
        if (newFooterElements.length > 0) {
            console.log(`🎯 Found ${newFooterElements.length} new footer elements to hide`);
            
            newFooterElements.forEach((pElement, index) => {
                const hasSpan = pElement.querySelector('span');
                const hasAnchor = pElement.querySelector('a[href]');
                
                if (hasSpan && hasAnchor) {
                    console.log(`🎯 Reprocessing footer element ${index + 1}`);
                    pElement.classList.add('hide-footer');
                }
            });
        }
        
        // Check if widget needs to be faded in (in case it was reset)
        const elevenLabsElement = document.querySelector('elevenlabs-convai');
        if (elevenLabsElement && elevenLabsElement.style.opacity !== '1') {
            console.log('🎭 Re-fading in ElevenLabs widget...');
            elevenLabsElement.style.transition = 'opacity 0.5s ease-in-out';
            elevenLabsElement.style.opacity = '1';
        }
    }
    
    function checkForShadowRoot() {
        const el = document.querySelector('elevenlabs-convai');
        
        if (el && el.shadowRoot) {
            console.log('🎯 ElevenLabs element with shadow root detected');
            processWidgetElements(el.shadowRoot);
            return true;
        }
        
        return false;
    }
    
    function startObserver() {
        // Initial check
        if (checkForShadowRoot()) {
            return;
        }
        
        console.log('👁️ Starting MutationObserver to watch for ElevenLabs shadow root...');
        
        observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // Check for added nodes
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node is elevenlabs-convai or contains it
                        if (node.tagName === 'ELEVENLABS-CONVAI' || 
                            node.querySelector && node.querySelector('elevenlabs-convai')) {
                            console.log('🔍 ElevenLabs element detected in DOM, checking for shadow root...');
                            
                            // Small delay to allow shadow root to be created
                            setTimeout(() => {
                                checkForShadowRoot();
                            }, 100);
                        }
                    }
                });
                
                // Also check for attribute changes on existing elevenlabs-convai elements
                if (mutation.target.tagName === 'ELEVENLABS-CONVAI' && mutation.target.shadowRoot) {
                    console.log('🔄 ElevenLabs element attribute changed, checking shadow root...');
                    checkForShadowRoot();
                }
            });
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['data-shown', 'class', 'style']
        });
    }
    
    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startObserver);
    } else {
        startObserver();
    }
    
    // Fallback: Also try when window loads (in case shadow root is created later)
    window.addEventListener('load', () => {
        console.log('🔄 Window loaded, checking for shadow root...');
        setTimeout(() => {
            checkForShadowRoot();
        }, 500);
    });
    
})();