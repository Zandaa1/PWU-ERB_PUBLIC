@if(auth()->user()->hasRole('student') && !auth()->user()->hasAnyRole(['committee', 'admin', 'secretary']) && !auth()->user()->is_chairperson)
    <a href="/dashboard" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('dashboard') || request()->is('requests*') ? 'bg-red-50 text-maroon font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('dashboard') || request()->is('requests*') ? 'color: #800000; border-left: 4px solid #800000;' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>My Requests</span>
    </a>
@endif

@if(auth()->user()->hasAnyRole(['committee', 'admin', 'secretary']) || auth()->user()->is_chairperson || auth()->user()->is_developer)
    <a href="/dashboard" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('dashboard') || request()->is('requests*') ? 'bg-red-50 text-maroon font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('dashboard') || request()->is('requests*') ? 'color: #800000; border-left: 4px solid #800000;' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>Review Queue</span>
    </a>
@endif

@can('view-monitoring')
    <a href="/monitoring" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('monitoring*') ? 'bg-red-50 text-maroon font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('monitoring*') ? 'color: #800000; border-left: 4px solid #800000;' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Research Monitoring</span>
    </a>
@endcan

@if(auth()->user()->hasRole('admin') || auth()->user()->is_developer)
    <a href="/admin" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('admin') || request()->is('admin/users*') || request()->is('admin/departments*') || request()->is('admin/document-types*') || request()->is('admin/archive*') || request()->is('admin/audit-log*') ? 'bg-red-50 text-maroon font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('admin') || request()->is('admin/users*') || request()->is('admin/departments*') || request()->is('admin/document-types*') || request()->is('admin/archive*') || request()->is('admin/audit-log*') ? 'color: #800000; border-left: 4px solid #800000;' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Admin Panel</span>
    </a>
@endif

@if(auth()->user()->is_developer)
    <a href="/admin/dev" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('admin/dev*') ? 'bg-purple-50 text-purple-900 font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('admin/dev*') ? 'border-left: 4px solid #6B21A8;' : '' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
        <span>Developer Panel</span>
    </a>
@endif

<a href="/faq" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-semibold transition-colors {{ request()->is('faq') ? 'bg-red-50 text-maroon font-bold' : 'text-gray-600 hover:bg-gray-50' }}" style="{{ request()->is('faq') ? 'color: #800000; border-left: 4px solid #800000;' : '' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>FAQ & Guidelines</span>
</a>
