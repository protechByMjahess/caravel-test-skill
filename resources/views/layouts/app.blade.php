<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    
    <!-- Fallback for development -->
    @if(app()->environment('local') && !file_exists(public_path('build/manifest.json')))
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-900">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('projects.list') }}" 
                           class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Projects
                        </a>
                        <a href="{{ route('projects.create') }}" 
                           class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            New Project
                        </a>
                        
                        @auth
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
