// Styling functions using CSS variables from voiceflow.css
function getExtensionContainerStyles() {
    return `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s ease-out;
    `;
}

function getCardsContainerStyles() {
    return `
        display: flex;
        flex-direction: row;
        gap: var(--spacing-lg);
        padding: var(--spacing-xl);
        transition: opacity 0.3s ease-out;
    `;
}

function getCardStyles() {
    return `
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: var(--spacing-lg);
        cursor: pointer;
        color: var(--card-foreground);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-sm);
        width: 280px;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        opacity: 0;
        transform: translateY(30px);
    `;
}

function getCardContentStyles() {
    return `
        text-align: left;
    `;
}

function getCardTitleStyles() {
    return `
        margin: 0 0 var(--spacing-xs) 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--foreground);
        line-height: 1.3;
    `;
}

function getCardDescriptionStyles() {
    return `
        margin: 0;
        font-size: 14px;
        color: var(--muted-foreground);
        line-height: 1.4;
    `;
}

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

        // Check if conversation is already active - allow only ADAPTIVE system responses
        const dialogElement = vfrcChat.querySelector('.vfrc-chat--dialog');
        const hasNonAdaptiveMessages = dialogElement && (
            dialogElement.children.length > 2 || 
            dialogElement.querySelector('.vfrc-message:not(.vfrc-message--extension-ADAPTIVE)')
        );
        
        if (hasNonAdaptiveMessages) {
            console.log('Conversation is already active');
            // Don't apply overflow hidden if cards aren't being rendered
            if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                delete vfrcChat.dataset.originalOverflow;
            }
            return;
        }

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
        extensionContainer.style.cssText = getExtensionContainerStyles();
        
        // Add extension-cards as a child of vfrc-chat
        vfrcChat.appendChild(extensionContainer);

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
        container.style.cssText = getCardsContainerStyles();

        cards.forEach((card, index) => {
            const cardElement = document.createElement('div');
            cardElement.className = 'adaptive-card';
            cardElement.style.cssText = getCardStyles();

            cardElement.innerHTML = `
                <div style="${getCardContentStyles()}">
                    <h3 style="${getCardTitleStyles()}">${card.title}</h3>
                    <p style="${getCardDescriptionStyles()}">${card.description}</p>
                </div>
            `;

            // Sequential fade-in animation with higher fidelity
            setTimeout(() => {
                cardElement.style.opacity = '1';
                cardElement.style.transform = 'translateY(0)';
            }, index * 200 + 100); // 200ms delay between cards, 100ms initial delay

            cardElement.addEventListener('mouseenter', () => {
                cardElement.style.transform = 'translateY(-2px)';
                cardElement.style.boxShadow = 'var(--shadow-md)';
            });

            cardElement.addEventListener('mouseleave', () => {
                cardElement.style.transform = 'translateY(0)';
                cardElement.style.boxShadow = 'var(--shadow-sm)';
            });

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
                const currentExtensionContainer = document.getElementById('extension-cards');
                const hasOverflowHidden = vfrcChat.style.overflow === 'hidden';
                const hasNonAdaptiveMessages = chatDialog.children.length > 2 || 
                    chatDialog.querySelector('.vfrc-message:not(.vfrc-message--extension-ADAPTIVE)');
                
                // Remove extension if overflow is not hidden OR if there are non-adaptive messages
                if (currentExtensionContainer && (!hasOverflowHidden || hasNonAdaptiveMessages)) {
                    currentExtensionContainer.style.opacity = '0';
                    setTimeout(() => {
                        if (currentExtensionContainer.parentNode) {
                            currentExtensionContainer.remove();
                        }
                        // Restore original overflow
                        if (vfrcChat && vfrcChat.dataset.originalOverflow) {
                            vfrcChat.style.overflow = vfrcChat.dataset.originalOverflow;
                            delete vfrcChat.dataset.originalOverflow;
                        }
                    }, 300);
                    return true; // Extension was removed
                }
                return false; // Extension still active
            };

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        checkAndCleanupExtension();
                    }
                });
            });

            // Also observe style changes on vfrcChat to detect overflow changes
            const styleObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        checkAndCleanupExtension();
                    }
                });
            });

            // Start observing
            observer.observe(chatDialog, {
                childList: true,
                subtree: false
            });
            
            styleObserver.observe(vfrcChat, {
                attributes: true,
                attributeFilter: ['style']
            });

            // Initial check
            checkAndCleanupExtension();
        }
    }
};