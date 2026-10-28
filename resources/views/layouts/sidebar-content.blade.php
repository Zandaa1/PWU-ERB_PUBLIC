<div>
    <!-- Branding Header -->
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="/images/pwu-logo.png" alt="DEMO Seal" class="w-10 h-10 object-contain shrink-0" />
            <div>
                <span class="font-bold text-sm leading-tight text-gray-900 block font-heading" style="color: #800000;">DEMO ERB Portal</span>
                <p class="text-xs text-gray-500 font-medium">Ethics Review Board</p>
            </div>
        </div>
        @if(!empty($isMobile))
            <button @click="mobileOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
        @endif
    </div>

    <!-- Role Nav Menu -->
    <nav class="p-4 space-y-1">
        @include('layouts.navigation')
    </nav>
</div>

<!-- User Footer / Profile -->
<div class="p-4 border-t border-gray-100">
    <div class="flex items-center gap-3 mb-3">
        <div class="relative w-9 h-9 shrink-0">
            @if(auth()->user()->avatar)
                <img 
                    src="{{ auth()->user()->avatar }}" 
                    alt="{{ auth()->user()->name }}" 
                    referrerpolicy="no-referrer"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');"
                    class="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-200 shadow-sm" 
                />
            @endif
            <div @class([
                'w-9 h-9 rounded-full bg-red-100 text-maroon border border-red-200 items-center justify-center font-bold text-xs shrink-0 select-none font-heading',
                'hidden' => !empty(auth()->user()->avatar),
                'flex' => empty(auth()->user()->avatar),
            ]) style="color: #800000;">
                {{ auth()->user()->initials }}
            </div>
        </div>
        <div class="overflow-hidden">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>
    <form action="/logout" method="POST">
        @csrf
        <button type="submit" class="w-full text-left text-xs text-gray-500 hover:text-red-700 font-medium px-2 py-1 transition-colors flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span>Sign Out</span>
        </button>
    </form>
</div>
