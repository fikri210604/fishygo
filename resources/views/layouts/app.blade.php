<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400;500;600&display=swap" />

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head data-theme="mytheme">

<body class="font-sans antialiased min-h-screen flex flex-col">
    <div class="flex-1 bg-gray-100 pt-16">
        @auth
            @can('access-admin')
                @include('layouts.navbars.nav-admin')
            @else
                @include('layouts.navbars.nav-user')
            @endcan
        @else
            @include('layouts.navbars.nav-public')
        @endauth

        <div class="max-w-7xl mx-auto">
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <x-breadcrumbs />
            </div>
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="px-4 sm:px-6 lg:px-8 py-6">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
    </div>
    @include('layouts.footer')
    @if (! View::hasSection('hide-toast'))
        <x-flash-toast />
    @endif
    @stack('scripts')
</body>

</html>
