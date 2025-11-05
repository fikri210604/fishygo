<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <header class="bg-white shadow h-16 flex items-center">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Panel</h2>
                        <nav class="space-x-4 text-sm">
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">Dashboard</a>
                            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline">Users</a>
                            <a href="{{ route('admin.admins.index') }}" class="text-indigo-600 hover:underline">Admins</a>
                            <a href="{{ route('admin.articles.index') }}" class="text-indigo-600 hover:underline">Artikel</a>
                        </nav>
                    </div>
                </div>
            </header>

            <main>
                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
        <x-flash-toast />
    </body>
    </html>
