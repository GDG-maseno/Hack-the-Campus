<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
        <link rel="icon" type="image/jpeg" href="/images/logo.jpeg">
        <link rel="apple-touch-icon" href="/images/logo-icon.png">


        <!-- Google Fonts: Inter and Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- MapLibre GL JS -->
        <link href="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.css" rel="stylesheet" />
        <script src="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        @wirekitStyles
    </head>
    <body class="bg-[var(--color-wk-bg)] text-[var(--color-wk-text)] font-sans antialiased min-h-screen">
        
        <x-wirekit::app-shell>
            <x-slot:header>
                <x-wirekit::navbar>
                    <x-wirekit::brand logo="/images/logo.jpeg" name="" href="/" />
                    
                    <div class="flex gap-4">
                        <x-wirekit::navbar.item href="/" :active="request()->is('/')">Home</x-wirekit::navbar.item>
                        <x-wirekit::navbar.item href="/services" :active="request()->is('services*')">Services</x-wirekit::navbar.item>
                        <x-wirekit::navbar.item href="/how-it-works" :active="request()->is('how-it-works')">How it Works</x-wirekit::navbar.item>
                        
                        @auth
                            <x-wirekit::navbar.item href="{{ route('dashboard.bookings') }}" :active="request()->is('dashboard/bookings*')">My Bookings</x-wirekit::navbar.item>

                            @if(auth()->user()->isAdmin())
                                <x-wirekit::navbar.item href="/admin" :active="request()->is('admin*')">Admin Dashboard</x-wirekit::navbar.item>
                            @endif

                            @if(auth()->user()->isProvider())
                                <x-wirekit::navbar.item href="{{ route('provider.dashboard') }}" :active="request()->is('provider/dashboard*')" class="text-amber-500 font-semibold">Provider Hub</x-wirekit::navbar.item>
                                @if(auth()->user()->kyc_status === 'approved')
                                    <x-wirekit::navbar.item href="{{ route('provider.profile', ['slug' => auth()->id()]) }}" :active="request()->is('provider/' . auth()->id())">Public Profile</x-wirekit::navbar.item>
                                @else
                                    <x-wirekit::navbar.item href="{{ route('provider.onboarding') }}" :active="request()->is('provider/onboarding*')" class="text-wk-accent text-xs">Onboarding</x-wirekit::navbar.item>
                                @endif
                            @elseif(auth()->user()->isClient())
                                <x-wirekit::navbar.item href="{{ route('provider.onboarding') }}" :active="request()->is('provider/onboarding*')" class="text-wk-accent font-medium">Become a Provider</x-wirekit::navbar.item>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-wk-text-subtle hover:text-wk-text-strong px-3 py-2 rounded-md text-sm font-medium transition-colors">Log Out</button>
                            </form>
                        @else
                            <x-wirekit::navbar.item href="/login" :active="request()->is('login')">Log In</x-wirekit::navbar.item>
                            <x-wirekit::button href="/register" tag="a" size="sm" variant="primary" class="ml-2">Sign Up</x-wirekit::button>
                        @endauth
                    </div>
                </x-wirekit::navbar>
            </x-slot:header>
            
            <x-wirekit::main>
                {{ $slot }}
            </x-wirekit::main>
        </x-wirekit::app-shell>

        @wirekitScripts
        @livewireScripts
    </body>
</html>
