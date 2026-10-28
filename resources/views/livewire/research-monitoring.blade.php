{{-- Hallmark · design-system: DESIGN.md · genre: modern-minimal --}}
<div class="space-y-6 font-heading">
    <!-- Header Banner (DEMO Maroon Box with Crisp Typography) -->
    <div class="rounded-2xl p-6 md:p-8 text-white shadow-xs bg-maroon-primary">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Research Monitoring Dashboard</h1>
                <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Track post-approval active protocols, annual renewals, progress reports, and international clearances.</p>
            </div>
        </div>
    </div>

    <!-- Sub Nav Header & Tab Controls -->
    <div class="bg-white p-4 rounded-xl border border-neutral-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            <button 
                type="button"
                wire:click="setTab('active')"
                class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 {{ $activeTab === 'active' ? 'bg-maroon-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Active Protocols</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full font-extrabold {{ $activeTab === 'active' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                    {{ $activeCount }}
                </span>
            </button>

            <button 
                type="button"
                wire:click="setTab('host_country')"
                class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 {{ $activeTab === 'host_country' ? 'bg-maroon-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Host Country Clearance</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full font-extrabold {{ $activeTab === 'host_country' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                    {{ $hostCountryCount }}
                </span>
            </button>

            <button 
                type="button"
                wire:click="setTab('completed')"
                class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors inline-flex items-center gap-2 {{ $activeTab === 'completed' ? 'bg-maroon-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Completed / Closed</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full font-extrabold {{ $activeTab === 'completed' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                    {{ $completedCount }}
                </span>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="relative w-full md:w-72">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search protocol title or control #..." 
                class="w-full px-3.5 py-2 pl-9 pr-8 bg-gray-50 border border-neutral-200 rounded-lg text-xs font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-maroon-primary focus:border-maroon-primary transition-colors" 
            />
            <div class="absolute left-3 top-2.5 text-gray-400 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div wire:loading wire:target="search" class="absolute right-2.5 top-2.5">
                <svg class="w-4 h-4 text-maroon-primary animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Status Alert Flash -->
    @if(session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- Protocols Monitoring Table -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="search,setTab,recordProgressReport,markCompleted,gotoPage,previousPage,nextPage" class="bg-white rounded-xl border border-neutral-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50/80 border-b border-neutral-200 text-gray-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Protocol & Investigator</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Control #</th>
                        <th class="px-6 py-4">Approval & Validity</th>
                        <th class="px-6 py-4">Monitoring Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($monitors as $monitor)
                        @php
                            $request = $monitor->ethicsRequest;
                            $daysRemaining = $monitor->expires_at ? (int) now()->diffInDays($monitor->expires_at, false) : null;
                        @endphp
                        <tr wire:key="monitor-{{ $monitor->id }}" class="hover:bg-gray-50/70 transition-colors">
                            <!-- Title and PI -->
                            <td class="px-6 py-4 max-w-sm">
                                <a href="/requests/{{ $request->id }}" class="font-bold text-gray-900 hover:text-maroon-primary transition-colors line-clamp-2 leading-snug">
                                    {{ $request->title }}
                                </a>
                                <div class="text-[11px] text-gray-500 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>PI: {{ $request->student->name ?? 'N/A' }}</span>
                                </div>
                                @if($monitor->notes)
                                    <div class="mt-2 text-[10px] text-gray-600 bg-gray-50 p-2 rounded border border-gray-200 font-mono whitespace-pre-line max-h-24 overflow-y-auto">
                                        {{ $monitor->notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Department -->
                            <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                <div class="font-semibold text-gray-800">{{ $request->department->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-500 mt-0.5 font-medium">
                                    {{ $request->is_local ? 'Local Protocol' : 'International / External' }}
                                </div>
                            </td>

                            <!-- Control Number -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                    {{ $request->control_number ?? 'PENDING' }}
                                </span>
                            </td>

                            <!-- Approval Date & Expiry Countdown Badge -->
                            <td class="px-6 py-4 whitespace-nowrap space-y-1.5">
                                <div class="text-[11px] text-gray-600">
                                    Approved: <strong class="font-semibold text-gray-800">{{ $monitor->approved_at?->toFormattedDateString() ?? 'N/A' }}</strong>
                                </div>
                                @if($monitor->expires_at)
                                    <div>
                                        @if($monitor->expires_at->isPast() || $daysRemaining < 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Expired{{ $daysRemaining < 0 ? ' (' . abs($daysRemaining) . 'd ago)' : '' }}
                                            </span>
                                        @elseif($daysRemaining <= 30)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Expires in {{ $daysRemaining }}d
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Expires in {{ $daysRemaining }}d
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if($monitor->last_report_submitted_at)
                                    <div class="text-[10px] text-gray-500">
                                        Last Report: {{ $monitor->last_report_submitted_at->toFormattedDateString() }}
                                    </div>
                                @endif
                            </td>

                            <!-- Monitoring Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = $monitor->monitoring_status;
                                @endphp
                                @if($status === \App\Enums\MonitoringStatus::Active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @elseif($status === \App\Enums\MonitoringStatus::RenewalDue)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Renewal Due
                                    </span>
                                @elseif($status === \App\Enums\MonitoringStatus::ProgressReportDue)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-800 border border-orange-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        Progress Report Due
                                    </span>
                                @elseif($status === \App\Enums\MonitoringStatus::HostCountryPending)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Host Country Pending
                                    </span>
                                @elseif($status === \App\Enums\MonitoringStatus::Completed)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Completed
                                    </span>
                                @elseif($status === \App\Enums\MonitoringStatus::Terminated)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        Terminated
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button 
                                    type="button" 
                                    wire:click="openReportModal({{ $monitor->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 transition-colors cursor-pointer"
                                    title="Log a progress report"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Log Report</span>
                                </button>

                                @if($monitor->monitoring_status !== \App\Enums\MonitoringStatus::Completed && $monitor->monitoring_status !== \App\Enums\MonitoringStatus::Terminated)
                                    <button 
                                        type="button" 
                                        wire:click="markCompleted({{ $monitor->id }})"
                                        wire:confirm="Are you sure you want to mark this research protocol as completed?"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition-colors cursor-pointer"
                                        title="Mark as completed"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Complete</span>
                                    </button>
                                @endif

                                <a 
                                    href="/requests/{{ $request->id }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors"
                                >
                                    <span>View</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">No research protocols found</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">There are currently no research protocols under this monitoring tab or matching your search filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitors->hasPages())
            <div class="p-4 border-t border-neutral-200">
                {{ $monitors->links() }}
            </div>
        @endif
    </div>

    <!-- Modal for Logging Progress Report -->
    @if($showReportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="relative bg-white rounded-2xl border border-neutral-200 shadow-sm max-w-lg w-full p-6 text-left overflow-hidden">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900">Record Progress Report</h3>
                    <button type="button" wire:click="closeReportModal" class="text-gray-400 hover:text-gray-600 font-bold text-lg p-1">&times;</button>
                </div>

                <div class="mt-4 space-y-4">
                    <p class="text-xs text-gray-600">
                        Enter summary notes for the progress report or renewal documentation received from the Principal Investigator.
                    </p>

                    <div>
                        <label for="progressNotes" class="block text-xs font-bold text-gray-700 mb-1">Report Notes & Observations</label>
                        <textarea 
                            id="progressNotes"
                            wire:model="progressNotes"
                            rows="4"
                            placeholder="e.g. Mid-term progress report submitted, no protocol deviations observed, participant recruitment on track."
                            class="w-full p-3 bg-gray-50 border border-neutral-200 rounded-lg text-xs font-medium text-gray-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-maroon-primary focus:border-maroon-primary"
                        ></textarea>
                        @error('progressNotes')
                            <span class="text-[11px] text-red-600 font-semibold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button 
                        type="button" 
                        wire:click="closeReportModal" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        wire:click="submitProgressReport" 
                        wire:loading.attr="disabled"
                        class="btn-primary text-xs shadow-xs disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="submitProgressReport">Submit Report</span>
                        <span wire:loading wire:target="submitProgressReport">Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
