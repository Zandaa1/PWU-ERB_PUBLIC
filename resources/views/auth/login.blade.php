<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Official Example Research Institution (DEMO) Ethics Review Board Portal — Submit, track, and manage thesis research ethics review clearance applications.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    <title>DEMO Ethics Review Board | Portal Sign In</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="image" href="/images/Main-Gate.webp" type="image/webp">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $loginAnnouncement = \App\Models\SystemAnnouncement::getActive();
    @endphp
</head>
<body class="min-h-screen w-full font-heading bg-[#F8F7F6] text-neutral-900 antialiased overflow-x-hidden selection:bg-[#800000] selection:text-white relative">

    <!-- Site-Wide System Announcement Banner (Animated Top to Bottom) -->
    @if($loginAnnouncement)
        <div 
            class="fixed top-0 left-0 right-0 z-50 w-full text-white font-heading shadow-md transition-all duration-300 animate-slide-down"
            style="
                @if($loginAnnouncement->type === 'down') background-color: #991b1b;
                @elseif($loginAnnouncement->type === '404_issue') background-color: #c2410c;
                @elseif($loginAnnouncement->type === '402_issue') background-color: #b45309;
                @elseif($loginAnnouncement->type === 'maintenance') background-color: #800000;
                @else background-color: #1e3a8a;
                @endif
            "
        >
            <div class="w-full px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3 flex items-center justify-center text-center text-[11px] md:text-xs">
                <div class="w-full flex flex-wrap items-center justify-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[9px] uppercase font-extrabold tracking-wider bg-white/20 border border-white/30 text-white shrink-0 font-heading inline-flex items-center gap-1">
                        @if($loginAnnouncement->type === 'maintenance')
                            <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 012 2v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h1.586A2 2 0 0111 4zM4 14h16m-2 0v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4"/></svg>
                            <span>Maintenance</span>
                        @elseif($loginAnnouncement->type === 'down')
                            <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Outage</span>
                        @elseif($loginAnnouncement->type === '404_issue')
                            <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>404 Warning</span>
                        @elseif($loginAnnouncement->type === '402_issue')
                            <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Fee Issue</span>
                        @else
                            <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Notice</span>
                        @endif
                    </span>
                    <span class="text-[11px] md:text-xs leading-snug text-white font-heading break-words">
                        <strong class="font-extrabold text-white font-heading">{{ $loginAnnouncement->title }}:</strong>
                        <span class="text-white/95 font-medium font-heading whitespace-normal">{{ $loginAnnouncement->message }}</span>
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- SECTION 1: FULLSCREEN 360 DEMO PHOTO HERO WITH ETHICS BOARD PORTAL & QUICK GOOGLE SIGN IN -->
    <section id="hero" class="relative min-h-screen w-full flex flex-col items-center justify-between p-4 sm:p-6 lg:p-8 overflow-hidden text-white font-heading @if($loginAnnouncement) pt-20 sm:pt-24 @endif">
        
        <!-- Fullscreen 360 Hardware-Accelerated Panorama Layer -->
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <!-- Deep DEMO Maroon Vignette -->
            <div class="absolute inset-0 z-10" style="background: linear-gradient(135deg, rgba(128, 0, 0, 0.88) 0%, rgba(35, 0, 0, 0.95) 100%);"></div>
            
            <!-- 60FPS Rotating 360 Panorama -->
            <div 
                class="absolute top-0 bottom-0 left-0 w-[200%] h-full bg-cover bg-center animate-panorama-spin"
                style="background-image: image-set(url('/images/Main-Gate.webp') type('image/webp'), url('/images/Main-Gate.jpg') type('image/jpeg'));"
            ></div>
        </div>

        <!-- Top Spacer -->
        <div class="w-full max-w-5xl flex items-center justify-between py-2"></div>

        <!-- Center Content Box: Seal, Titles, and Quick Google Sign-In -->
        <div class="w-full max-w-lg mx-auto text-center space-y-6 relative z-20 py-4" data-gsap="hero-reveal">
            
            <!-- University Seal Medallion -->
            <div class="flex justify-center">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white p-1.5 border-4 border-white/30 shadow-2xl flex items-center justify-center">
                    <img src="/images/pwu-logo.png" alt="Example Research Institution Seal" class="w-full h-full object-contain" />
                </div>
            </div>

            <!-- Portal Title -->
            <div class="space-y-1.5">
                <span class="text-xs font-bold uppercase tracking-widest text-red-200 block font-heading">
                    Example Research Institution
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white font-heading tracking-tight leading-tight">
                    Ethics Review Board Portal
                </h1>
                <p class="text-xs sm:text-sm text-red-100 font-medium max-w-md mx-auto leading-relaxed">
                    Institutional Thesis & Research Ethics Clearance
                </p>
            </div>

            <!-- Inline Error Notification -->
            @if(session('error') || request('error'))
                <div class="p-3.5 bg-white/10 backdrop-blur-md border border-red-200/30 rounded-2xl text-xs text-white text-left font-semibold font-heading flex items-start gap-2.5 shadow-md">
                    <svg class="w-4 h-4 text-red-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') ?? (request('error') === 'unauthorized_domain' ? 'Please use your school Google account (@example.test).' : 'An authentication error occurred. Please try again.') }}</span>
                </div>
            @endif

            <!-- Quick Sign In with Google -->
            <div class="space-y-3 pt-2">
                <a 
                    href="/auth/google" 
                    class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 bg-white text-neutral-900 font-extrabold text-sm sm:text-base rounded-2xl shadow-xl hover:bg-red-50 hover:scale-[1.02] active:scale-[0.98] transition-all font-heading group"
                    style="color: #800000;"
                >
                    <svg class="w-5 h-5 shrink-0 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Sign in with Google</span>
                </a>

                <p class="text-xs text-red-100 font-semibold font-heading">
                    Use your official <strong class="text-white font-bold">@example.test</strong> email
                </p>

                <div class="flex items-center justify-center gap-3 text-[11px] text-red-200/90 pt-1 font-medium">
                    <a href="/privacy-policy" class="hover:text-white underline transition-colors">Privacy Policy</a>
                    <span>&bull;</span>
                    <a href="/terms" class="hover:text-white underline transition-colors">Terms of Service</a>
                    <span>&bull;</span>
                    <a href="/faq" class="hover:text-white underline transition-colors">Guidelines</a>
                </div>
            </div>

        </div>

        <!-- Bottom Scroll-Down Indicator to Steps -->
        <div class="py-4 relative z-20">
            <a href="#steps" class="inline-flex flex-col items-center gap-1 text-white/75 hover:text-white text-xs font-bold transition-colors font-heading group">
                <span class="group-hover:translate-y-0.5 transition-transform">How the Review Works</span>
                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </a>
        </div>
    </section>

    <!-- SECTION 2: STUDENT PORTAL WALKTHROUGH WITH SCROLL REVEAL & LIVE CURSOR ENGINE -->
    <section id="steps" class="bg-[#F8F7F6] text-neutral-900 py-16 sm:py-24 px-4 sm:px-6 lg:px-8 border-t border-gray-200 font-heading">
        <div id="demo-reveal-container" class="max-w-6xl mx-auto space-y-8 opacity-0 translate-y-12 transition-all duration-700 ease-out">
            
            <!-- Section Header -->
            <div class="text-center space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-red-900 bg-red-50 px-3 py-1 rounded-full border border-red-200 inline-block font-heading" style="color: #800000;">
                    Clearance Workflow
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 font-heading">
                    Ethics Review & Clearance Process
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 font-medium max-w-xl mx-auto">
                    A guided overview of the institutional ethics review procedure, from protocol submission to certificate issuance.
                </p>
            </div>

            <!-- Step Navigation Tabs (1: Submission, 2: Review, 3: Revisions, 4: Clearance Certificate) - Non-interactive Timeline Display -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 font-heading pointer-events-none select-none" id="demo-tabs-container">
                
                <!-- Tab 1: Protocol Submission -->
                <div 
                    id="demo-tab-1" 
                    class="demo-step-tab relative p-4 rounded-2xl border text-left transition-all bg-white border-gray-200 shadow-xs overflow-hidden"
                >
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="demo-step-badge w-6 h-6 rounded-full text-white font-extrabold text-[11px] flex items-center justify-center shrink-0 transition-colors" style="background-color: #800000;">
                            1
                        </span>
                        <span class="text-xs font-extrabold text-gray-900 font-heading">Protocol Submission</span>
                    </div>
                    <p class="text-[11px] text-gray-500 font-medium">Upload required documents</p>
                    <div class="demo-progress-bar absolute bottom-0 left-0 right-0 h-1 bg-red-100 overflow-hidden">
                        <div class="demo-progress-fill h-full w-0 transition-all" style="background-color: #800000;"></div>
                    </div>
                </div>

                <!-- Tab 2: Committee Review & Replying to Comments -->
                <div 
                    id="demo-tab-2" 
                    class="demo-step-tab relative p-4 rounded-2xl border text-left transition-all bg-white border-gray-200 shadow-xs overflow-hidden opacity-70"
                >
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="demo-step-badge w-6 h-6 rounded-full bg-gray-200 text-gray-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 transition-colors">
                            2
                        </span>
                        <span class="text-xs font-extrabold text-gray-900 font-heading">Committee Review</span>
                    </div>
                    <p class="text-[11px] text-gray-500 font-medium">Address reviewer feedback</p>
                    <div class="demo-progress-bar absolute bottom-0 left-0 right-0 h-1 bg-red-100 overflow-hidden">
                        <div class="demo-progress-fill h-full w-0 transition-all" style="background-color: #800000;"></div>
                    </div>
                </div>

                <!-- Tab 3: Document Revision -->
                <div 
                    id="demo-tab-3" 
                    class="demo-step-tab relative p-4 rounded-2xl border text-left transition-all bg-white border-gray-200 shadow-xs overflow-hidden opacity-70"
                >
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="demo-step-badge w-6 h-6 rounded-full bg-gray-200 text-gray-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 transition-colors">
                            3
                        </span>
                        <span class="text-xs font-extrabold text-gray-900 font-heading">Document Revision</span>
                    </div>
                    <p class="text-[11px] text-gray-500 font-medium">Submit revised documents</p>
                    <div class="demo-progress-bar absolute bottom-0 left-0 right-0 h-1 bg-red-100 overflow-hidden">
                        <div class="demo-progress-fill h-full w-0 transition-all" style="background-color: #800000;"></div>
                    </div>
                </div>

                <!-- Tab 4: Clearance Certificate -->
                <div 
                    id="demo-tab-4" 
                    class="demo-step-tab relative p-4 rounded-2xl border text-left transition-all bg-white border-gray-200 shadow-xs overflow-hidden opacity-70"
                >
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="demo-step-badge w-6 h-6 rounded-full bg-gray-200 text-gray-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 transition-colors">
                            4
                        </span>
                        <span class="text-xs font-extrabold text-gray-900 font-heading">Ethical Clearance</span>
                    </div>
                    <p class="text-[11px] text-gray-500 font-medium">Download signed certificate</p>
                    <div class="demo-progress-bar absolute bottom-0 left-0 right-0 h-1 bg-red-100 overflow-hidden">
                        <div class="demo-progress-fill h-full w-0 transition-all" style="background-color: #800000;"></div>
                    </div>
                </div>

            </div>

            <!-- AUTHENTIC STUDENT PORTAL WINDOW (FIXED HEIGHT, NON-INTERACTIVE VIEW-ONLY) -->
            <div 
                id="demo-video-frame"
                class="bg-white border-2 border-gray-300 rounded-3xl shadow-xl overflow-hidden relative font-heading pointer-events-none select-none"
            >
                
                <!-- Browser Chrome Header Bar -->
                <div class="bg-gray-100/90 border-b border-gray-200 px-4 py-2.5 flex items-center justify-between gap-4">
                    <!-- Traffic light dots -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>

                    <!-- Simulated Browser URL Bar -->
                    <div class="flex-1 max-w-lg bg-white border border-gray-200 rounded-xl px-3 py-1 text-center flex items-center justify-center gap-2 text-[11px] text-gray-500 shadow-2xs font-mono">
                        <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span id="demo-url-text" class="text-gray-700 font-semibold">https://erb.example.test/requests/create</span>
                    </div>

                    <!-- Live Demo Video Indicator -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                        <span class="text-[10px] font-extrabold text-red-900 uppercase tracking-wider font-heading">SYSTEM PREVIEW</span>
                    </div>
                </div>

                <!-- Complete Authentic Portal Split: Student Sidebar on Left, Workspace on Right (FIXED CONTAINER HEIGHT) -->
                <div class="flex h-[490px] max-h-[490px] bg-neutral-50 relative overflow-hidden" id="demo-screen-canvas">

                    <!-- PRECISE ANIMATED MOUSE CURSOR -->
                    <div 
                        id="demo-cursor" 
                        class="absolute z-50 pointer-events-none transition-all duration-700 cubic-bezier(0.25, 1, 0.5, 1) flex items-start"
                        style="top: 0px; left: 0px; transform: translate3d(240px, 140px, 0);"
                    >
                        <!-- High-resolution SVG Cursor Pointer -->
                        <svg class="w-6 h-6 text-gray-900 drop-shadow-[0_4px_8px_rgba(0,0,0,0.35)] filter" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 2l16 11-7.5 1.5 4.5 7.5-3 1.5-4.5-7.5L4 20V2z" fill="#111827" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round"/>
                        </svg>

                        <!-- Ripple Click Pulse Ring -->
                        <div id="demo-cursor-ripple" class="absolute -top-3 -left-3 w-12 h-12 rounded-full border-2 border-red-500 opacity-0 scale-50 transition-all duration-300 pointer-events-none"></div>
                    </div>

                    <!-- 1. AUTHENTIC STUDENT LEFT SIDEBAR -->
                    <aside class="w-52 sm:w-60 bg-white border-r border-gray-200 flex-col justify-between shrink-0 hidden sm:flex font-heading h-full">
                        <div>
                            <!-- Branding Header -->
                            <div class="p-4 border-b border-gray-100 flex items-center gap-2.5">
                                <img src="/images/pwu-logo.png" alt="DEMO Logo" class="w-8 h-8 object-contain shrink-0" />
                                <div>
                                    <p class="text-xs font-bold leading-tight font-heading" style="color: #800000;">DEMO ERB Portal</p>
                                    <p class="text-[10px] text-gray-500 font-medium">Ethics Review Board</p>
                                </div>
                            </div>

                            <!-- Student Nav Menu Items -->
                            <nav class="p-3 space-y-1 text-xs">
                                <div id="demo-nav-requests" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-600 font-semibold transition-all">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>My Requests</span>
                                </div>
                                <div id="demo-nav-create" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-600 font-semibold transition-all" style="color: #800000; background-color: #FEF2F2; border-left: 3px solid #800000;">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Submit Request</span>
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-gray-600 font-semibold">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>FAQ & Guidelines</span>
                                </div>
                            </nav>
                        </div>

                        <!-- Authenticated Student Persona Profile Footer -->
                        <div class="p-3 border-t border-gray-100 flex items-center gap-2.5 text-xs">
                            <div class="w-8 h-8 rounded-full text-white font-bold text-[11px] flex items-center justify-center shrink-0" style="background-color: #800000;">
                                AS
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-gray-900">Demo Student</p>
                                <p class="text-[10px] text-gray-500 font-medium">Student (Pharmacy)</p>
                            </div>
                        </div>
                    </aside>

                    <!-- 2. STUDENT WORKSPACE CONTENT AREA -->
                    <main class="flex-1 flex flex-col min-w-0 bg-neutral-50 overflow-hidden font-heading h-full">
                        
                        <!-- Top Workspace Header Bar -->
                        <header class="h-12 bg-white border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-bold text-gray-800">Student Portal</span>
                                <span class="text-gray-300">/</span>
                                <span id="demo-top-crumb" class="text-gray-500 font-medium">New Protocol Submission</span>
                            </div>

                            <span class="text-[10px] font-bold text-red-950 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full" style="color: #800000;">
                                Ethics Review Application
                            </span>
                        </header>

                        <!-- Inner Views Canvas (ABSOLUTELY POSITIONED FOR STABLE FIXED HEIGHT) -->
                        <div class="relative flex-1 h-full overflow-hidden">
                            
                            <!-- STEP 1: STUDENT INITIAL SUBMISSION WITH INTERACTIVE UPLOAD ACTION -->
                            <div id="demo-panel-1" class="demo-panel absolute inset-0 p-4 sm:p-5 overflow-y-auto space-y-3.5 transition-all duration-500 ease-in-out opacity-100">
                                <!-- DEMO Red Box Header -->
                                <div class="rounded-2xl p-4 sm:p-5 text-white flex items-center justify-between shadow-xs" style="background-color: #800000;">
                                    <div>
                                        <h3 class="text-sm sm:text-base font-extrabold text-white font-heading">New Research Ethics Application</h3>
                                        <p class="text-[11px] sm:text-xs text-red-100 font-medium mt-0.5">Provide study protocol details and upload mandatory documentation for committee review.</p>
                                    </div>
                                    <span class="px-2 py-0.5 bg-white/20 text-white font-bold text-xs rounded-lg">Draft</span>
                                </div>

                                <!-- Form Card Container -->
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 space-y-3.5 shadow-xs">
                                    <div class="border-b border-gray-100 pb-2.5">
                                        <span class="text-[11px] font-extrabold uppercase text-gray-700" style="color: #800000;">1. Research Study Title</span>
                                        <div class="mt-1 p-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-800">
                                            Public Perception and Willingness to Pay for Medication Therapy Management (MTM) Services in Community Pharmacies
                                        </div>
                                    </div>

                                    <!-- Required Documents Section -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold uppercase text-gray-700" style="color: #800000;">2. Required Protocol Documents</span>
                                            <span id="target-s1-counter" class="text-[11px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200 transition-all">6 of 7 Files Uploaded</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                            <!-- INTERACTIVE TARGET SLOT -->
                                            <div id="target-s1-upload" class="p-2.5 bg-amber-50/50 border-2 border-dashed border-amber-300 rounded-xl flex items-center justify-between transition-all duration-300">
                                                <div class="flex items-center gap-2">
                                                    <div id="target-s1-icon" class="w-6 h-6 rounded-lg bg-amber-200 text-amber-900 font-bold flex items-center justify-center text-[10px]">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    </div>
                                                    <div>
                                                        <p id="target-s1-title" class="font-bold text-gray-900">1. Research Protocol</p>
                                                        <p id="target-s1-sub" class="text-[10px] text-amber-700 font-medium">Click to select file...</p>
                                                    </div>
                                                </div>
                                                <span id="target-s1-badge" class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Mandatory</span>
                                            </div>

                                            <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
                                                <span class="font-bold text-gray-900">2. Informed Consent Form</span>
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Uploaded (PDF)</span>
                                            </div>
                                            <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
                                                <span class="font-bold text-gray-900">3. Data Collection Tools</span>
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Uploaded (PDF)</span>
                                            </div>
                                            <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
                                                <span class="font-bold text-gray-900">4. CV & Certifications</span>
                                                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">Uploaded (PDF)</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Action Button -->
                                    <div class="pt-1 flex justify-end">
                                        <button id="target-s1-btn" class="px-5 py-2.5 text-white font-extrabold text-xs rounded-xl shadow-xs transition-transform duration-200 active:scale-95 inline-flex items-center gap-1.5" style="background-color: #800000;">
                                            <span>Submit Application for Review</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: STUDENT VIEWING FEEDBACK & REPLYING TO COMMENTS (/requests/1/documents/2) -->
                            <div id="demo-panel-2" class="demo-panel absolute inset-0 p-4 sm:p-5 overflow-y-auto space-y-3.5 transition-all duration-500 ease-in-out opacity-0 hidden">
                                <!-- Top Request Header -->
                                <div class="bg-white border border-gray-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-gray-600">ERB2026_00001</span>
                                        <span class="text-gray-300">|</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-gray-900 font-heading">Informed Consent Form &bull; Version 1</h3>
                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-lg border border-amber-200">Revisions Requested</span>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-500">1 Review Comment</span>
                                </div>

                                <!-- Reviewer Feedback & Student Reply Thread -->
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 space-y-3.5 shadow-xs text-xs">
                                    <!-- Reviewer Comment Bubble -->
                                    <div class="p-3 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded-full bg-amber-700 text-white font-bold text-[9px] flex items-center justify-center">DR</div>
                                                <span class="font-bold text-amber-950">Demo Reviewer (Committee Reviewer)</span>
                                            </div>
                                            <span class="text-[10px] font-mono text-amber-800">Section 3: Confidentiality</span>
                                        </div>
                                        <p class="text-gray-800 leading-relaxed pl-7 text-[11px]">
                                            "Please clarify participant confidentiality protocols in Section 3 and include the institutional ERB contact information."
                                        </p>
                                    </div>

                                    <!-- Student Reply Box -->
                                    <div class="space-y-2 border-t border-gray-100 pt-2.5">
                                        <label class="block font-bold text-gray-700 text-[11px]">Proponent Response to Reviewer Feedback:</label>
                                        <div id="target-s2-commentbox" class="p-2.5 bg-gray-50 border border-gray-300 rounded-xl text-gray-800 transition-all font-medium text-[11px] leading-relaxed">
                                            "Acknowledged. The institutional ERB contact details have been included in the consent statement, and Section 3 participant confidentiality safeguards have been revised as requested."
                                        </div>
                                        
                                        <div class="flex justify-end pt-1">
                                            <button id="target-s2-replybtn" class="px-4 py-2 text-white font-extrabold text-xs rounded-xl shadow-xs transition-transform duration-200 active:scale-95 inline-flex items-center gap-1.5" style="background-color: #800000;">
                                                <span>Submit Response to Reviewer</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: STUDENT SUBMITTING DOCUMENT REVISION (/requests/1/revisions) -->
                            <div id="demo-panel-3" class="demo-panel absolute inset-0 p-4 sm:p-5 overflow-y-auto space-y-3.5 transition-all duration-500 ease-in-out opacity-0 hidden">
                                <!-- Red Box Status Header -->
                                <div class="rounded-2xl p-4 sm:p-5 text-white flex items-center justify-between shadow-xs" style="background-color: #800000;">
                                    <div>
                                        <span class="text-xs text-red-200 uppercase tracking-widest font-mono">ERB2026_00001</span>
                                        <h3 class="text-sm sm:text-base font-extrabold text-white font-heading">Submit Document Revision</h3>
                                    </div>
                                    <span class="px-2 py-0.5 bg-white/20 text-white font-bold text-xs rounded-lg">Document Amendment</span>
                                </div>

                                <!-- Revision Upload Container -->
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 space-y-3.5 shadow-xs text-xs">
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                                        <div>
                                            <p class="font-bold text-gray-900 font-heading text-xs sm:text-sm">Target Document: Informed Consent Form</p>
                                            <p class="text-gray-500 text-[11px]">Submitting revised protocol addressing reviewer remarks</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-lg">Revision Pending</span>
                                    </div>

                                    <!-- Dropzone with Revised File -->
                                    <div id="target-s3-dropzone" class="p-3.5 border-2 border-dashed border-emerald-400 bg-emerald-50/50 rounded-2xl flex items-center justify-between transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">PDF</div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-xs">Informed_Consent_Form_Revised.pdf</p>
                                                <p class="text-gray-500 text-[11px]">1.4 MB &bull; Resolution Notes Included</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200">
                                            Ready for Submission
                                        </span>
                                    </div>

                                    <!-- Submit Revision Button -->
                                    <div class="flex justify-end pt-1">
                                        <button id="target-s3-uploadbtn" class="px-5 py-2.5 text-white font-extrabold text-xs rounded-xl shadow-xs transition-transform duration-200 active:scale-95 inline-flex items-center gap-1.5" style="background-color: #800000;">
                                            <span>Upload & Submit Revision</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 4: STUDENT VIEWING SCROLLED APPROVED DOCUMENT WITH SIGNATURE & DOWNLOAD -->
                            <div id="demo-panel-4" class="demo-panel absolute inset-0 p-4 sm:p-5 overflow-y-auto space-y-3.5 transition-all duration-500 ease-in-out opacity-0 hidden">
                                <!-- Top Status Bar -->
                                <div class="bg-white border border-gray-200 rounded-2xl p-3.5 flex items-center justify-between shadow-xs">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Status: Approved</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-gray-900 mt-0.5 font-heading">Ethical Clearance Certificate Issued</h3>
                                    </div>
                                    <button id="target-s4-downloadbtn" class="px-3.5 py-2 text-white font-extrabold text-xs rounded-xl shadow-xs transition-transform duration-200 flex items-center gap-1.5 shrink-0" style="background-color: #800000;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Download PDF</span>
                                    </button>
                                </div>

                                <!-- Scrollable Simulated Official Clearance Document -->
                                <div 
                                    id="demo-cert-scrollable" 
                                    class="bg-white border-2 border-red-200 rounded-2xl p-4 sm:p-5 max-h-[260px] overflow-y-auto space-y-4 shadow-inner font-heading relative scroll-smooth text-center"
                                >
                                    <!-- Document Top: DEMO Crest & Official Header -->
                                    <div class="space-y-1.5 border-b border-gray-100 pb-3">
                                        <div class="flex justify-center">
                                            <div class="w-10 h-10 rounded-full bg-white p-1 border border-red-100 flex items-center justify-center">
                                                <img src="/images/pwu-logo.png" alt="DEMO Seal" class="w-full h-full object-contain" />
                                            </div>
                                        </div>
                                        <p class="text-[9px] font-bold uppercase tracking-widest text-red-900" style="color: #800000;">EXAMPLE RESEARCH INSTITUTION</p>
                                        <p class="text-xs sm:text-sm font-extrabold text-gray-900 font-heading">ETHICS REVIEW BOARD CLEARANCE CERTIFICATE</p>
                                        <span class="inline-block font-mono text-[10px] font-extrabold bg-red-50 text-red-950 px-2 py-0.5 rounded border border-red-200">
                                            CONTROL NO: ERB2026_00001
                                        </span>
                                    </div>

                                    <!-- Document Body: Protocol Information -->
                                    <div class="text-[11px] text-gray-700 space-y-1.5 max-w-lg mx-auto text-left bg-gray-50/70 p-3 rounded-xl border border-gray-200 leading-relaxed">
                                        <p><strong>Principal Investigator:</strong> Demo Student (School of Pharmacy)</p>
                                        <p><strong>Study Protocol Title:</strong> Public Perception and Willingness to Pay for Medication Therapy Management (MTM) Services in Community Pharmacies</p>
                                        <p><strong>Review Classification:</strong> Full Institutional Ethics Clearance</p>
                                        <p class="text-[10px] text-gray-600 pt-0.5">
                                            This certifies that the thesis protocol has satisfied all institutional human participant protection guidelines under RA 10173 and DEMO ERB standard operating procedures.
                                        </p>
                                    </div>

                                    <!-- Document Bottom (Revealed on Smooth Scroll): Live Signature Block -->
                                    <div id="target-s4-signatureblock" class="pt-3 border-t-2 border-dashed border-gray-200 space-y-2">
                                        <p class="text-[11px] font-bold text-gray-700">Official Institutional Sign-Off</p>
                                        
                                        <div class="flex flex-col items-center justify-center">
                                            <!-- Ink Signature -->
                                            <div class="font-serif italic font-extrabold text-sm text-red-950 border-b-2 border-gray-900 px-5 pb-0.5" style="color: #800000;">
                                                Demo Chair, PhD
                                            </div>
                                            <p class="text-[10px] font-bold text-gray-800 mt-1">Demo Chair, PhD</p>
                                            <p class="text-[9px] text-gray-500">Chairperson, Ethics Review Board</p>
                                        </div>

                                        <div class="flex items-center justify-center gap-1.5 text-[9px] font-mono text-emerald-800 bg-emerald-50 border border-emerald-200 py-0.5 px-2.5 rounded-full w-max mx-auto">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>DIGITALLY VERIFIED CLEARANCE &bull; ERB2026_00001</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </main>

                </div>

            </div>

            <!-- Return to Top / Sign In Action -->
            <div class="text-center pt-2">
                <a 
                    href="#hero" 
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-xs text-white transition-all shadow-xs hover:opacity-95"
                    style="background-color: #800000;"
                >
                    <span>Back to Top & Sign In</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </a>
            </div>

        </div>
    </section>

    <!-- University Institutional Footer -->
    <footer class="w-full bg-neutral-900 text-neutral-400 py-10 px-4 sm:px-6 lg:px-8 border-t border-neutral-800 font-heading text-xs">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div class="flex items-center gap-3">
                <img src="/images/pwu-logo.png" alt="DEMO Seal" class="w-9 h-9 object-contain shrink-0" />
                <div class="text-left">
                    <p class="font-extrabold text-white text-sm tracking-tight">Example Research Institution</p>
                    <p class="text-[11px] text-neutral-400">Ethics Review Board (ERB) Online Portal</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-semibold">
                <a href="/privacy-policy" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="/terms" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="/faq" class="hover:text-white transition-colors">Submission Guidelines</a>
                <a href="mailto:erb@example.test" class="hover:text-white transition-colors">Contact ERB</a>
            </div>

            <div class="text-neutral-500 text-[11px]">
                &copy; {{ date('Y') }} Example Research Institution.<br class="hidden sm:inline"> All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Precise Animated Cursor, Student Step Cross-Fade, and Document Scroll Engine -->
    <script>
        (function() {
            var currentStep = 1;
            var totalSteps = 4;
            var stepDuration = 5200; // 5.2s per scene for realistic reading and scroll
            var progressStartTime = Date.now();
            var progressInterval = null;

            var urls = [
                'https://erb.example.test/requests/create',
                'https://erb.example.test/requests/ERB2026_00001/documents/2',
                'https://erb.example.test/requests/ERB2026_00001/revisions',
                'https://erb.example.test/requests/ERB2026_00001/certificate'
            ];

            var crumbs = [
                'New Protocol Submission',
                'Committee Review & Feedback',
                'Submit Document Revision',
                'Ethical Clearance Certificate'
            ];

            // Sequence of precise target DOM IDs to move to and click per student step
            var targetSequences = {
                1: [
                    { id: 'target-s1-upload', delay: 600, click: true, onAction: triggerUploadAttach },
                    { id: 'target-s1-btn', delay: 2800, click: true }
                ],
                2: [
                    { id: 'target-s2-commentbox', delay: 700, click: true },
                    { id: 'target-s2-replybtn', delay: 2600, click: true }
                ],
                3: [
                    { id: 'target-s3-dropzone', delay: 700, click: true },
                    { id: 'target-s3-uploadbtn', delay: 2600, click: true }
                ],
                4: [
                    { id: 'target-s4-signatureblock', delay: 1800, click: false },
                    { id: 'target-s4-downloadbtn', delay: 3800, click: true }
                ]
            };

            function triggerUploadAttach() {
                var slot = document.getElementById('target-s1-upload');
                var icon = document.getElementById('target-s1-icon');
                var title = document.getElementById('target-s1-title');
                var sub = document.getElementById('target-s1-sub');
                var badge = document.getElementById('target-s1-badge');
                var counter = document.getElementById('target-s1-counter');

                if (slot) {
                    slot.classList.remove('bg-amber-50/50', 'border-dashed', 'border-amber-300');
                    slot.classList.add('bg-emerald-50/70', 'border-solid', 'border-emerald-300');
                }
                if (icon) {
                    icon.classList.remove('bg-amber-200', 'text-amber-900');
                    icon.classList.add('bg-emerald-100', 'text-emerald-800');
                    icon.innerText = 'PDF';
                }
                if (title) {
                    title.innerText = 'Protocol_Manuscript_Final.pdf';
                }
                if (sub) {
                    sub.classList.remove('text-amber-700');
                    sub.classList.add('text-gray-500');
                    sub.innerText = '1.8 MB • Uploaded';
                }
                if (badge) {
                    badge.classList.remove('text-amber-800', 'bg-amber-100');
                    badge.classList.add('text-emerald-800', 'bg-emerald-100');
                    badge.innerText = 'Uploaded (PDF)';
                }
                if (counter) {
                    counter.classList.remove('text-amber-800', 'bg-amber-50', 'border-amber-200');
                    counter.classList.add('text-emerald-700', 'bg-emerald-50', 'border-emerald-200');
                    counter.innerText = '7 of 7 Files Uploaded';
                }
            }

            function resetUploadAttach() {
                var slot = document.getElementById('target-s1-upload');
                var icon = document.getElementById('target-s1-icon');
                var title = document.getElementById('target-s1-title');
                var sub = document.getElementById('target-s1-sub');
                var badge = document.getElementById('target-s1-badge');
                var counter = document.getElementById('target-s1-counter');

                if (slot) {
                    slot.classList.add('bg-amber-50/50', 'border-dashed', 'border-amber-300');
                    slot.classList.remove('bg-emerald-50/70', 'border-solid', 'border-emerald-300');
                }
                if (icon) {
                    icon.classList.add('bg-amber-200', 'text-amber-900');
                    icon.classList.remove('bg-emerald-100', 'text-emerald-800');
                    icon.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
                }
                if (title) {
                    title.innerText = '1. Research Protocol';
                }
                if (sub) {
                    sub.classList.add('text-amber-700');
                    sub.classList.remove('text-gray-500');
                    sub.innerText = 'Click to select file...';
                }
                if (badge) {
                    badge.classList.add('text-amber-800', 'bg-amber-100');
                    badge.classList.remove('text-emerald-800', 'bg-emerald-100');
                    badge.innerText = 'Mandatory';
                }
                if (counter) {
                    counter.classList.add('text-amber-800', 'bg-amber-50', 'border-amber-200');
                    counter.classList.remove('text-emerald-700', 'bg-emerald-50', 'border-emerald-200');
                    counter.innerText = '6 of 7 Files Uploaded';
                }
            }

            window.switchDemoStep = function(step) {
                currentStep = step;
                if (currentStep === 1) {
                    resetUploadAttach();
                }
                renderActiveStepWithFade();
                runCursorSequenceForStep(currentStep);
                if (currentStep === 4) {
                    triggerDocumentScroll();
                }
                progressStartTime = Date.now();
            };

            function triggerDocumentScroll() {
                var doc = document.getElementById('demo-cert-scrollable');
                if (!doc) return;
                doc.scrollTop = 0;
                setTimeout(function() {
                    if (currentStep !== 4) return;
                    doc.scrollTo({
                        top: doc.scrollHeight - doc.clientHeight,
                        behavior: 'smooth'
                    });
                }, 700);
            }

            function renderActiveStepWithFade() {
                var urlEl = document.getElementById('demo-url-text');
                if (urlEl) {
                    urlEl.innerText = urls[currentStep - 1];
                }

                var crumb = document.getElementById('demo-top-crumb');
                if (crumb) crumb.innerText = crumbs[currentStep - 1];

                var navReq = document.getElementById('demo-nav-requests');
                var navCreate = document.getElementById('demo-nav-create');

                if (navReq && navCreate) {
                    if (currentStep === 1) {
                        navCreate.style.color = '#800000';
                        navCreate.style.backgroundColor = '#FEF2F2';
                        navCreate.style.borderLeft = '3px solid #800000';
                        navReq.style.color = '#4B5563';
                        navReq.style.backgroundColor = 'transparent';
                        navReq.style.borderLeft = 'none';
                    } else {
                        navReq.style.color = '#800000';
                        navReq.style.backgroundColor = '#FEF2F2';
                        navReq.style.borderLeft = '3px solid #800000';
                        navCreate.style.color = '#4B5563';
                        navCreate.style.backgroundColor = 'transparent';
                        navCreate.style.borderLeft = 'none';
                    }
                }

                // Update tabs & cross-fade panels
                for (var i = 1; i <= totalSteps; i++) {
                    var tab = document.getElementById('demo-tab-' + i);
                    var panel = document.getElementById('demo-panel-' + i);
                    var badge = tab ? tab.querySelector('.demo-step-badge') : null;
                    var progressFill = tab ? tab.querySelector('.demo-progress-fill') : null;

                    if (i === currentStep) {
                        if (tab) {
                            tab.classList.remove('opacity-70');
                            tab.classList.add('border-red-900', 'shadow-md');
                            tab.style.borderColor = '#800000';
                        }
                        if (badge) {
                            badge.style.backgroundColor = '#800000';
                            badge.classList.remove('bg-gray-200', 'text-gray-700');
                            badge.classList.add('text-white');
                        }
                        if (panel) {
                            panel.classList.remove('hidden');
                            setTimeout((function(p) {
                                return function() {
                                    p.classList.remove('opacity-0');
                                    p.classList.add('opacity-100');
                                };
                            })(panel), 30);
                        }
                    } else {
                        if (tab) {
                            tab.classList.add('opacity-70');
                            tab.classList.remove('border-red-900', 'shadow-md');
                            tab.style.borderColor = '#e5e7eb';
                        }
                        if (badge) {
                            badge.style.backgroundColor = '#e5e7eb';
                            badge.classList.add('text-gray-700');
                            badge.classList.remove('text-white');
                        }
                        if (panel) {
                            panel.classList.remove('opacity-100');
                            panel.classList.add('opacity-0');
                            setTimeout((function(p) {
                                return function() {
                                    p.classList.add('hidden');
                                };
                            })(panel), 300);
                        }
                        if (progressFill) {
                            progressFill.style.width = '0%';
                        }
                    }
                }
            }

            function moveCursorToTarget(targetId, clickEffect, onAction) {
                var container = document.getElementById('demo-screen-canvas');
                var target = document.getElementById(targetId);
                var cursor = document.getElementById('demo-cursor');
                var ripple = document.getElementById('demo-cursor-ripple');
                if (!container || !target || !cursor) return;

                var containerRect = container.getBoundingClientRect();
                var targetRect = target.getBoundingClientRect();

                // Target center coordinates relative to canvas
                var targetX = (targetRect.left + targetRect.width / 2) - containerRect.left;
                var targetY = (targetRect.top + targetRect.height / 2) - containerRect.top;

                cursor.style.transform = 'translate3d(' + Math.round(targetX) + 'px, ' + Math.round(targetY) + 'px, 0)';

                if (clickEffect) {
                    setTimeout(function() {
                        target.style.transform = 'scale(0.96)';
                        if (ripple) {
                            ripple.style.opacity = '1';
                            ripple.style.transform = 'scale(1.4)';
                        }
                        setTimeout(function() {
                            target.style.transform = 'scale(1)';
                            if (ripple) {
                                ripple.style.opacity = '0';
                                ripple.style.transform = 'scale(0.5)';
                            }
                            if (onAction) {
                                onAction();
                            }
                        }, 250);
                    }, 450);
                }
            }

            function runCursorSequenceForStep(step) {
                var seq = targetSequences[step] || [];
                seq.forEach(function(item) {
                    setTimeout(function() {
                        if (currentStep !== step) return;
                        moveCursorToTarget(item.id, item.click, item.onAction);
                    }, item.delay);
                });
            }

            function startProgressLoop() {
                progressStartTime = Date.now();
                if (progressInterval) clearInterval(progressInterval);
                progressInterval = setInterval(function() {
                    var elapsed = Date.now() - progressStartTime;
                    var pct = Math.min(100, (elapsed / stepDuration) * 100);
                    var activeTab = document.getElementById('demo-tab-' + currentStep);
                    var fill = activeTab ? activeTab.querySelector('.demo-progress-fill') : null;
                    if (fill) {
                        fill.style.width = pct + '%';
                    }
                    if (elapsed >= stepDuration) {
                        currentStep = (currentStep % totalSteps) + 1;
                        if (currentStep === 1) {
                            resetUploadAttach();
                        }
                        renderActiveStepWithFade();
                        runCursorSequenceForStep(currentStep);
                        if (currentStep === 4) {
                            triggerDocumentScroll();
                        }
                        progressStartTime = Date.now();
                    }
                }, 50);
            }

            // Scroll Reveal & Intersection Observer Trigger
            var hasStarted = false;
            var stepsSection = document.getElementById('steps');
            var revealContainer = document.getElementById('demo-reveal-container');

            function startTourOnScroll() {
                if (hasStarted) return;
                hasStarted = true;

                if (revealContainer) {
                    revealContainer.classList.remove('opacity-0', 'translate-y-12');
                    revealContainer.classList.add('opacity-100', 'translate-y-0');
                }

                resetUploadAttach();
                renderActiveStepWithFade();
                setTimeout(function() {
                    runCursorSequenceForStep(1);
                }, 400);
                startProgressLoop();
            }

            if ('IntersectionObserver' in window && stepsSection) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            startTourOnScroll();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15 });

                observer.observe(stepsSection);
            } else {
                startTourOnScroll();
            }
        })();
    </script>

</body>
</html>
