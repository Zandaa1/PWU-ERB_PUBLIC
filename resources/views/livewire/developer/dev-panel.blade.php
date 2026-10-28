<div class="space-y-6 max-w-5xl mx-auto text-xs">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">Developer Console & Operations</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1 font-heading">Technical monitoring, environment diagnostics, queue health, and certificate counters.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4 font-heading">
        <div class="flex flex-wrap items-center gap-2 font-heading">
            <button wire:click="setTab('system')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'system',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'system',
            ])>System Info</button>

            <button wire:click="setTab('queue')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'queue',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'queue',
            ])>Queue Monitor ({{ $pendingJobs }} pending)</button>

            <button wire:click="setTab('counters')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'counters',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'counters',
            ])>Control Number Counters</button>

            <button wire:click="setTab('logs')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'logs',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'logs',
            ])>Error Logs</button>

            <button wire:click="setTab('announcements')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer inline-flex items-center gap-1.5',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'announcements',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'announcements',
            ])>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 58h2m-1 0v-1m0 0a9 9 0 100-18 9 9 0 000 18zM11 5L6 9H2v6h4l5 4V5z"/></svg>
                <span>Site Announcements</span>
            </button>

            <button wire:click="setTab('bugs')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer inline-flex items-center gap-1.5',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'bugs',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'bugs',
            ])>
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Bug Reports</span>
                @if($openBugCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-red-600 text-white font-extrabold">{{ $openBugCount }}</span>
                @endif
            </button>

            <button wire:click="setTab('users')" @class([
                'px-3 py-1.5 rounded-xl font-bold uppercase transition-colors text-xs font-heading cursor-pointer inline-flex items-center gap-1.5',
                'bg-[#800000] text-white shadow-xs' => $activeTab === 'users',
                'bg-gray-100 text-gray-700 hover:bg-gray-200' => $activeTab !== 'users',
            ])>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>User Roles & Access</span>
            </button>
        </div>

    </div>

    <!-- Tab 1: System Info & Certificate Test Suite -->
    @if($activeTab === 'system')
        <div class="space-y-6">
            @if(session()->has('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl font-bold font-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="card-white p-6 space-y-6">
                <div>
                    <h3 class="font-bold text-sm text-gray-900 uppercase border-b border-gray-200 pb-2 mb-4">Environment & Runtime Specifications</h3>
                    <div class="grid grid-cols-2 gap-4" data-auto-animate>
                        @foreach($systemInfo as $key => $val)
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded">
                                <span class="text-gray-400 block uppercase font-bold text-[10px]">{{ $key }}</span>
                                <span class="font-bold text-gray-900 text-sm mt-0.5 block">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ERB Chairperson Designation Tool -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <h4 class="font-bold text-xs uppercase text-amber-950 flex items-center gap-2 font-heading">
                                    <svg class="w-4 h-4 text-amber-700 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>ERB Chairperson Designation (Developer Authority)</span>
                                </h4>
                                <p class="text-xs text-amber-900 font-medium font-heading">Designate the acting institutional Chairperson authorized to digitally sign ethics certificates.</p>
                            </div>
                            @if($currentChairperson)
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded bg-amber-200 text-amber-900 font-bold text-xs border border-amber-300">
                                        Current: {{ $currentChairperson->name }} ({{ $currentChairperson->email }})
                                    </span>
                                    <button 
                                        wire:click="removeChairperson" 
                                        wire:confirm="Are you sure you want to remove current chairperson designation?"
                                        class="px-2.5 py-1 bg-white hover:bg-gray-100 text-amber-900 border border-amber-300 rounded font-semibold text-xs transition-colors cursor-pointer"
                                    >
                                        Clear
                                    </button>
                                </div>
                            @else
                                <span class="px-2.5 py-1 rounded bg-gray-100 text-gray-700 font-semibold text-xs border border-gray-300">
                                    No Chairperson Designated
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 pt-2 border-t border-amber-200/60">
                            <label for="chairpersonSelect" class="text-xs font-semibold text-amber-950 shrink-0">Designate New Chair:</label>
                            <select 
                                id="chairpersonSelect"
                                wire:change="designateChairperson($event.target.value)"
                                class="flex-1 px-3 py-1.5 bg-white border border-amber-300 rounded text-xs font-semibold text-gray-800 focus:outline-none focus:ring-1 focus:ring-amber-500 cursor-pointer"
                            >
                                <option value="">-- Select user to designate as Chairperson --</option>
                                @foreach($eligibleChairpersons as $u)
                                    <option value="{{ $u->id }}" {{ $u->is_chairperson ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }}) — {{ ucfirst(implode(', ', $u->getRoleNames()->toArray()) ?: 'User') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Certificate Process Testing Tool -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-xs uppercase text-[#800000] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Ethics Certificate Generation & Render Testing
                            </h4>
                            <p class="text-xs text-[#800000] font-medium">Generate and stream a sample ERB Ethics Review Approval Certificate PDF with letterhead, mock control number, and chairperson signature.</p>
                        </div>
                        <a href="/admin/dev/test-certificate" target="_blank" class="px-4 py-2 bg-[#800000] hover:bg-[#5C0000] text-white rounded font-bold text-xs flex items-center gap-2 shadow-sm transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Preview Test Certificate PDF</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Resend Transactional Email Diagnostics Tool -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg space-y-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-xs uppercase text-red-950 flex items-center gap-2 font-heading" style="color: #800000;">
                                <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Resend Transactional Email Diagnostics
                            </h4>
                            <p class="text-xs text-red-900 font-medium font-heading">
                                Test Resend API + the new maroon-box DEMO layout. Send one sample or all 3 templates at once to a single address.
                            </p>
                            <p class="text-[11px] text-red-800 font-medium font-heading">Templates: 1) Submission Acknowledgment &bull; 2) Decision Update (Approved) &bull; 3) Reviewer Feedback</p>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-start gap-3">
                                <div class="flex-1">
                                    <label for="testEmailRecipient" class="sr-only">Recipient Email Address</label>
                                    <input 
                                        id="testEmailRecipient"
                                        type="email" 
                                        wire:model="testEmailRecipient" 
                                        placeholder="Enter recipient email (e.g. developer@example.invalid)" 
                                        class="w-full p-2.5 bg-white border border-gray-300 rounded-xl text-xs font-medium font-heading focus:border-red-900 focus:outline-none"
                                    />
                                    @error('testEmailRecipient')
                                        <span class="text-[11px] text-red-600 font-bold font-heading block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button 
                                    type="button" 
                                    wire:click="sendTestResendEmail" 
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-60 cursor-wait"
                                    wire:target="sendTestResendEmail"
                                    class="px-4 py-2.5 bg-red-800 hover:bg-red-900 text-white rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-colors shrink-0 font-heading cursor-pointer disabled:opacity-50"
                                    style="background-color: #800000;"
                                >
                                    <svg wire:loading.remove wire:target="sendTestResendEmail" class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <svg wire:loading wire:target="sendTestResendEmail" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="sendTestResendEmail">Send 1 Test Email</span>
                                    <span wire:loading wire:target="sendTestResendEmail">Sending...</span>
                                </button>
                            </div>

                            <button 
                                type="button" 
                                wire:click="sendAllTestResendEmails" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                wire:target="sendAllTestResendEmails"
                                class="w-full sm:w-auto px-4 py-2.5 bg-white hover:bg-gray-50 text-white rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-colors shrink-0 font-heading cursor-pointer disabled:opacity-50 border"
                                style="background-color: #FFFFFF; color: #800000; border-color: #800000;"
                            >
                                <svg wire:loading.remove wire:target="sendAllTestResendEmails" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #800000;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <svg wire:loading wire:target="sendAllTestResendEmails" class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" style="color: #800000;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="sendAllTestResendEmails">Send All 3 Emails to This Address</span>
                                <span wire:loading wire:target="sendAllTestResendEmails">Sending All 3...</span>
                            </button>
                            <p class="text-[11px] text-gray-500 font-medium font-heading">Sends submission + decision + comment in sequence. Useful to preview the maroon-box layout across all templates with one click.</p>
                        </div>
                    </div>
                </div>

                <!-- Temporary Upload Storage Maintenance Tool -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-xs uppercase text-red-950 flex items-center gap-2 font-heading" style="color: #800000;">
                                <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Orphan Temporary Uploads Storage Health
                            </h4>
                            <p class="text-xs text-red-900 font-medium font-heading">
                                Temporary Files: <strong>{{ $tmpFileCount }} files</strong> ({{ $tmpMB }} MB). Automatically purged daily via <code>app:clean-orphan-uploads</code>.
                            </p>
                        </div>
                        <button 
                            type="button" 
                            wire:click="purgeOrphanUploads" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            wire:target="purgeOrphanUploads"
                            class="px-4 py-2 bg-red-800 hover:bg-red-900 text-white rounded-xl font-bold text-xs flex items-center gap-2 shadow-xs transition-colors shrink-0 font-heading cursor-pointer disabled:opacity-50"
                            style="background-color: #800000;"
                        >
                            <svg wire:loading.remove wire:target="purgeOrphanUploads" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <svg wire:loading wire:target="purgeOrphanUploads" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="purgeOrphanUploads">Purge Orphan Uploads Storage Now</span>
                            <span wire:loading wire:target="purgeOrphanUploads">Purging Storage...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 2: Queue Monitor -->
    @if($activeTab === 'queue')
        <div class="card-white p-6 space-y-4">
            <h3 class="font-bold text-sm text-gray-900 uppercase border-b border-gray-200 pb-2">Queue & Background Jobs</h3>
            <p class="text-gray-500">Pending Database Jobs Count: <strong class="text-gray-900">{{ $pendingJobs }}</strong></p>

            <h4 class="font-bold text-xs uppercase text-red-700 pt-2">Failed Jobs Log:</h4>
            <div class="space-y-2" data-auto-animate>
                @forelse($failedJobs as $fj)
                    <div class="p-3 bg-red-50 border border-red-200 rounded">
                        <p class="font-bold text-red-900">{{ $fj->payload }}</p>
                        <p class="text-red-700 mt-1">{{ $fj->exception }}</p>
                    </div>
                @empty
                    <p class="text-gray-400 italic">No failed queue jobs recorded.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Tab 3: Control Number Counters Editor -->
    @if($activeTab === 'counters')
        <div class="card-white p-6 space-y-4">
            <div class="border-b border-gray-200 pb-2">
                <h3 class="font-bold text-sm text-gray-900 uppercase">Control Number Sequence Editors</h3>
                <p class="text-amber-800 text-[11px] font-semibold mt-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>CAUTION:</strong> Manually adjusting sequence numbers may cause certificate collisions or sequence gaps.</span>
                </p>
            </div>

            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                    <tr>
                        <th class="p-3">Scope ID</th>
                        <th class="p-3">Last Sequence Issued</th>
                        <th class="p-3">Next Issued Number</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" data-auto-animate>
                    @foreach($counters as $c)
                        <tr>
                            <td class="p-3 font-bold text-gray-900">{{ $c->scope }}</td>
                            <td class="p-3 font-bold text-[#800000]">{{ $c->last_sequence }}</td>
                            <td class="p-3 text-gray-600">{{ $c->scope }}_{{ str_pad($c->last_sequence + 1, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-3 text-right">
                                <button wire:click="openEditCounter({{ $c->id }}, {{ $c->last_sequence }})" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded font-bold text-[10px]">
                                    Manually Adjust Sequence
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Tab 4: Error Logs -->
    @if($activeTab === 'logs')
        <div class="card-white p-6 space-y-4">
            <h3 class="font-bold text-sm text-gray-900 uppercase border-b border-gray-200 pb-2">Recent Laravel Error Logs (Tail 50)</h3>
            <pre class="bg-neutral-900 text-green-400 p-4 rounded-lg overflow-x-auto text-[11px] leading-relaxed max-h-96 whitespace-pre-wrap">{{ $recentLogs }}</pre>
        </div>
    @endif

    <!-- Tab 5: Site-Wide Announcements (Maintenance, Down, 404, 402, Info) -->
    @if($activeTab === 'announcements')
        <div class="space-y-6 font-heading">
            <!-- Flash Message -->
            @if(session()->has('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl font-bold font-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Announcement Control Form -->
            <div class="bg-white border border-gray-200 rounded-3xl p-6 md:p-8 space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-extrabold text-gray-900 font-heading">Broadcast Site-Wide System Announcement</h3>
                    <p class="text-xs text-gray-600 font-medium font-heading">Publish immediate operational alert banners across all pages for maintenance, service outages, 404/402 issues, or general information.</p>
                </div>

                <!-- Presets Bar -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider font-heading">Quick Operational Presets:</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="applyPreset('maintenance')" class="px-3.5 py-2 bg-red-100 hover:bg-red-200 text-red-950 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 font-heading" style="color: #800000;">
                            <svg class="w-3.5 h-3.5 text-maroon shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #800000;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 012 2v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h1.586A2 2 0 0111 4zM4 14h16m-2 0v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4"/></svg>
                            <span>Maintenance Mode</span>
                        </button>
                        <button type="button" wire:click="applyPreset('down')" class="px-3.5 py-2 bg-red-900 text-white hover:bg-red-950 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 font-heading">
                            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>System Outage</span>
                        </button>
                        <button type="button" wire:click="applyPreset('404_issue')" class="px-3.5 py-2 bg-orange-100 hover:bg-orange-200 text-orange-950 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 font-heading">
                            <svg class="w-3.5 h-3.5 text-orange-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>404 Route Issue</span>
                        </button>
                        <button type="button" wire:click="applyPreset('402_issue')" class="px-3.5 py-2 bg-amber-100 hover:bg-amber-200 text-amber-950 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 font-heading">
                            <svg class="w-3.5 h-3.5 text-amber-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>402 Fee Issue</span>
                        </button>
                        <button type="button" wire:click="applyPreset('info')" class="px-3.5 py-2 bg-blue-100 hover:bg-blue-200 text-blue-950 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 font-heading">
                            <svg class="w-3.5 h-3.5 text-blue-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>General Info</span>
                        </button>
                    </div>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="saveAnnouncement" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider font-heading">Alert Severity Type</label>
                            <select wire:model="announcementType" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-bold font-heading focus:bg-white focus:outline-none">
                                <option value="maintenance">Maintenance Mode (#800000 DEMO Red)</option>
                                <option value="down">System Outage / Service Down (Dark Red)</option>
                                <option value="404_issue">404 Route / Link Issue (Orange)</option>
                                <option value="402_issue">402 Review Fee Payment Issue (Amber)</option>
                                <option value="info">General Notice (Blue)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider font-heading">Announcement Title</label>
                            <input type="text" wire:model="announcementTitle" placeholder="e.g. Scheduled System Maintenance" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-bold font-heading focus:bg-white focus:outline-none" />
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider font-heading">Announcement Message (Displayed Site-Wide)</label>
                        <textarea wire:model="announcementMessage" rows="3" placeholder="Detail the operational notice or technical status for users..." class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium font-heading focus:bg-white focus:outline-none"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer font-heading">
                            <input type="checkbox" wire:model="announcementIsActive" class="w-4 h-4 text-maroon rounded border-gray-300 focus:ring-maroon" />
                            <span class="text-xs font-extrabold text-gray-900">Publish & Activate Site-Wide Banner Immediately</span>
                        </label>

                        <button type="submit" class="px-6 py-3 text-white font-extrabold text-xs rounded-xl shadow transition-all font-heading" style="background-color: #800000;">
                            Save & Update Announcement
                        </button>
                    </div>
                </form>
            </div>

            <!-- Active / Past Announcements List -->
            <div class="bg-white border border-gray-200 rounded-3xl p-6 md:p-8 space-y-4">
                <h4 class="text-base font-extrabold text-gray-900 font-heading">Announcement History & Status</h4>
                <div class="divide-y divide-gray-100" data-auto-animate>
                    @forelse($announcements as $ann)
                        <div class="py-3.5 flex flex-col md:flex-row md:items-center justify-between gap-4 font-heading">
                            <div class="space-y-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'px-2.5 py-0.5 rounded text-[10px] uppercase font-bold text-white',
                                        'bg-[#800000]' => $ann->type === 'maintenance',
                                        'bg-red-800' => $ann->type === 'down',
                                        'bg-orange-700' => $ann->type === '404_issue',
                                        'bg-amber-700' => $ann->type === '402_issue',
                                        'bg-blue-800' => $ann->type === 'info',
                                    ])>
                                        {{ strtoupper(str_replace('_', ' ', $ann->type)) }}
                                    </span>
                                    <strong class="font-extrabold text-sm text-gray-900 font-heading">{{ $ann->title }}</strong>
                                    @if($ann->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded text-[10px] font-extrabold uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            <span>Active Live</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-200 rounded text-[10px] font-bold uppercase">Inactive</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-700 font-medium leading-relaxed font-heading">{{ $ann->message }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">Created by {{ $ann->creator->name ?? 'Developer' }} &bull; {{ $ann->created_at->diffForHumans() }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button wire:click="toggleAnnouncementStatus({{ $ann->id }})" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold transition-colors">
                                    {{ $ann->is_active ? 'Deactivate' : 'Activate Live' }}
                                </button>
                                <button wire:click="deleteAnnouncement({{ $ann->id }})" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-bold transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 italic py-4">No system announcements recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 6: Bug Reports & Telemetry Inspector -->
    @if($activeTab === 'bugs')
        <div class="card-white p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h3 class="font-bold text-sm text-gray-900 uppercase">Reported Portal Bugs & Telemetry Inspector</h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Inspect user-submitted bug reports, browser telemetry, screen resolutions, and diagnostics.</p>
                </div>
                
                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl shrink-0 font-heading">
                    <button wire:click="$set('bugStatusFilter', 'all')" @class(['px-2.5 py-1 rounded-lg text-xs font-bold transition-colors', 'bg-white text-gray-900 shadow-xs' => $bugStatusFilter === 'all', 'text-gray-600 hover:text-gray-900' => $bugStatusFilter !== 'all'])>All</button>
                    <button wire:click="$set('bugStatusFilter', 'open')" @class(['px-2.5 py-1 rounded-lg text-xs font-bold transition-colors', 'bg-red-600 text-white shadow-xs' => $bugStatusFilter === 'open', 'text-gray-600 hover:text-gray-900' => $bugStatusFilter !== 'open'])>Open ({{ $openBugCount }})</button>
                    <button wire:click="$set('bugStatusFilter', 'in_progress')" @class(['px-2.5 py-1 rounded-lg text-xs font-bold transition-colors', 'bg-amber-600 text-white shadow-xs' => $bugStatusFilter === 'in_progress', 'text-gray-600 hover:text-gray-900' => $bugStatusFilter !== 'in_progress'])>In Progress</button>
                    <button wire:click="$set('bugStatusFilter', 'resolved')" @class(['px-2.5 py-1 rounded-lg text-xs font-bold transition-colors', 'bg-emerald-600 text-white shadow-xs' => $bugStatusFilter === 'resolved', 'text-gray-600 hover:text-gray-900' => $bugStatusFilter !== 'resolved'])>Resolved</button>
                </div>
            </div>

            @if($bugReports->isEmpty())
                <div class="p-8 bg-gray-50 border border-gray-200 rounded-2xl text-center space-y-2 text-gray-500">
                    <p class="text-xs font-bold text-gray-700">No bug reports found matching filter "{{ $bugStatusFilter }}".</p>
                    <p class="text-[11px]">All portal components are running smoothly with no reported telemetry errors.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 border border-gray-200 rounded-2xl overflow-hidden" data-auto-animate>
                    @foreach($bugReports as $report)
                        <div class="p-4 bg-white hover:bg-gray-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($report->status === 'open')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-100 text-red-800 border border-red-200">Open</span>
                                    @elseif($report->status === 'in_progress')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200">In Progress</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">Resolved</span>
                                    @endif

                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $report->category }}
                                    </span>

                                    <span class="text-xs font-bold text-gray-900 truncate">{{ $report->subject }}</span>
                                </div>

                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">{{ $report->description }}</p>

                                <div class="flex items-center gap-3 text-[11px] text-gray-400 font-mono pt-1">
                                    <span>User: <strong>{{ $report->user?->name ?? 'Guest' }}</strong> ({{ $report->user?->email }})</span>
                                    <span>&bull;</span>
                                    <span>Date: {{ $report->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="inspectTelemetry({{ $report->id }})" class="px-3 py-1.5 bg-gray-900 hover:bg-black text-white font-bold rounded-lg text-xs transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Inspect Details
                                </button>

                                @if($report->status !== 'resolved')
                                    <button type="button" wire:click="markBugStatus({{ $report->id }}, 'resolved')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition-colors">
                                        Mark Resolved
                                    </button>
                                @else
                                    <button type="button" wire:click="markBugStatus({{ $report->id }}, 'open')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition-colors">
                                        Re-open
                                    </button>
                                @endif

                                <button type="button" wire:click="deleteBugReport({{ $report->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Report">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Bug Report Details Modal -->
    <!-- Bug Report Details Modal -->
    @if($showTelemetryModal && $selectedBugReport)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl sm:rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading animate-in fade-in zoom-in-95 duration-200 shadow-2xl my-auto mx-auto">
                    
                    <!-- Header -->
                    <div class="p-4 sm:p-6 text-white space-y-1 relative bg-gray-900 shrink-0">
                        <button type="button" wire:click="$set('showTelemetryModal', false)" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-white/80 hover:text-white font-extrabold text-2xl transition-colors cursor-pointer leading-none">&times;</button>
                        
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-[#800000] text-white">
                                Bug Report #{{ $selectedBugReport->id }}
                            </span>
                            <span class="text-xs text-gray-400 font-mono">{{ $selectedBugReport->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-extrabold text-white leading-tight">{{ $selectedBugReport->subject }}</h3>
                    </div>

                    <!-- Body -->
                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5 font-heading text-xs flex-1 overflow-y-auto">
                        
                        <!-- Description Box -->
                        <div class="p-3.5 sm:p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-1">
                            <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">User Issue Description:</span>
                            <p class="text-xs text-gray-900 font-medium leading-relaxed">{{ $selectedBugReport->description }}</p>
                        </div>

                        <!-- Report Meta -->
                        <div class="p-3.5 sm:p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-1 font-mono text-xs break-all">
                            <div><strong>Page URL:</strong> {{ $selectedBugReport->page_url }}</div>
                            <div><strong>User Agent:</strong> {{ $selectedBugReport->user_agent }}</div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 shrink-0">
                            <button type="button" wire:click="$set('showTelemetryModal', false)" class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors cursor-pointer text-center">Close</button>
                        </div>

                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Tab 7: User Roles & Access Management (Developer Authority) -->
    @if($activeTab === 'users')
        <div class="space-y-6">
            @if(session()->has('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl font-bold font-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="p-4 bg-red-50 border border-red-300 text-red-900 rounded-2xl font-bold font-heading flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Pre-Provision / Grant Access Card -->
            <form wire:submit.prevent="provisionUser" class="card-white p-6 space-y-4 font-heading">
                <div class="border-b border-gray-200 pb-3">
                    <h3 class="font-bold text-sm text-gray-900 uppercase">Pre-Provision & Grant Roles (Web UI)</h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Assign roles or developer privileges to any @example.test email before or after their first Google login.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">User Institutional Email *</label>
                        <input type="email" wire:model="newUserEmail" placeholder="e.g. dean@example.test" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-maroon-800" />
                        @error('newUserEmail') <p class="text-[10px] text-red-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Full Name (Optional)</label>
                        <input type="text" wire:model="newUserName" placeholder="e.g. Demo Chair" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-maroon-800" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Assigned Role</label>
                        <select wire:model="newUserRole" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs font-bold text-gray-800 focus:bg-white focus:outline-none">
                            <option value="admin">Admin (Dean / Coordinator)</option>
                            <option value="committee">Committee Reviewer</option>
                            <option value="student">Student / Researcher</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                            <input type="checkbox" wire:model="newUserIsDeveloper" class="rounded text-maroon" />
                            <span>Grant Developer Access (`is_developer`)</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-amber-900 cursor-pointer">
                            <input type="checkbox" wire:model="newUserIsChairperson" class="rounded text-amber-600" />
                            <span>Designate as ERB Chairperson</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="provisionUser"
                        class="btn-primary text-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading wire:target="provisionUser" class="w-3.5 h-3.5 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="provisionUser">+ Grant / Update Access</span>
                        <span wire:loading wire:target="provisionUser">Saving...</span>
                    </button>
                </div>
            </form>

            <!-- All Users Table -->
            <div class="card-white overflow-hidden font-heading">
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                    <div>
                        <h4 class="font-bold text-xs uppercase text-gray-900">All Registered & Provisioned Accounts ({{ $allUsers->count() }})</h4>
                        <p class="text-[11px] text-gray-500 font-medium">Complete university user directory including Developers, Admins, Reviewers, and Students.</p>
                    </div>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="userSearch" placeholder="Filter by name or email..." class="px-3 py-1.5 pr-8 bg-white border border-gray-300 rounded text-xs font-medium focus:outline-none focus:ring-1 focus:ring-maroon-800" />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">User Details</th>
                                <th class="px-6 py-4">Spatie Role</th>
                                <th class="px-6 py-4">Developer Privilege</th>
                                <th class="px-6 py-4">Chairperson</th>
                                <th class="px-6 py-4 text-right">Account Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                            @foreach($allUsers as $u)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $u->name }}</div>
                                        <div class="text-gray-500 text-[11px] font-mono">{{ $u->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select 
                                            wire:change="assignUserRole({{ $u->id }}, $event.target.value)" 
                                            wire:loading.attr="disabled"
                                            class="px-2 py-1 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-700 cursor-pointer disabled:opacity-50"
                                        >
                                            @foreach($allRoles as $r)
                                                <option value="{{ $r->name }}" {{ $u->hasRole($r->name) ? 'selected' : '' }}>
                                                    {{ ucfirst($r->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button 
                                            wire:click="toggleDeveloper({{ $u->id }})" 
                                            wire:confirm="Are you sure you want to toggle developer privileges for this account?"
                                            wire:loading.attr="disabled"
                                            @class([
                                                'px-2.5 py-1 rounded text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer disabled:opacity-50',
                                                'bg-purple-100 text-purple-900 border border-purple-300' => $u->is_developer,
                                                'bg-gray-100 text-gray-600 hover:bg-gray-200' => !$u->is_developer,
                                            ])
                                        >
                                            @if($u->is_developer)
                                                <span>Developer</span>
                                            @else
                                                <span>Standard</span>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($u->is_chairperson)
                                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span>Chairperson</span>
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 font-medium">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button 
                                            wire:click="toggleUserActive({{ $u->id }})" 
                                            wire:confirm="Are you sure you want to change this account active status?"
                                            wire:loading.attr="disabled"
                                            @class([
                                                'px-2.5 py-1 rounded text-xs font-semibold cursor-pointer disabled:opacity-50',
                                                'bg-green-100 text-green-800' => $u->is_active,
                                                'bg-red-100 text-red-800' => !$u->is_active,
                                            ])
                                        >
                                            {{ $u->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirmation Modal for Sequence Change -->
    @if($showConfirmModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col overflow-hidden border border-red-200 p-5 sm:p-6 space-y-4 shadow-2xl relative z-[100000] my-auto mx-auto font-heading">
                    <h3 class="text-sm font-bold text-red-700 uppercase">Confirm Dangerous Operation</h3>
                    <p class="text-xs text-gray-700">Are you sure you want to manually adjust the last sequence counter to <strong class="text-[#800000] font-bold">{{ $newSequence }}</strong>?</p>
                    <input type="number" wire:model="newSequence" class="w-full p-2.5 border border-gray-300 rounded-xl font-bold text-sm focus:ring-2 focus:ring-maroon-800" />
                    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-2">
                        <button wire:click="$set('showConfirmModal', false)" class="w-full sm:w-auto btn-secondary text-xs text-center justify-center">Cancel</button>
                        <button wire:click="confirmUpdateCounter" class="w-full sm:w-auto btn-danger text-xs text-center justify-center">Confirm Sequence Override</button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
