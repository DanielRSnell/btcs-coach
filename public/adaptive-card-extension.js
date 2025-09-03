// All styles are now defined in voiceflow.css using CSS classes

window.AdaptiveCardExtension = {
    name: 'ADAPTIVE',
    type: 'response',
    match: ({ trace }) => {
        console.log('🎴 Adaptive Card Extension - trace.type:', trace.type);
        return trace.type === 'ADAPTIVE_CARDS' || trace.payload?.name === 'ADAPTIVE_CARDS';
    },
    render: ({ trace, element }) => {
        // Walk up the DOM tree to find .vfrc-chat and .vfrc-system-response
        let currentElement = element;
        let vfrcChat = null;
        let systemResponseElement = null;
        
        while (currentElement && currentElement.parentElement) {
            currentElement = currentElement.parentElement;
            
            // Check if current element has the classes we're looking for
            if (currentElement.classList?.contains('s9t60i1')) {
                vfrcChat = currentElement;
                // Store original overflow value but DON'T set to hidden yet
                if (!vfrcChat.dataset.originalOverflow) {
                    vfrcChat.dataset.originalOverflow = vfrcChat.style.overflow || 'auto';
                }
            }
            
            if (currentElement.classList?.contains('vfrc-system-response')) {
                systemResponseElement = currentElement;
            }
            
            // If we found both, we can break early
            if (vfrcChat && systemResponseElement) {
                break;
            }
        }

        if (!vfrcChat) {
            console.error('Could not find .vfrc-chat element in parent hierarchy');
            return;
        }

        // Check if user has sent any messages or if conversation is already active - if so, don't render cards
        const dialogElement = vfrcChat.querySelector('.vfrc-chat--dialog');
        const hasUserMessages = dialogElement && dialogElement.querySelector('.vfrc-user-response');
        const hasMultipleMessages = dialogElement && dialogElement.children.length > 2;
        
        console.log('🔍 Dialog element found:', !!dialogElement);
        console.log('🔍 User messages found:', !!hasUserMessages);
        console.log('🔍 Dialog children count:', dialogElement ? dialogElement.children.length : 0);
        console.log('🔍 Has multiple messages (>2 children):', !!hasMultipleMessages);
        
        if (dialogElement) {
            const allUserMessages = dialogElement.querySelectorAll('.vfrc-user-response');
            console.log('🔍 Total user message elements:', allUserMessages.length);
            console.log('🔍 Dialog HTML:', dialogElement.outerHTML.substring(0, 500) + '...');
            allUserMessages.forEach((msg, i) => {
                console.log(`🔍 User message ${i + 1}:`, msg.textContent?.trim());
                console.log(`🔍 User message ${i + 1} classes:`, msg.className);
            });
        }
        
        if (hasUserMessages || hasMultipleMessages) {
            if (hasUserMessages) {
                console.log('✅ User has already sent messages - not rendering adaptive cards');
            }
            if (hasMultipleMessages) {
                console.log('✅ Conversation already active (>2 children) - not rendering adaptive cards');
            }
            console.log('🚫 Extension render blocked - active conversation detected');
            // Don't apply overflow hidden if cards aren't being rendered
            if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                delete vfrcChat.dataset.originalOverflow;
            }
            return;
        }
        
        console.log('✅ No user messages found - proceeding to render adaptive cards');
        console.log('🎴 Creating extension container...');

        // Only set overflow hidden if we're actually rendering cards
        if (vfrcChat) {
            vfrcChat.style.overflow = 'hidden';
        }

        // Remove the system response element if found
        if (systemResponseElement) {
            systemResponseElement.remove();
        }

        // Create new extension-cards container with glass overlay
        const extensionContainer = document.createElement('div');
        extensionContainer.id = 'extension-cards';
        extensionContainer.className = 'adaptive-cards-extension-container';
        
        console.log('🎴 Extension container created with ID:', extensionContainer.id);
        
        // Add extension-cards as a child of vfrc-chat
        vfrcChat.appendChild(extensionContainer);
        
        console.log('🎴 Extension container added to DOM');
        console.log('🎴 Extension container in DOM check:', !!document.getElementById('extension-cards'));

        // Hide chat input initially
        const chatInput = document.querySelector('.vfrc-chat--input');
        if (chatInput) {
            chatInput.style.display = 'none';
        }

        const cards = [
            { 
                title: "Performance Review Prep", 
                description: "How can I use my PI profile to better prepare for my performance review"
            },
            { 
                title: "Manager Role Practice", 
                description: "Roleplay as my manager so I can practice asking for more autonomy on this project"
            },
            { 
                title: "Compensation Meeting", 
                description: "Help me prep for my team's annual compensation meeting"
            }
        ];

        const container = document.createElement('div');
        container.className = 'adaptive-cards-container';

        cards.forEach((card, index) => {
            const cardElement = document.createElement('div');
            cardElement.className = 'adaptive-card';

            cardElement.innerHTML = `
                <div class="adaptive-card-content">
                    <h3 class="adaptive-card-title">${card.title}</h3>
                    <p class="adaptive-card-description">${card.description}</p>
                </div>
            `;

            // Sequential fade-in animation with higher fidelity
            setTimeout(() => {
                cardElement.style.opacity = '1';
                cardElement.style.transform = 'translateY(0)';
            }, index * 200 + 100); // 200ms delay between cards, 100ms initial delay

            // Hover effects are now handled by CSS

            cardElement.addEventListener('click', () => {
                // Get the current URL to extract module context
                const currentPath = window.location.pathname;
                const moduleMatch = currentPath.match(/\/modules\/([^\/]+)/);
                const moduleSlug = moduleMatch ? moduleMatch[1] : null;
                
                // Send interaction to Voiceflow with module context
                if (window.voiceflow?.chat?.interact) {
                    window.voiceflow.chat.interact({
                        type: 'complete',
                        payload: {
                            choice: card.title,
                            message: `I want to discuss ${card.title.toLowerCase()}`,
                            module: moduleSlug,
                            context: 'adaptive_card_selection'
                        }
                    });
                }

                // Fade out cards and show input
                extensionContainer.style.opacity = '0';
                setTimeout(() => {
                    extensionContainer.remove();
                    // Restore original overflow
                    if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                        vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                        delete vfrcChat.dataset.originalOverflow;
                    }
                    const chatInput = document.querySelector('.vfrc-chat--input');
                    if (chatInput) {
                        chatInput.style.display = 'flex';
                    }
                }, 300);
            });

            container.appendChild(cardElement);
        });

        // Render the cards inside the #extension-cards container
        extensionContainer.appendChild(container);

        // Set up MutationObserver to watch for changes and maintain proper state
        const chatDialog = vfrcChat.querySelector('.vfrc-chat--dialog');
        if (chatDialog) {
            const checkAndCleanupExtension = () => {
                // Use the actual extension container reference instead of getElementById
                const currentExtensionContainer = extensionContainer.parentNode ? extensionContainer : null;
                const hasUserMessages = chatDialog.querySelector('.vfrc-user-response');
                const hasMultipleMessages = chatDialog.children.length > 2;
                
                console.log('🔍 Cleanup check - Extension exists:', !!currentExtensionContainer);
                console.log('🔍 Cleanup check - User messages:', !!hasUserMessages);
                console.log('🔍 Cleanup check - Multiple messages (>2 children):', !!hasMultipleMessages);
                console.log('🔍 Cleanup check - Dialog children count:', chatDialog.children.length);
                
                if (hasUserMessages) {
                    const allUserMessages = chatDialog.querySelectorAll('.vfrc-user-response');
                    console.log('🔍 Cleanup check - Total user messages:', allUserMessages.length);
                }
                
                // Remove extension if user has sent any messages OR if conversation is already active
                if (currentExtensionContainer && (hasUserMessages || hasMultipleMessages)) {
                    console.log('🧹 Cleanup: User messages detected, removing extension');
                    console.log('🧹 Extension being removed:', currentExtensionContainer);
                    currentExtensionContainer.style.opacity = '0';
                    setTimeout(() => {
                        if (currentExtensionContainer.parentNode) {
                            console.log('🧹 Actually removing extension from DOM');
                            currentExtensionContainer.remove();
                            console.log('🧹 Extension removed successfully');
                        }
                        // Restore original overflow
                        if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                            vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                            delete vfrcChat.dataset.originalOverflow;
                        }
                        // Show chat input
                        const chatInput = document.querySelector('.vfrc-chat--input');
                        if (chatInput) {
                            chatInput.style.display = 'flex';
                        }
                    }, 300);
                    return true; // Extension was removed
                } else if (!currentExtensionContainer) {
                    console.log('🔍 No extension container to remove (already gone)');
                } else if (!hasUserMessages && !hasMultipleMessages) {
                    console.log('🔍 No user messages or multiple messages detected, extension staying active');
                }
                return false; // Extension still active
            };

            const observer = new MutationObserver((mutations) => {
                let shouldCheckForUserMessages = false;
                
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        // Check if user sent a message
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                // Check if this is a user response or contains one
                                const isUserMessage = node.classList?.contains('vfrc-user-response') || 
                                                    node.querySelector?.('.vfrc-user-response');
                                
                                if (isUserMessage) {
                                    console.log('🔄 User message detected in added node, immediately removing extension');
                                    shouldCheckForUserMessages = true;
                                    
                                    // Immediate removal when user message detected
                                    if (extensionContainer && extensionContainer.parentNode) {
                                        console.log('🚀 Immediate extension removal triggered');
                                        extensionContainer.style.opacity = '0';
                                        setTimeout(() => {
                                            if (extensionContainer.parentNode) {
                                                extensionContainer.remove();
                                                console.log('✅ Extension removed immediately');
                                            }
                                            // Restore original overflow
                                            if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                                                vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                                                delete vfrcChat.dataset.originalOverflow;
                                            }
                                            // Show chat input
                                            const chatInput = document.querySelector('.vfrc-chat--input');
                                            if (chatInput) {
                                                chatInput.style.display = 'flex';
                                            }
                                        }, 300);
                                    }
                                }
                            }
                        });
                    }
                });
                
                // If any user message was detected, or just run a periodic check
                if (shouldCheckForUserMessages || Math.random() < 0.1) { // 10% chance periodic check
                    // Run cleanup check which will detect user messages
                    const wasRemoved = checkAndCleanupExtension();
                    if (wasRemoved) {
                        console.log('✅ Extension removed due to user message detection');
                    }
                }
            });

            // Also observe style changes on vfrcChat to detect overflow changes
            const styleObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        checkAndCleanupExtension();
                    }
                });
            });

            // Start observing - watch the entire subtree for user messages
            observer.observe(chatDialog, {
                childList: true,
                subtree: true // Watch all child elements deeply
            });
            
            styleObserver.observe(vfrcChat, {
                attributes: true,
                attributeFilter: ['style']
            });

            // Initial check
            checkAndCleanupExtension();
            
            // Additional continuous monitoring - check every 500ms for user messages
            const continuousCheck = setInterval(() => {
                // Use the actual extension container reference
                const currentExtensionContainer = extensionContainer.parentNode ? extensionContainer : null;
                if (!currentExtensionContainer) {
                    // Extension already removed, stop checking
                    clearInterval(continuousCheck);
                    return;
                }
                
                const hasUserMessages = chatDialog.querySelector('.vfrc-user-response');
                const hasMultipleMessages = chatDialog.children.length > 2;
                
                if (hasUserMessages || hasMultipleMessages) {
                    if (hasUserMessages) {
                        console.log('🔄 Continuous check: User message detected, cleaning up');
                    }
                    if (hasMultipleMessages) {
                        console.log('🔄 Continuous check: Multiple messages detected, cleaning up');
                    }
                    const wasRemoved = checkAndCleanupExtension();
                    if (wasRemoved) {
                        clearInterval(continuousCheck);
                    }
                }
            }, 500);
            
            // Clean up interval when extension is removed
            const originalRemove = extensionContainer.remove;
            extensionContainer.remove = function() {
                clearInterval(continuousCheck);
                return originalRemove.call(this);
            };
        }
    }
};