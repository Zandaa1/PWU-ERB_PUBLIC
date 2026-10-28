<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DEMO Ethics Review Board — Developer Console & Quick Logins (Demo Mode)">
    <title>DEMO ERB | Developer Console & Quick Logins</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php $devAnnouncement = \App\Models\SystemAnnouncement::getActive(); @endphp
</head>
<body class="min-h-screen w-full font-heading bg-[#F8F7F6] text-neutral-900 antialiased selection:bg-[#800000] selection:text-white">

    @if($devAnnouncement)
        <div
            class="fixed top-0 left-0 right-0 z-50 w-full text-white font-heading shadow-sm"
            style="@if($devAnnouncement->type === 'down') background-color: #991b1b; @elseif($devAnnouncement->type === '404_issue') background-color: #c2410c; @elseif($devAnnouncement->type === '402_issue') background-color: #b45309; @elseif($devAnnouncement->type === 'maintenance') background-color: #800000; @else background-color: #1e3a8a; @endif"
        >
            <div class="w-full px-4 sm:px-6 py-2.5 flex items-center justify-center text-center text-[11px] md:text-xs gap-2">
                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-extrabold tracking-wider bg-white/20 border border-white/30 text-white shrink-0 inline-flex items-center gap-1">
                    @if($devAnnouncement->type === 'maintenance')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 012 2v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h1.586A2 2 0 0111 4zM4 14h16m-2 0v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4"/></svg>
                        <span>Maintenance</span>
                    @elseif($devAnnouncement->type === 'down')
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Outage</span>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Notice</span>
                    @endif
                </span>
                <span class="text-white font-heading break-words"><strong class="font-extrabold">{{ $devAnnouncement->title }}:</strong> <span class="text-white/95 font-medium">{{ $devAnnouncement->message }}</span></span>
            </div>
        </div>
    @endif

    <div class="w-full max-w-lg mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-6 @if($devAnnouncement) pt-16 sm:pt-20 @endif">

        <!-- Maroon Header Banner -->
        <div class="rounded-3xl p-6 md:p-7 text-white shadow-sm relative overflow-hidden" style="background-color: #800000;" data-gsap="header-reveal">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-white p-1.5 border border-white/20 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="/images/pwu-logo.png" alt="DEMO Seal" class="w-full h-full object-contain" />
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-base font-extrabold text-white font-heading leading-tight">Developer Console</h1>
                        <p class="text-xs text-red-100 font-medium font-heading">Quick Auth & Environment Testing</p>
                    </div>
                </div>
                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/20 border border-white/30 text-white font-heading">
                    Demo Mode
                </span>
            </div>
            <p class="text-[11px] text-red-100 font-medium leading-relaxed mt-3 font-heading">Demo logins for QA only. Gated by <span class="font-mono font-bold text-white">APP_DEMO_MODE=true</span>. Matches DEMO institutional palette.</p>
        </div>

        <!-- Environment Info -->
        <div class="bg-white border border-gray-200 rounded-2xl p-4 space-y-0 divide-y divide-gray-100 shadow-sm">
            <div class="flex items-center justify-between py-2.5 first:pt-0">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500 font-heading">APP_ENV</span>
                <span class="text-xs font-mono font-bold text-gray-900 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-lg">{{ app()->environment() }}</span>
            </div>
            <div class="flex items-center justify-between py-2.5">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500 font-heading">APP_DEMO_MODE</span>
                <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-lg">true</span>
            </div>
            <div class="flex items-center justify-between py-2.5 last:pb-0">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500 font-heading">DATABASE</span>
                <span class="text-xs font-mono font-bold text-gray-900 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-lg">{{ config('database.default') }}</span>
            </div>
        </div>

        <!-- Quick Logins -->
        <div class="space-y-3" data-gsap="stagger-cards">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-extrabold uppercase tracking-widest text-gray-500 font-heading">Select Test Persona</h2>
                <span class="text-[11px] text-gray-400 font-medium font-heading">Auto-authenticates</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 font-heading">

                <!-- Ana (Student) -->
                <a href="/auth/dev-login/student" class="p-3.5 bg-white border border-gray-200 rounded-2xl flex items-center gap-3 hover:border-red-300 hover:bg-red-50/40 transition-colors group shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-red-50 border border-red-200 text-[#800000] font-extrabold text-xs flex items-center justify-center shrink-0 font-heading">ST</div>
                    <div class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-bold text-gray-900 truncate font-heading">Demo Student</p>
                        <span class="text-[10px] text-gray-500 font-medium block font-heading">Student Role</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#800000] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- Demo Secretary -->
                <a href="/auth/dev-login/secretary" class="p-3.5 bg-white border border-gray-200 rounded-2xl flex items-center gap-3 hover:border-red-300 hover:bg-red-50/40 transition-colors group shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-red-50 border border-red-200 text-[#800000] font-extrabold text-xs flex items-center justify-center shrink-0 font-heading">SC</div>
                    <div class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-bold text-gray-900 truncate font-heading">Demo Secretary</p>
                        <span class="text-[10px] text-gray-500 font-medium block font-heading">ERB Secretary</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#800000] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- Demo Reviewer (Committee Reviewer) -->
                <a href="/auth/dev-login/committee" class="p-3.5 bg-white border border-gray-200 rounded-2xl flex items-center gap-3 hover:border-red-300 hover:bg-red-50/40 transition-colors group shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-red-50 border border-red-200 text-[#800000] font-extrabold text-xs flex items-center justify-center shrink-0 font-heading">RV</div>
                    <div class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-bold text-gray-900 truncate font-heading">Demo Reviewer</p>
                        <span class="text-[10px] text-gray-500 font-medium block font-heading">Reviewer Role</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#800000] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- Demo Chair (Chairperson / Admin) -->
                <a href="/auth/dev-login/admin" class="p-3.5 bg-white border border-gray-200 rounded-2xl flex items-center gap-3 hover:border-red-300 hover:bg-red-50/40 transition-colors group shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-red-50 border border-red-200 text-[#800000] font-extrabold text-xs flex items-center justify-center shrink-0 font-heading">CH</div>
                    <div class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-bold text-gray-900 truncate font-heading">Demo Chair</p>
                        <span class="text-[10px] text-gray-500 font-medium block font-heading">Chair / Admin</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#800000] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                <!-- Developer Panel (Operations) -->
                <a href="/auth/dev-login/developer" class="p-3.5 bg-white border border-gray-200 rounded-2xl flex items-center gap-3 hover:border-red-300 hover:bg-red-50/40 transition-colors group shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-gray-900 border border-gray-800 text-white font-extrabold text-xs flex items-center justify-center shrink-0 font-heading">DV</div>
                    <div class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-bold text-gray-900 truncate font-heading">Dev Console</p>
                        <span class="text-[10px] text-gray-500 font-medium block font-heading">Developer Ops</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-gray-900 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

            </div>
            <p class="text-[11px] text-gray-400 font-medium font-heading leading-relaxed">Each card signs you in instantly as that persona and redirects to <span class="font-mono font-bold text-gray-600">/dashboard</span>. No password required in demo.</p>
        </div>

        <!-- Footer -->
        <div class="pt-3 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500 font-heading">
            <a href="/" class="inline-flex items-center gap-1.5 font-bold hover:text-[#800000] transition-colors" style="color: #800000;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Official Sign In</span>
            </a>
            <span class="text-[10px] text-gray-400 font-mono">/developer</span>
        </div>

        <p class="text-center text-[11px] text-gray-400 font-medium font-heading">Example Research Institution — Ethics Review Board · Institutional portal</p>

    </div>

</body>
</html>
