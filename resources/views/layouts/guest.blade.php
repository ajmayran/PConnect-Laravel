<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>PConnect</title>
        <script src="https://unpkg.com/iconify-icon/dist/iconify-icon.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="flex flex-col items-center pt-6 bg-gradient-to-r from-green-400 to-green-600 sm:justify-center sm:pt-0 dark:bg-gray-900">

            <div class="w-full shadow-md bg-gradient-to-r from-green-400 to-green-600 sm:max-w-md sm:rounded-lg over">
                {{ $slot }}
            </div>

        </div>
    </body>
<style>
    @media (min-width: 640px) {
        .sm\:max-w-md {
            max-width: 100%;
        }
    }
</style>
</html>
