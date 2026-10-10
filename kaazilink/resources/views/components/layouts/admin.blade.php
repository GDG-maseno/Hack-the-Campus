<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kaazilink Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    {{-- ================================================================= --}}
    {{-- ADMIN NAVIGATION                                                   --}}
    {{-- Desktop : fixed left sidebar (unchanged)                           --}}
    {{-- Mobile  : collapsible horizontal banner with toggle arrow + X     --}}
    {{-- ================================================================= --}}
    <div
        x-data="{ open: false }"
        class="shrink-0 md:w-64 md:h-screen md:sticky md:top-0"
    >
        {{-- ---- Mobile top bar (visible only on mobile) ---- --}}
        <div class="md:hidden bg-[#006B3F] text-white">
            <div class="flex items-center justify-between px-4 py-3 border-b border-[#005a35]">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Kaazilink <span class="text-[#E8A317]">Admin</span>
                </a>
                {{-- Toggle button: shows chevron-down when closed, X when open --}}
                <button
                    x-on:click="open = !open"
                    type="button"
                    class="p-2 rounded-lg hover:bg-[#005a35] transition-colors focus:outline-none"
                    :aria-expanded="open.toString()"
                    aria-label="Toggle navigation menu"
                >
                    {{-- Chevron down (menu closed) --}}
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    {{-- X (menu open) --}}
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Collapsible panel --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="border-b border-[#005a35]"
            >
                <nav class="px-4 py-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" x-on:click="open = false" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.kyc.index') }}" x-on:click="open = false" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.kyc.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                        KYC Reviews
                    </a>
                    <a href="{{ route('admin.settings.index') }}" x-on:click="open = false" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                        Site Settings
                    </a>
                    <a href="{{ route('admin.metrics.index') }}" x-on:click="open = false" class="flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.metrics.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                        Business Metrics <span class="text-xs bg-[#E8A317] text-black px-1.5 py-0.5 rounded ml-2">Soon</span>
                    </a>
                </nav>
                <div class="px-4 py-3 border-t border-[#005a35] flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm text-gray-200 hover:text-white hover:underline">← Main Site</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-200 hover:text-white hover:underline">Log Out</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ---- Desktop sidebar (hidden on mobile) ---- --}}
        <aside class="hidden md:flex w-64 bg-[#006B3F] text-white flex-col shadow-lg h-screen sticky top-0">
            <div class="p-6 flex items-center justify-center border-b border-[#005a35]">
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Kaazilink <span class="text-[#E8A317]">Admin</span>
                </a>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.kyc.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.kyc.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                    KYC Reviews
                </a>
                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                    Site Settings
                </a>
                <a href="{{ route('admin.metrics.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.metrics.*') ? 'bg-[#005a35] font-semibold' : 'hover:bg-[#005a35]' }}">
                    Business Metrics <span class="text-xs bg-[#E8A317] text-black px-1.5 py-0.5 rounded ml-1">Soon</span>
                </a>
            </nav>
            <div class="p-4 border-t border-[#005a35]">
                <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-center hover:underline">Back to Main Site</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2 text-center">
                    @csrf
                    <button type="submit" class="w-full text-sm hover:underline text-gray-200 hover:text-white px-4 py-2">Log Out</button>
                </form>
            </div>
        </aside>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto w-full">
        <div class="max-w-6xl mx-auto">
            {{ $slot }}
        </div>
    </main>

</body>
</html>
