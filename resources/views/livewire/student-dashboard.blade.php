<div class="space-y-6">
    <!-- Header Action (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm animate-header-glide" style="background-color: #800000;">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">My Ethics Submissions</h1>
            <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Track and manage your submitted research ethics review applications.</p>
        </div>
        <a href="/requests/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white font-bold text-xs rounded-xl transition-all shrink-0 shadow hover:bg-red-50 active:scale-[0.98]" style="color: #800000;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>New Ethics Request</span>
        </a>
    </div>

    <!-- Student Approval Celebration Popup Modal -->
    @if($showApprovalPopup && $popupRequest)
        @teleport('body')
            <div x-data="{ show: true }" x-show="show" class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden border border-gray-200 relative z-[100000] font-heading shadow-2xl my-auto animate-modal-pop">
                    
                    <!-- Header Banner (DEMO Red Box with Large White Text) -->
                    <div class="p-6 md:p-8 text-white space-y-1 relative text-center" style="background-color: #800000;">
                        <button type="button" @click="show = false" wire:click="dismissApprovalPopup" class="absolute top-5 right-5 text-white/80 hover:text-white font-extrabold text-2xl transition-colors cursor-pointer leading-none">&times;</button>
                        
                        <div class="w-16 h-16 rounded-full bg-white/20 border-2 border-white/30 flex items-center justify-center font-extrabold mx-auto mb-3 shadow-inner">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        
                        <span class="text-xs font-bold text-red-200 uppercase tracking-widest block font-heading">Ethical Clearance Notification</span>
                        <h3 class="text-xl md:text-2xl font-extrabold font-heading text-white leading-tight">
                            Research Protocol Ethically Cleared
                        </h3>
                        <p class="text-xs text-red-100 font-medium font-heading pt-1">
                            Official Research Ethics Review Board Clearance Granted
                        </p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 md:p-8 space-y-5 font-heading">
                        <!-- Request Details Card -->
                        <div class="p-5 bg-emerald-50/70 border border-emerald-300 rounded-2xl space-y-2 font-heading">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-extrabold text-emerald-950 uppercase tracking-wider bg-emerald-200 px-2.5 py-0.5 rounded border border-emerald-300">
                                    {{ $popupRequest->control_number }}
                                </span>
                                <span class="text-[10px] font-extrabold text-emerald-800 uppercase bg-emerald-100 px-2 py-0.5 rounded inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Approved</span>
                                </span>
                            </div>
                            <h4 class="font-extrabold text-gray-900 text-sm md:text-base leading-snug font-heading">{{ $popupRequest->title }}</h4>
                        </div>

                        <p class="text-xs text-gray-700 font-medium leading-relaxed font-heading">
                            The DEMO Research Ethics Review Board has reviewed your manuscript and granted official ethical approval. Your signed Certificate of Approval is now available for download.
                        </p>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-100 pt-4 font-heading">
                            <button type="button" @click="show = false" wire:click="dismissApprovalPopup" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors text-center font-heading">Close</button>
                            <a href="/requests/{{ $popupRequest->id }}/certificate" target="_blank" class="w-full sm:w-auto px-5 py-2.5 text-white font-extrabold rounded-xl text-xs transition-all flex items-center justify-center gap-2 font-heading shadow-sm bg-emerald-600 hover:bg-emerald-700">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Download ERB Certificate</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Student Feedback Popup Modal -->
    @if($showFeedbackPopup && $popupRequest)
        @teleport('body')
            <div x-data="{ show: true }" x-show="show" class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-4 overflow-y-auto">
                <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden border border-gray-200 relative z-[100000] font-heading shadow-2xl my-auto animate-modal-pop">
                    
                    <!-- Header Banner (DEMO Red Box with Large White Text) -->
                    <div class="p-6 md:p-8 text-white space-y-1 relative" style="background-color: #800000;">
                        <button type="button" @click="show = false" wire:click="dismissPopup" class="absolute top-5 right-5 text-white/80 hover:text-white font-extrabold text-2xl transition-colors cursor-pointer leading-none">&times;</button>
                        
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold shrink-0 shadow-inner">
                                <svg class="w-5 h-5 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-red-200 uppercase tracking-widest block font-heading">Committee Response</span>
                                <h3 class="text-xl md:text-2xl font-extrabold font-heading text-white leading-tight">Review Committee Feedback Available</h3>
                            </div>
                        </div>
                        <p class="text-xs text-red-100 font-medium font-heading">
                            Official Review Committee Feedback and Action Items Issued
                        </p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 md:p-8 space-y-5 font-heading">
                        <!-- Request Details Card -->
                        <div class="p-4 bg-red-50/70 border border-red-200 rounded-2xl space-y-1.5 font-heading">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[10px] font-bold text-red-900 uppercase tracking-wider bg-red-100 px-2 py-0.5 rounded border border-red-200" style="color: #800000;">
                                    {{ $popupRequest->status === App\Enums\RequestStatus::Approved ? $popupRequest->control_number : 'Pending Approval' }}
                                </span>
                            </div>
                            <h4 class="font-extrabold text-gray-900 text-sm md:text-base leading-snug font-heading">{{ $popupRequest->title }}</h4>
                        </div>

                        <p class="text-xs text-gray-700 font-medium leading-relaxed font-heading">
                            The ERB Review Committee has evaluated your thesis submission. Specific feedback comments have been left, and revisions are required for the following document(s) before final approval:
                        </p>

                        <!-- Documents Requiring Revision Pills -->
                        <div class="space-y-2.5 font-heading">
                            <span class="text-xs font-extrabold text-gray-900 uppercase tracking-wider block font-heading">Action Required on Documents:</span>
                            <div class="space-y-2" data-auto-animate>
                                @foreach($popupRequest->requestDocuments->where('status', App\Enums\DocumentStatus::RevisionsRequested) as $rd)
                                    <div class="p-3 bg-red-50/50 border border-red-200 rounded-xl flex items-center justify-between gap-3 font-heading">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg text-white flex items-center justify-center font-extrabold text-xs shrink-0" style="background-color: #800000;">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </div>
                                            <span class="font-extrabold text-xs text-gray-900 font-heading truncate">{{ $rd->documentType->name }}</span>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-red-900 uppercase bg-red-100 px-2.5 py-1 rounded-lg border border-red-200 shrink-0 font-heading" style="color: #800000;">Needs Revision</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-100 pt-4 font-heading">
                            <button type="button" @click="show = false" wire:click="dismissPopup" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors text-center font-heading">Dismiss</button>
                            <a href="/requests/{{ $popupRequest->id }}" class="w-full sm:w-auto px-5 py-2.5 text-white font-extrabold rounded-xl text-xs transition-all flex items-center justify-center gap-2 font-heading shadow-sm animate-subtle-bounce" style="background-color: #800000;">
                                <span>View Comments & Upload Revisions</span>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Request Cards Grid -->
    <div class="space-y-4" data-auto-animate>
        @forelse($requests as $request)
            @php
                $totalDocs = $request->applicable_documents_count;
                $approvedDocs = $request->approved_documents_count;
                $revisionDocs = $request->revision_documents_count;
            @endphp
            <div class="card-white p-6 hover:border-red-200 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-semibold px-2 py-0.5 bg-gray-100 border border-gray-200 rounded text-gray-700">
                            {{ $request->status === App\Enums\RequestStatus::Approved ? $request->control_number : 'Pending Approval' }}
                        </span>
                        <x-status-badge :status="$request->status" />
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 font-heading leading-snug">
                        {{ $request->title }}
                    </h2>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 font-medium">
                        <span>Department: <strong class="text-gray-700 font-semibold">{{ $request->department->name }}</strong></span>
                        <span>&bull;</span>
                        <span>Submitted: <strong class="text-gray-700 font-semibold">{{ $request->created_at->format('M d, Y') }}</strong></span>
                        @if($request->expiry_date)
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1">
                                <span>Expiry: <strong class="text-gray-900 font-semibold">{{ $request->expiry_date->format('M d, Y') }}</strong></span>
                                <span class="group relative inline-flex items-center">
                                     <span class="w-3.5 h-3.5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center cursor-help hover:bg-red-900 hover:text-white shrink-0" title="Valid for one year from official issuance date">
                                         <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                     </span>
                                     <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover:block w-44 p-2 bg-gray-900 text-white text-[10px] font-medium rounded-lg shadow-lg z-30 font-heading leading-tight text-center pointer-events-none">
                                         Valid for one year from official issuance date
                                     </span>
                                </span>
                            </span>
                        @endif
                    </div>

                    <!-- Progress Bar / Indicator -->
                    <div class="pt-2 flex items-center gap-3">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden border border-gray-200">
                            <div class="bg-green-600 h-full rounded-full transition-all" style="width: {{ $totalDocs > 0 ? ($approvedDocs / $totalDocs) * 100 : 0 }}%;"></div>
                        </div>
                        <span class="text-xs font-semibold text-gray-600 shrink-0">
                            {{ $approvedDocs }}/{{ $totalDocs }} approved
                            @if($revisionDocs > 0)
                                <span class="text-orange-600 font-bold">({{ $revisionDocs }} needs revision)</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-3">

                    @if($request->certificate_path)
                        <a href="/requests/{{ $request->id }}/certificate" target="_blank" class="btn-secondary text-xs">
                            <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download Certificate</span>
                        </a>
                    @endif
                    <a href="/requests/{{ $request->id }}" class="btn-primary text-xs">
                        View Request
                    </a>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="card-white p-12 text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-red-50 text-maroon mx-auto flex items-center justify-center" style="background-color: #FFF5F5; color: #800000;">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 font-heading">Start Your Ethics Review Submission</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">You haven't submitted any thesis ethics review applications yet. Click below to begin your submission.</p>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="/requests/create" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Create New Ethics Request</span>
                    </a>

                    @if(config('app.demo_mode'))
                        <button 
                            type="button" 
                            wire:click="claimSampleRequest" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            wire:target="claimSampleRequest"
                            class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold rounded-xl text-xs transition-colors inline-flex items-center gap-1.5 font-heading cursor-pointer disabled:opacity-50"
                        >
                            <svg wire:loading.remove wire:target="claimSampleRequest" class="w-4 h-4 text-amber-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <svg wire:loading wire:target="claimSampleRequest" class="w-4 h-4 text-amber-800 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="claimSampleRequest">Load Sample Research Protocol</span>
                            <span wire:loading wire:target="claimSampleRequest">Loading Sample Protocol...</span>
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
