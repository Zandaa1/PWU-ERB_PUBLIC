<div class="relative">
    <button wire:click="toggle" aria-label="Notifications" title="View Notifications" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors focus:outline-none cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-4 h-4 bg-red-600 text-white rounded-full text-[10px] font-bold flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    @if($open)
        <div class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">
            <div class="p-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Notifications</h4>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-maroon hover:underline font-semibold" style="color: #800000;">Mark all as read</button>
                @endif
            </div>

            <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto text-xs" data-auto-animate>
                @forelse($notifications as $n)
                    <div class="p-3 hover:bg-gray-50 transition-colors {{ !$n->is_read ? 'bg-red-50/30' : '' }}">
                        <a href="{{ $n->link ?? '#' }}" wire:click="markAsRead({{ $n->id }})" class="block">
                            <p class="text-gray-900 font-medium leading-snug">{{ $n->message }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-mono">{{ $n->created_at->diffForHumans() }}</p>
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 font-medium">
                        No notifications yet.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
