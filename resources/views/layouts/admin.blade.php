<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400;500;600&display=swap" />

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        .material-symbols-outlined{font-variation-settings:'FILL'0,'wght'400,'GRAD'0,'opsz'24}
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="font-sans antialiased" x-data>
<div class="min-h-screen bg-gray-100 sm:p-6">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')

    {{-- CONTENT WRAPPER --}}
    <div class="flex-1"
        :class="{
            'md:ml-64': !Alpine.store('layout').sidebarCollapsed,
            'md:ml-16': Alpine.store('layout').sidebarCollapsed
        }">

        @include('layouts.navbars.nav-admin')

        <main>
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="px-0 sm:px-0 lg:px-0 pb-4">
                    <x-breadcrumbs />
                </div>
                @yield('content')

            </div>
        </main>
    </div>
</div>

<x-flash-toast />

<script>
    window.hasFormError = @json($errors->any() ?? false);
</script>

@stack('scripts')

</body>
</html>
