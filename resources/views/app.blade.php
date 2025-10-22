<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        {{-- Adaptive Card Extension for Voiceflow --}}
        <script src="/adaptive-card-extension.js?v={{ time() }}"></script>

        {{-- Voiceflow DOM Observer - handles audio button modifications --}}
        <script src="/voiceflow-dom.js?v={{ time() }}"></script>

        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        
        {{-- Load ElevenLabs scripts for audio mode only --}}
        @if(isset($page['props']['isAudioMode']) && $page['props']['isAudioMode'])
        <script src="https://unpkg.com/@elevenlabs/convai-widget-embed" async></script>
        <script>
            window.addEventListener('load', function() {
                // Add a small delay to ensure everything is fully loaded
                setTimeout(function() {
                    const script = document.createElement('script');
                    script.src = '/eleven-labs.js';
                    script.async = false; // Load synchronously after delay
                    document.body.appendChild(script);
                }, 100);
            });
        </script>
        <script src="/elevenlabtools.js" async></script>
        @endif
    </body>
</html>
