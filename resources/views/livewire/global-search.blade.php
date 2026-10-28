<div class="relative w-36 sm:w-44 md:w-48 lg:w-56 xl:w-64 2xl:w-72 max-w-full transition-all">
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query" 
            placeholder="Search control no, title, student..." 
            class="w-full pl-9 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 transition-all"
        />
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <div wire:loading wire:target="query" class="absolute right-3 top-2.5">
            <svg class="w-4 h-4 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>
    </div>

    @if(strlen($query) >= 2)
        <div class="absolute left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden text-xs">
            @if(count($results) > 0)
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto" data-auto-animate>
                    @foreach($results as $item)
                        <a href="/requests/{{ $item->id }}" class="block p-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="font-mono text-xs font-semibold text-gray-700">{{ $item->control_number ?? 'Draft' }}</span>
                                <x-status-badge :status="$item->status" />
                            </div>
                            <p class="font-semibold text-gray-900 line-clamp-1">{{ $item->title }}</p>
                            <p class="text-gray-500 mt-0.5">{{ $item->student->name }} &bull; {{ $item->department->name }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-4 text-center text-gray-500 font-medium">
                    No matching ethics requests found.
                </div>
            @endif
        </div>
    @endif
</div>
