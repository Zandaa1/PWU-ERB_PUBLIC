<div>
    @if($announcement)
        <div 
            x-data="{ dismissed: false }" 
            x-show="!dismissed" 
            x-cloak
            class="font-heading transition-all duration-300 mb-6"
        >
        <div @class([
            'p-4 rounded-2xl border flex flex-col md:flex-row md:items-start justify-between gap-3 shadow-sm relative overflow-hidden font-heading',
            'bg-red-50/90 border-red-300 text-red-950 animate-border-glow' => in_array($announcement->type, ['maintenance', 'down']),
            'bg-amber-50/90 border-amber-300 text-amber-950' => in_array($announcement->type, ['404_issue', '402_issue']),
            'bg-red-50/70 border-red-200 text-gray-900' => !in_array($announcement->type, ['maintenance', 'down', '404_issue', '402_issue']),
        ])>
            <!-- Left Icon + Announcement Content -->
            <div class="flex items-start gap-3 flex-1 min-w-0 font-heading">
                
                <!-- Animated Alert Badge Icon -->
                <div @class([
                    'w-9 h-9 rounded-xl flex items-center justify-center font-extrabold shrink-0 mt-0.5 shadow-xs font-heading',
                    'bg-red-600 text-white animate-pulse-glow' => in_array($announcement->type, ['maintenance', 'down']),
                    'bg-amber-600 text-white' => in_array($announcement->type, ['404_issue', '402_issue']),
                    'bg-[#800000] text-white' => !in_array($announcement->type, ['maintenance', 'down', '404_issue', '402_issue']),
                ])>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 58h2m-1 0v-1m0 0a9 9 0 100-18 9 9 0 000 18zM11 5L6 9H2v6h4l5 4V5z"/></svg>
                </div>

                <div class="space-y-1 flex-1 min-w-0 font-heading">
                    <div class="flex items-center gap-2 flex-wrap font-heading">
                        <span @class([
                            'px-2 py-0.5 rounded text-[10px] font-extrabold uppercase font-heading tracking-wider text-white',
                            'bg-red-700' => in_array($announcement->type, ['maintenance', 'down']),
                            'bg-amber-700' => in_array($announcement->type, ['404_issue', '402_issue']),
                            'bg-red-900' => !in_array($announcement->type, ['maintenance', 'down', '404_issue', '402_issue']),
                        ]) style="background-color: #800000;">
                            System Announcement
                        </span>
                        <h4 class="text-sm font-extrabold text-gray-900 font-heading">{{ $announcement->title }}</h4>
                    </div>

                    <!-- Full Announcement Message -->
                    <div class="text-xs text-gray-800 font-medium leading-relaxed font-heading whitespace-pre-line">
                        <p class="font-heading">{{ $announcement->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Dismiss Button -->
            <button 
                type="button" 
                @click="dismissed = true" 
                class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-black/5 transition-colors shrink-0 self-start font-heading"
                title="Dismiss Banner"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
@endif
</div>
