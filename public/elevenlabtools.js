// ElevenLabs ConvAI Integration Tools
console.log('📦 ElevenLabTools.js loaded');

// Set up client-side tools for ElevenLabs
document.addEventListener('elevenlabs-convai:call', (event) => {
    console.log('🔧 ElevenLabs widget call event triggered');
    console.log('📦 window.chatContext is available with data:', window.chatContext);
    
    event.detail.config.clientTools = {
        getChatSessionData: () => {
            console.log('📞 getChatSessionData called by ElevenLabs');
            console.log('📦 window.chatContext is available with data:', window.chatContext);
            return window.chatContext;
        },
        showUsersPIChart: () => {
            window.profile.chart.show();
            return true;
        },
        hideUsersPIChart: () => {
            window.profile.chart.hide();
            return true;
        }
    };
    
    console.log('✅ ElevenLabs client tools configured');
});

console.log('🎙️ ElevenLabs event listener registered for elevenlabs-convai:call');