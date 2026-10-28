<!DOCTYPE html>
<html lang="en" class="h-full bg-neutral-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Example Research Institution (DEMO) Ethics Review Board Portal — Manage, track, and review research ethics protocols.">
    @if(auth()->check())
        <meta name="robots" content="noindex, nofollow, noarchive">
    @else
        <meta name="robots" content="index, follow">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $title ?? 'Dashboard' }} | DEMO ERB Portal</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="/vendor/sign-pad/sign-pad.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body 
    x-data="{ sidebarHidden: localStorage.getItem('erb_sidebar_hidden') === 'true', mobileOpen: false }" 
    x-init="$watch('sidebarHidden', val => localStorage.setItem('erb_sidebar_hidden', val))"
    class="h-full flex text-gray-900 bg-neutral-50 overflow-hidden"
>

    <!-- Global DEMO Maroon Top Loading Indicator Bar -->
    <div 
        x-data="{ loading: false, progress: 0 }" 
        x-init="
            window.addEventListener('beforeunload', () => { loading = true; progress = 70; });
            document.addEventListener('livewire:navigating', () => { loading = true; progress = 35; });
            document.addEventListener('livewire:navigated', () => { progress = 100; setTimeout(() => { loading = false; progress = 0; }, 300); });
            document.addEventListener('livewire:init', () => {
                Livewire.hook('request', ({ respond }) => {
                    loading = true;
                    progress = 45;
                    respond(() => {
                        progress = 100;
                        setTimeout(() => { loading = false; progress = 0; }, 300);
                    });
                });
            });
        "
        x-show="loading"
        x-cloak
        class="fixed top-0 left-0 right-0 z-[99999] h-[3px] bg-transparent pointer-events-none"
    >
        <div 
            class="h-full transition-all duration-300 ease-out"
            :style="`width: ${progress}%; background-color: #800000; box-shadow: 0 0 10px rgba(128, 0, 0, 0.8);`"
        ></div>
    </div>

    <!-- Mobile Slide-Over Backdrop (lg:hidden) -->
    @auth
    <!-- Desktop Sidebar (Statically Rendered to Prevent Any Initial Page Load Motion or Layout Shift) -->
    <aside 
        :class="sidebarHidden ? 'lg:hidden' : 'lg:flex'"
        class="hidden lg:flex w-64 bg-white border-r border-gray-200 flex-col justify-between shrink-0 h-full lg:static lg:z-20 overflow-hidden"
    >
        @include('layouts.sidebar-content')
    </aside>

    <!-- Mobile Slide-Over Backdrop (lg:hidden) -->
    <div 
        x-show="mobileOpen" 
        @click="mobileOpen = false"
        x-cloak
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 z-40 lg:hidden"
    ></div>

    <!-- Mobile Slide-Over Drawer (lg:hidden, ONLY animates when hamburger is tapped) -->
    <aside 
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 flex w-64 bg-white border-r border-gray-200 flex-col justify-between shrink-0 h-full lg:hidden overflow-hidden shadow-2xl"
    >
        @include('layouts.sidebar-content', ['isMobile' => true])
    </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden h-full">
        
        <!-- Global Site-Wide System Announcement Banner (Developer Controlled) -->
        @php
            $activeAnnouncement = \App\Models\SystemAnnouncement::getActive();
        @endphp

        @if($activeAnnouncement)
            <div x-data="{ dismissed: false }" x-show="!dismissed" class="w-full text-white font-heading relative z-0 shrink-0 transition-all shadow-xs"
                style="
                    @if($activeAnnouncement->type === 'down') background-color: #991b1b;
                    @elseif($activeAnnouncement->type === '404_issue') background-color: #c2410c;
                    @elseif($activeAnnouncement->type === '402_issue') background-color: #b45309;
                    @elseif($activeAnnouncement->type === 'maintenance') background-color: #800000;
                    @else background-color: #1e3a8a;
                    @endif
                "
            >
                <div class="max-w-7xl mx-auto px-3 md:px-6 py-1.5 md:py-2.5 flex items-center justify-between gap-2 md:gap-4 text-[11px] md:text-xs">
                    <div class="flex items-center gap-2 md:gap-3 min-w-0">
                        <span class="px-2 py-0.5 rounded-md text-[9px] md:text-[10px] uppercase font-extrabold tracking-wider bg-white/20 border border-white/30 text-white shrink-0 font-heading inline-flex items-center gap-1 animate-pulse-glow">
                            @if($activeAnnouncement->type === 'maintenance')
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 012 2v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h1.586A2 2 0 0111 4zM4 14h16m-2 0v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4"/></svg>
                                <span>Maintenance</span>
                            @elseif($activeAnnouncement->type === 'down')
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Outage</span>
                            @elseif($activeAnnouncement->type === '404_issue')
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span>404 Warning</span>
                            @elseif($activeAnnouncement->type === '402_issue')
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Fee Issue</span>
                            @else
                                <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Notice</span>
                            @endif
                        </span>
                        <div class="min-w-0 text-[11px] md:text-xs leading-tight md:leading-normal">
                            <strong class="font-extrabold text-white font-heading">{{ $activeAnnouncement->title }}:</strong>
                            <span class="text-white/90 font-medium font-heading">{{ $activeAnnouncement->message }}</span>
                        </div>
                    </div>
                    <button @click="dismissed = true" class="text-white/80 hover:text-white font-extrabold text-base md:text-lg p-0.5 leading-none shrink-0 cursor-pointer" title="Dismiss Alert">&times;</button>
                </div>
            </div>
        @endif

        <!-- Top Header Navigation Bar -->
        <header class="bg-white border-b border-gray-200 h-16 px-4 md:px-6 lg:px-8 flex items-center justify-between shrink-0 font-heading">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0 flex-1 mr-2 sm:mr-4">
                @auth
                <!-- Desktop Sidebar Collapse Toggle Button -->
                <button 
                    @click="sidebarHidden = !sidebarHidden" 
                    title="Toggle Sidebar Navigation"
                    aria-label="Toggle Sidebar Navigation"
                    class="hidden lg:flex p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors items-center justify-center cursor-pointer font-heading shrink-0"
                >
                    <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': sidebarHidden }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Mobile Hamburger Button -->
                <button 
                    @click="mobileOpen = true" 
                    title="Open Mobile Navigation Menu"
                    aria-label="Open Mobile Navigation Menu"
                    class="lg:hidden p-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors flex items-center justify-center cursor-pointer shrink-0"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @else
                <a href="/" class="flex items-center gap-2.5 shrink-0">
                    <img src="/images/pwu-logo.png" alt="DEMO Seal" class="w-8 h-8 object-contain shrink-0" />
                    <span class="font-extrabold text-sm text-gray-900 font-heading" style="color: #800000;">DEMO ERB Portal</span>
                </a>
                @endauth

                <h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 font-heading truncate">{{ $header ?? 'Dashboard' }}</h2>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 lg:gap-4 shrink-0 min-w-0">
                @auth
                    <!-- Global Search Component (Hidden on mobile and hidden for student users) -->
                    @if(auth()->user()->hasAnyRole(['committee', 'admin']) || auth()->user()->is_developer)
                        <div class="hidden md:block shrink max-w-full">
                            @livewire('global-search')
                        </div>
                    @endif

                    <!-- Notification Dropdown -->
                    @livewire('notification-dropdown')

                    <!-- User Profile Avatar Pill -->
                    <div class="flex items-center gap-2 sm:gap-2.5 pl-2 sm:pl-3 border-l border-gray-200 shrink-0 min-w-0">
                        <div class="relative w-8 h-8 shrink-0 flex items-center justify-center">
                            @if(auth()->user()->avatar)
                                <img 
                                    src="{{ auth()->user()->avatar }}" 
                                    alt="{{ auth()->user()->name }}" 
                                    referrerpolicy="no-referrer"
                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');"
                                    class="w-8 h-8 rounded-full object-cover shrink-0 border border-gray-200 shadow-sm" 
                                />
                            @endif
                            <div @class([
                                'w-8 h-8 rounded-full bg-red-50 font-bold text-xs items-center justify-center border border-red-200 shrink-0 font-heading select-none',
                                'hidden' => !empty(auth()->user()->avatar),
                                'flex' => empty(auth()->user()->avatar),
                            ]) style="background-color: #FFF5F5; color: #800000;">
                                {{ auth()->user()->initials }}
                            </div>
                        </div>
                        <div class="hidden sm:flex flex-col justify-center text-left font-heading min-w-0">
                            <p class="text-xs font-bold text-gray-900 leading-snug truncate max-w-[110px] md:max-w-[140px] lg:max-w-[180px] xl:max-w-[220px] font-heading">{{ auth()->user()->name }}</p>
                            <span class="text-[10px] text-gray-500 font-semibold capitalize block font-heading leading-tight truncate">{{ auth()->user()->roles->first()?->name ?? 'Student' }}</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <a href="/" class="px-4 py-2 text-white font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-1.5" style="background-color: #800000;">
                            <span>Sign In</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Main Body -->
        <main @class([
            'flex-1 overflow-y-auto',
            'p-4 md:p-6 lg:p-8' => !request()->is('requests/*/documents/*'),
            'p-0 bg-neutral-900' => request()->is('requests/*/documents/*')
        ])>
            @if(!request()->is('requests/*/documents/*'))
                <div class="max-w-7xl mx-auto space-y-6 w-full">
                    @if(session('success'))
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 font-medium flex items-center justify-between shadow-sm">
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800 font-medium flex items-center justify-between shadow-sm">
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            @else
                {{ $slot ?? '' }}
                @yield('content')
            @endif
        </main>
    </div>

    <!-- Global Popups & Toast Notifications Container (Alpine.js) -->
    <div 
        x-data="{ 
            notifications: [],
            add(msg, type = 'success', title = '') {
                const id = Date.now() + Math.random();
                this.notifications.push({ id, msg, type, title });
                setTimeout(() => this.remove(id), 6000);
            },
            remove(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }"
        x-init="
            @if(session('success'))
                add('{{ addslashes(session('success')) }}', 'success', 'Success');
            @endif
            @if(session('error'))
                add('{{ addslashes(session('error')) }}', 'error', 'Action Failed');
            @endif
            @if(session('warning'))
                add('{{ addslashes(session('warning')) }}', 'warning', 'Warning');
            @endif
            window.addEventListener('notify', e => {
                const detail = e.detail || {};
                add(detail.message || detail.msg || detail[0] || 'Notification', detail.type || 'success', detail.title || '');
            });
        "
        class="fixed top-3 sm:top-4 left-3 right-3 sm:left-auto sm:right-4 z-[999999] flex flex-col gap-2 sm:gap-3 sm:max-w-sm pointer-events-none font-heading mx-auto"
        data-auto-animate
    >
        <template x-for="item in notifications" :key="item.id">
            <div 
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                :class="{
                    'bg-white border-t-[3px] border-t-emerald-600 text-gray-900': item.type === 'success',
                    'bg-white border-t-[3px] border-t-red-700 text-gray-900': item.type === 'error',
                    'bg-white border-t-[3px] border-t-amber-600 text-gray-900': item.type === 'warning'
                }"
                class="pointer-events-auto p-4 rounded-xl border border-gray-200 shadow-sm flex items-start gap-3 relative overflow-hidden"
            >
                <div class="shrink-0 mt-0.5">
                    <template x-if="item.type === 'success'">
                        <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-extrabold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </template>
                    <template x-if="item.type === 'error'">
                        <div class="w-7 h-7 rounded-full bg-red-100 text-red-800 flex items-center justify-center font-extrabold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    </template>
                    <template x-if="item.type === 'warning'">
                        <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-extrabold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </template>
                </div>
                <div class="flex-1 min-w-0 pr-4">
                    <template x-if="item.title">
                        <h5 class="text-xs font-bold text-gray-900 leading-tight mb-0.5 font-heading" x-text="item.title"></h5>
                    </template>
                    <p class="text-xs text-gray-700 font-medium leading-normal font-heading" x-text="item.msg"></p>
                </div>
                <button @click="remove(item.id)" class="text-gray-400 hover:text-gray-600 font-extrabold text-lg leading-none cursor-pointer absolute top-2 right-2 p-1">&times;</button>
            </div>
        </template>
    </div>

    <!-- Global Floating Bottom-Right Bug Report & Help Widget -->
    @livewire('bug-report-widget')

    @livewireScripts
</body>
</html>
