<!DOCTYPE html>
<html lang="en" class="h-full bg-neutral-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | DEMO Ethics Review Board</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-between text-gray-900 bg-neutral-50 font-heading antialiased selection:bg-red-900 selection:text-white">

    <!-- Top Navigation Header Bar -->
    <header class="w-full bg-white border-b border-gray-200 py-3.5 px-4 sm:px-8 shrink-0">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                <img src="/images/pwu-logo.png" alt="Example Research Institution" class="w-8 h-8 object-contain" />
                <div class="text-left">
                    <span class="block text-xs font-extrabold text-gray-900 tracking-tight leading-none group-hover:text-red-900 transition-colors">Example Research Institution</span>
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5" style="color: #800000;">Ethics Review Board</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <a href="/faq" class="text-xs font-bold text-gray-600 hover:text-red-900 px-3 py-1.5 rounded-lg transition-colors">
                    Guidelines & FAQ
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Box -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 md:p-12">
        <div class="max-w-lg w-full bg-white border border-gray-200 rounded-3xl p-6 sm:p-10 shadow-xs text-center space-y-6 animate-header-glide">
            
            <!-- Status Badge / Icon Container -->
            <div class="mx-auto w-16 h-16 rounded-2xl flex items-center justify-center border shadow-xs" style="background-color: #FFF5F5; border-color: #FECACA; color: #800000;">
                @yield('icon')
            </div>

            <!-- Error Code & Headings -->
            <div class="space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full border font-mono" style="background-color: #F8F7F6; border-color: #E4E1DE; color: #800000;">
                    Error @yield('code')
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight mt-3">
                    @yield('heading')
                </h1>
                <p class="text-xs sm:text-sm text-gray-600 font-medium leading-relaxed max-w-sm mx-auto">
                    @yield('message')
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                @yield('actions')
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-gray-200 bg-white py-4 px-4 text-center text-xs text-gray-500 font-medium shrink-0">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Example Research Institution — Ethics Review Board</p>
            <p>Inquiries: <a href="mailto:erb@example.test" class="font-bold underline hover:text-red-900" style="color: #800000;">erb@example.test</a></p>
        </div>
    </footer>

</body>
</html>
