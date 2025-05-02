<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-black">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-block transition-transform hover:scale-105">
                        <svg class="w-14 h-14 text-[#F53003] dark:text-[#F61500]" viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.0279 0L50 12.9677V38.7419L25.0279 51.6129L0 38.7419V12.9677L25.0279 0Z" fill="currentColor"/>
                        </svg>
                    </a>
                </div>

                <!-- Content -->
                <div class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl shadow-lg dark:shadow-2xl rounded-2xl overflow-hidden border border-gray-100/50 dark:border-gray-800/50">
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                </div>
            </div>
        </div>
    </body>
</html>
