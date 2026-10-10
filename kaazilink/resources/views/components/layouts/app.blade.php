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
    <body class="bg-[var(--color-wk-bg)] text-[var(--color-wk-text)] font-sans antialiased min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">
        
        <x-wirekit::app-shell class="flex-1 w-full">
            <x-slot:header>
                {{-- Desktop Navbar (Hidden on mobile) --}}
                <div class="hidden md:block">
                    <x-wirekit::navbar>
                        <x-wirekit::brand logo="/images/logo.jpeg" name="" href="/" />
                        
                        <div class="flex gap-4 items-center">
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
                </div>

                {{-- Mobile Header Bar (Mobile only: Menu icon on top-left + KAAZILINK text logo) --}}
                <div class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="mobileMenuOpen = true" class="p-2 -ml-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#006B3F]" aria-label="Open Menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <a href="/" class="flex items-center" aria-label="KaaziLink Home">
                            <img src="/images/logo.jpeg" alt="KaaziLink" class="h-9 w-auto object-contain">
                        </a>
                    </div>

                    <div>
                        @guest
                            <a href="/login" class="text-sm font-semibold text-[#006B3F] hover:text-[#005432]">Log In</a>
                        @endguest
                    </div>
                </div>

                {{-- Mobile Sidebar Drawer --}}
                <div x-show="mobileMenuOpen" class="relative z-50 md:hidden" style="display: none;" role="dialog" aria-modal="true">
                    {{-- Backdrop --}}
                    <div x-show="mobileMenuOpen" 
                         x-transition:enter="transition-opacity ease-linear duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition-opacity ease-linear duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="mobileMenuOpen = false"
                         class="fixed inset-0 bg-black/50 backdrop-blur-xs"></div>

                    <div class="fixed inset-0 flex">
                        <div x-show="mobileMenuOpen"
                             x-transition:enter="transition ease-in-out duration-300 transform"
                             x-transition:enter-start="-translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition ease-in-out duration-300 transform"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="-translate-x-full"
                             class="relative flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4 shadow-xl">
                            
                            {{-- Drawer Header --}}
                            <div class="flex items-center justify-between px-5 pb-4 border-b border-gray-100">
                                <a href="/" class="flex items-center" @click="mobileMenuOpen = false" aria-label="KaaziLink Home">
                                    <img src="/images/logo.jpeg" alt="KaaziLink" class="h-9 w-auto object-contain">
                                </a>
                                <button type="button" @click="mobileMenuOpen = false" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Drawer Nav Links --}}
                            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                                <a href="/" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium {{ request()->is('/') ? 'bg-[#006B3F]/10 text-[#006B3F] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                    Home
                                </a>
                                <a href="/services" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium {{ request()->is('services*') ? 'bg-[#006B3F]/10 text-[#006B3F] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                    Services
                                </a>
                                <a href="/how-it-works" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium {{ request()->is('how-it-works') ? 'bg-[#006B3F]/10 text-[#006B3F] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                    How it Works
                                </a>

                                @auth
                                    <div class="pt-3 pb-1 border-t border-gray-100 my-2">
                                        <span class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Account</span>
                                    </div>
                                    <a href="{{ route('dashboard.bookings') }}" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium {{ request()->is('dashboard/bookings*') ? 'bg-[#006B3F]/10 text-[#006B3F] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        My Bookings
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        <a href="/admin" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium {{ request()->is('admin*') ? 'bg-[#006B3F]/10 text-[#006B3F] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                            Admin Dashboard
                                        </a>
                                    @endif

                                    @if(auth()->user()->isProvider())
                                        <a href="{{ route('provider.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium text-amber-600 font-semibold hover:bg-amber-50">
                                            Provider Hub
                                        </a>
                                        @if(auth()->user()->kyc_status === 'approved')
                                            <a href="{{ route('provider.profile', ['slug' => auth()->id()]) }}" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50">
                                                Public Profile
                                            </a>
                                        @else
                                            <a href="{{ route('provider.onboarding') }}" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium text-[#006B3F] hover:bg-gray-50">
                                                Onboarding
                                            </a>
                                        @endif
                                    @elseif(auth()->user()->isClient())
                                        <a href="{{ route('provider.onboarding') }}" class="flex items-center px-3 py-2.5 rounded-lg text-base font-medium text-[#006B3F] hover:bg-gray-50 font-semibold">
                                            Become a Provider
                                        </a>
                                    @endif
                                @endauth
                            </div>

                            {{-- Drawer Footer Auth --}}
                            <div class="border-t border-gray-100 p-4">
                                @auth
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            Log Out
                                        </button>
                                    </form>
                                @else
                                    <div class="space-y-2">
                                        <a href="/login" class="w-full flex justify-center py-2.5 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                            Log In
                                        </a>
                                        <a href="/register" class="w-full flex justify-center py-2.5 px-4 rounded-lg text-sm font-medium text-white bg-[#006B3F] hover:bg-[#005432] transition-colors">
                                            Sign Up
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot:header>
            
            <x-wirekit::main>
                {{ $slot }}
            </x-wirekit::main>
        </x-wirekit::app-shell>

        {{-- Universal Application Footer --}}
        <footer class="bg-white border-t border-gray-200 mt-auto w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-6 border-b border-gray-100 text-center md:text-left">
                    <div class="space-y-2">
                        <a href="/" class="inline-flex items-center" aria-label="KaaziLink Home">
                            <img src="/images/logo.jpeg" alt="KaaziLink" class="h-12 w-auto object-contain">
                        </a>
                        <p class="text-xs text-gray-500 font-inter">
                            Kenya's community service connection marketplace &bull; <span class="text-[#006B3F] font-medium">Tupate Fundi Haraka</span>
                        </p>
                    </div>

                    {{-- Focused, high-value quick actions --}}
                    <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-inter">
                        <a href="/" class="text-gray-600 hover:text-[#006B3F] transition-colors">Home</a>
                        <a href="/services" class="text-gray-600 hover:text-[#006B3F] transition-colors">Services</a>
                        <a href="/how-it-works" class="text-gray-600 hover:text-[#006B3F] transition-colors">How It Works</a>
                        <a href="{{ route('provider.onboarding') }}" class="text-[#006B3F] font-semibold hover:text-[#005432] transition-colors">Become a Provider</a>
                        <a href="{{ route('terms') }}" class="text-gray-600 hover:text-[#006B3F] transition-colors">Terms</a>
                        <a href="{{ route('privacy') }}" class="text-gray-600 hover:text-[#006B3F] transition-colors">Privacy</a>
                    </nav>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500 font-inter">
                    <p>
                        &copy; {{ now()->year }} Kaazilink Kenya. All rights reserved.
                    </p>
                    <div class="flex items-center space-x-4">
                        <span>Support: <a href="mailto:hello@kaazilink.co.ke" class="text-[#006B3F] hover:underline">hello@kaazilink.co.ke</a></span>
                        <span class="text-gray-300">&bull;</span>
                        <span class="text-gray-400">Made with pride in Kenya 🇰🇪</span>
                    </div>
                </div>
            </div>
        </footer>

        {{-- Universal Floating Toast Notification --}}
        <div 
            x-data="{ show: false, message: '' }"
            x-on:booking-success-toast.window="message = $event.detail.message || $event.detail[0]?.message || 'Booking request sent!'; show = true; setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-5 right-5 z-50 max-w-md bg-emerald-800 text-white px-5 py-3.5 rounded-xl shadow-2xl flex items-center gap-3 border border-emerald-600 font-sans"
            style="display: none;"
        >
            <svg class="w-6 h-6 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium" x-text="message"></span>
        </div>

        @wirekitScripts
        @livewireScripts
    </body>
</html>
