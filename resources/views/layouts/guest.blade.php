<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FishyGo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased min-h-screen flex flex-col">
    @auth
        @can('access-admin')
            @include('layouts.navbars.nav-admin')
        @else
            @include('layouts.navbars.nav-user')
        @endcan
    @else
        @include('layouts.navbars.nav-public')
    @endauth

    <div class="flex-1 bg-gray-100 pt-16">
        <div class="max-w-7xl mx-auto">
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <x-breadcrumbs />
            </div>
            <main class="px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @include('layouts.footer')
    <x-flash-toast />
</body>

</html>
