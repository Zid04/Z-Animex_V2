<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    translate="no"
    @class(['dark' => ($appearance ?? 'system') === 'dark'])
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Disable auto-translation -->
    <meta name="google" content="notranslate">

    <!-- Apply system dark mode instantly -->
    <script>
        (function() {
            const appearance = '{{ $appearance ?? "system" }}';
            if (appearance === 'system') {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                }
            }
        })();
    </script>

    <!-- Background color to avoid white flash -->
    <style>
        html { background-color: oklch(1 0 0); }
        html.dark { background-color: oklch(0.145 0 0); }
    </style>

    <!-- Title -->
    <title inertia>{{ config('app.name', 'Animex') }}</title>

    <!-- Icons -->
    <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.ico" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Vite -->
    @viteReactRefresh
@vite('resources/js/app.tsx')

    <!-- Inertia -->
    @inertiaHead
</head>

<body class="notranslate font-sans antialiased" translate="no">
    @inertia
</body>
</html>
