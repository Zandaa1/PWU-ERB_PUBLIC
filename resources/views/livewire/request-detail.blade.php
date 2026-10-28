<div class="space-y-6 max-w-5xl mx-auto font-heading">
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center gap-2" style="border-radius: 6px;">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-heading">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('status'))
        <div class="p-4 bg-blue-50 border border-blue-200 text-blue-950 font-bold text-xs flex items-center gap-2" style="border-radius: 6px;">
            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-heading">{{ session('status') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-900 font-bold text-xs flex items-center gap-2" style="border-radius: 6px;">
            <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span class="font-heading">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header — Large Maroon Box with White Montserrat Bold -->
    <div class="overflow-hidden bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
        <!-- Maroon Box -->
        <div class="p-8 md:p-10 text-white flex flex-col lg:flex-row lg:items-center justify-between gap-8 animate-header-glide" style="background-color: #800000;">
            <div class="space-y-4 flex-1 min-w-0">
                <!-- Meta row: control number + status -->
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold tracking-wide text-white border border-white/30" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; border-radius: 6px; background-color: rgba(255,255,255,0.14);">
                        {{ $request->status === App\Enums\RequestStatus::Approved ? $request->control_number : 'Control No. Pending' }}
                    </span>
                    <x-status-badge :status="$request->status" />
                </div>

                <!-- Title — Montserrat Bold 28-32px white -->
                <h1 class="font-heading font-bold text-white leading-tight" style="font-size: 28px; line-height: 1.2; font-weight: 700;">
                    {{ $request->title }}
                </h1>

                <!-- Investigator / Department — white + soft rose -->
                <div class="flex items-start gap-3 pt-1">
                    <div class="relative w-10 h-10 shrink-0 mt-0.5">
                        @if($request->student->avatar)
                            <img 
                                src="{{ $request->student->avatar }}" 
                                alt="{{ $request->student->name }}" 
                                referrerpolicy="no-referrer"
                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');"
                                class="w-10 h-10 rounded-full object-cover shrink-0 border-2 border-white/30" 
                            />
                        @endif
                        <div @class([
                            'w-10 h-10 rounded-full bg-white/15 border-2 border-white/30 text-white font-bold text-xs items-center justify-center shrink-0 select-none font-heading',
                            'hidden' => !empty($request->student->avatar),
                            'flex' => empty($request->student->avatar),
                        ])>
                            {{ $request->student->initials }}
                        </div>
                    </div>
                    <div class="space-y-1 text-sm leading-snug">
                        <p class="font-medium" style="color: #FECACA;">
                            Submitted by <strong class="font-bold text-white" style="font-weight: 700;">{{ $request->principal_investigator_display }}</strong>
                            <span class="font-normal" style="color: #FECACA;">({{ $request->student->email }})</span>
                            @if($request->co_authors)
                                <span class="block text-xs font-medium mt-1" style="color: #FECACA;">Co-Authors: {{ $request->co_authors }}</span>
                            @endif
                        </p>
                        <p class="font-medium" style="color: #FECACA;">
                            Department: <strong class="font-bold text-white">{{ $request->department->name }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action — white button on maroon (primary on dark) -->
            <div class="shrink-0 flex flex-col items-start lg:items-end gap-2">
                @if($request->certificate_path)
                    <a href="/requests/{{ $request->id }}/certificate" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-white font-bold text-xs transition-colors" style="color: #800000; border-radius: 6px; border: 1px solid #FFFFFF;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Download ERB Certificate</span>
                    </a>
                @elseif($request->host_country_cert_path)
                    <a href="/requests/{{ $request->id }}/host-country-certificate" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 bg-white font-bold text-xs transition-colors" style="color: #800000; border-radius: 6px; border: 1px solid #FFFFFF;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Download Host Country Requirement</span>
                    </a>
                @elseif(auth()->id() !== $request->student_id && (auth()->user()->hasAnyRole(['committee', 'admin']) || auth()->user()->is_chairperson || auth()->user()->is_developer))
                    @php $canApprove = $this->canApproveRequest(); @endphp
                    <button 
                        wire:click="openSignatureModal" 
                        @class([
                            'inline-flex items-center gap-2 px-5 py-3 bg-white font-bold text-xs transition-colors',
                            'animate-attention' => $canApprove,
                            'opacity-50 cursor-not-allowed' => !$canApprove
                        ])
                        style="color: #800000; border-radius: 6px; border: 1px solid #FFFFFF;"
                        @if(!$canApprove) disabled title="All required documents must be individually approved before final approval." @endif
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Approve Ethics Request & Sign Certificate</span>
                    </button>
                    @if(!$canApprove && $request->status !== App\Enums\RequestStatus::Approved)
                        <p class="text-xs font-medium max-w-xs text-left lg:text-right" style="color: #FECACA;">
                            Final approval disabled until all required documents pass review.
                        </p>
                    @endif

                    @if(!in_array($request->status, [App\Enums\RequestStatus::Approved, App\Enums\RequestStatus::Disapproved]))
                        <button 
                            type="button"
                            wire:click="openDisapproveModal" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-950/60 hover:bg-red-950 text-red-100 border border-red-300/40 font-bold text-xs rounded transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Disapprove Protocol</span>
                        </button>
                    @endif
                @endif
            </div>
        </div>

        <div class="p-6 md:p-8 space-y-6 bg-white">
            <!-- Progress Stepper — flat container -->
            <x-request-progress-stepper :request="$request" />

            <!-- Dates & Summary — neutral-50 cards, hairline -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div class="p-4" style="background-color: #F8F7F6; border: 1px solid #E4E1DE; border-radius: 6px;">
                    <span class="font-semibold block uppercase tracking-wide" style="color: #6B6864; font-size: 11px;">Date Submitted</span>
                    <span class="font-bold block mt-1" style="color: #231F1C; font-size: 13px;">{{ $request->created_at->format('F d, Y') }}</span>
                </div>
                <div class="p-4" style="background-color: #F8F7F6; border: 1px solid #E4E1DE; border-radius: 6px;">
                    <span class="font-semibold block uppercase tracking-wide" style="color: #6B6864; font-size: 11px;">Approval Date</span>
                    <span class="font-bold block mt-1" style="color: #231F1C; font-size: 13px;">{{ $request->approval_date ? $request->approval_date->format('F d, Y') : 'N/A' }}</span>
                </div>
                <div class="p-4" style="background-color: #F8F7F6; border: 1px solid #E4E1DE; border-radius: 6px;">
                    <div class="flex items-center gap-1.5">
                        <span class="font-semibold block uppercase tracking-wide" style="color: #6B6864; font-size: 11px;">Certificate Expiry</span>
                        <div class="group relative inline-flex items-center">
                            <span class="w-4 h-4 rounded-full flex items-center justify-center cursor-help shrink-0" style="background-color: #E4E1DE; color: #6B6864;" title="Valid for one year from official issuance date">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-3 text-white text-xs font-medium z-30 text-center pointer-events-none" style="background-color: #231F1C; border-radius: 6px; line-height: 1.4;">
                                Valid for one year from official issuance date
                                <div class="w-2 h-2 rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2" style="background-color: #231F1C;"></div>
                            </div>
                        </div>
                    </div>
                    <span class="font-bold block mt-1" style="color: #231F1C; font-size: 13px;">{{ $request->expiry_date ? $request->expiry_date->format('F d, Y') : 'N/A' }}</span>
                </div>
                <div class="p-4" style="background-color: #F8F7F6; border: 1px solid #E4E1DE; border-radius: 6px;">
                    <span class="font-semibold block uppercase tracking-wide" style="color: #6B6864; font-size: 11px;">Total Documents</span>
                    <span class="font-bold block mt-1" style="color: #231F1C; font-size: 13px;">{{ $request->applicable_documents_count }} Files Submitted</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Secretary Screening Panel -->
    @if(in_array($request->status, [\App\Enums\RequestStatus::Submitted, \App\Enums\RequestStatus::Screening]) && auth()->id() !== $request->student_id && $this->canScreen())
        <div class="p-6 space-y-6 bg-white border border-neutral-200 rounded-lg">
            <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Secretary Screening &amp; Intake Verification
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Verify document completeness and enter Turnitin / AI similarity report metrics before routing to the Chairperson.
                    </p>
                </div>
            </div>

            <!-- Metadata Cards (Flat tinted cards without redundant outer border) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Research Domain</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        {{ $request->research_domain ? $request->research_domain->label() : 'Not specified' }}
                    </span>
                </div>
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Study Site Locality</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        @if($request->is_local)
                            Local (Philippines)
                        @else
                            International ({{ $request->host_country ?? 'Overseas' }})
                        @endif
                    </span>
                </div>
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Document Checklist</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        {{ $request->applicable_documents_count }} Submitted File(s)
                    </span>
                </div>
            </div>

            <!-- Screening Form -->
            <div class="space-y-4 pt-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold uppercase text-gray-700">
                            Plagiarism Similarity Score (%) <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                wire:model="plagiarism_similarity_score" 
                                placeholder="e.g. 12.50" 
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading pr-8"
                            />
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">%</span>
                        </div>
                        <p class="text-[11px] text-gray-500">From official Turnitin / anti-plagiarism report.</p>
                        @error('plagiarism_similarity_score') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold uppercase text-gray-700">
                            AI Similarity Score (%) <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                max="100" 
                                wire:model="ai_similarity_score" 
                                placeholder="e.g. 2.00" 
                                class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading pr-8"
                            />
                            <span class="absolute right-3 top-2.5 text-xs text-gray-400 font-bold">%</span>
                        </div>
                        <p class="text-[11px] text-gray-500">From official AI content detection report.</p>
                        @error('ai_similarity_score') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase text-gray-700">
                        Screening Notes / Justification Remarks
                    </label>
                    <textarea 
                        wire:model="screening_notes" 
                        rows="3" 
                        placeholder="Provide comments or list missing/incomplete requirements if returning for revision..."
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                    ></textarea>
                    <p class="text-[11px] text-gray-500">Required when returning dossier for applicant revision (minimum 5 characters).</p>
                    @error('screening_notes') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Action Buttons -->
                <div class="pt-3 border-t border-gray-200 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button 
                        type="button" 
                        wire:click="returnScreeningRevision" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-200 rounded-lg font-bold text-xs transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-orange-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z"/>
                        </svg>
                        <span wire:loading.remove wire:target="returnScreeningRevision">Return for Screening Revision</span>
                        <span wire:loading wire:target="returnScreeningRevision">Processing Return...</span>
                    </button>

                    <button 
                        type="button" 
                        wire:click="passScreening" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                    >
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span wire:loading.remove wire:target="passScreening">Approve Screening &amp; Forward to Chair</span>
                        <span wire:loading wire:target="passScreening">Approving &amp; Routing...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Chairperson Review Routing Panel -->
    @if($request->status === \App\Enums\RequestStatus::ReviewRouting && auth()->id() !== $request->student_id && $this->isChairpersonOrAdmin())
        <div class="p-6 space-y-6 bg-white shadow-xs border border-neutral-200 rounded-lg">
            <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Chairperson Review Routing &amp; Domain Jurisdiction
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Determine the ethical review level based on research risk, or determine if protocol is outside ERB jurisdiction.
                    </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-blue-800 border border-blue-200 self-start sm:self-auto font-heading">
                    Routing Stage
                </span>
            </div>

            <!-- Protocol Assessment Summary (Flat tinted cards without redundant outer border) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Research Domain</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        {{ $request->research_domain ? $request->research_domain->label() : 'Not specified' }}
                    </span>
                </div>
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Study Locality</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        @if($request->is_local)
                            Local (Philippines)
                        @else
                            International ({{ $request->host_country ?? 'Overseas' }})
                        @endif
                    </span>
                </div>
                <div class="p-3 bg-gray-50/80 rounded-lg space-y-1">
                    <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide block">Screening Metrics</span>
                    <span class="font-bold text-gray-900 text-sm block">
                        Plagiarism: {{ $request->plagiarism_similarity_score ?? '0.00' }}% &bull; AI: {{ $request->ai_similarity_score ?? '0.00' }}%
                    </span>
                </div>
            </div>

            <!-- Routing Actions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- Expedited Review Option -->
                <div class="p-5 border border-gray-200 rounded-lg bg-gray-50/50 flex flex-col justify-between space-y-4 hover:border-gray-300 transition-colors">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 font-heading">Expedited Review</h4>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            For non-invasive research or protocols involving no greater than minimal risk to participants. Protocol will be assigned to two independent domain experts.
                        </p>
                    </div>
                    <button 
                        type="button" 
                        wire:click="routeReviewType('expedited_review')" 
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-maroon-primary hover:bg-maroon-dark text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                    >
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Route to Expedited Review</span>
                    </button>
                </div>

                <!-- Full Committee Review Option -->
                <div class="p-5 border border-gray-200 rounded-lg bg-gray-50/50 flex flex-col justify-between space-y-4 hover:border-gray-300 transition-colors">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-900 font-heading">Full Committee Review</h4>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            For invasive procedures, vulnerable populations, clinical trials, or sensitive personal data. Requires convened ERB quarterly meeting deliberation.
                        </p>
                    </div>
                    <button 
                        type="button" 
                        wire:click="routeReviewType('full_review')" 
                        wire:loading.attr="disabled"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#231F1C] hover:bg-black text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                    >
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Route to Full Committee Review</span>
                    </button>
                </div>
            </div>

            <!-- Out of Scope (STOP) Section -->
            <div class="p-4 bg-red-50/50 border border-red-200 rounded-lg space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-red-950 uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Out of Scope / Non-Human Subjects (STOP)
                        </h4>
                        <p class="text-xs text-red-800 mt-0.5">
                            If this research does not involve human subjects, or falls completely outside ERB jurisdiction, issue an Out of Scope determination.
                        </p>
                    </div>
                </div>

                <div class="space-y-2 pt-1">
                    <textarea 
                        wire:model="out_of_scope_reason" 
                        rows="2" 
                        placeholder="State official reason why this protocol is outside ERB jurisdiction..." 
                        class="w-full px-3 py-2 bg-white border border-red-200 rounded-lg text-xs focus:ring-2 focus:ring-red-600 focus:outline-none font-heading"
                    ></textarea>
                    @error('out_of_scope_reason') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                    
                    <div class="flex justify-end">
                        <button 
                            type="button" 
                            wire:click="markOutOfScope" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                        >
                            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span wire:loading.remove wire:target="markOutOfScope">Confirm Out of Scope Determination</span>
                            <span wire:loading wire:target="markOutOfScope">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Full Review Deliberation Panel -->
    @if($request->status === \App\Enums\RequestStatus::FullReviewPending && auth()->id() !== $request->student_id && (auth()->user()?->hasAnyRole(['committee', 'admin', 'secretary']) || auth()->user()?->is_chairperson || auth()->user()?->is_developer))
        <div class="p-6 space-y-5 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
            <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Full Review — Quarterly ERB Meeting Deliberation
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        This protocol has been routed to Full Committee Review. Deliberation is conducted during convened ERB meetings.
                    </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded bg-amber-50 text-amber-900 border border-amber-200 self-start sm:self-auto font-heading">
                    Full Review Pending
                </span>
            </div>

            @if($this->isChairpersonOrAdmin())
                <div class="space-y-3">
                    <label class="block text-xs font-semibold uppercase text-gray-700">
                        Quarterly Meeting Deliberation Notes &amp; Committee Action *
                    </label>
                    <textarea 
                        wire:model="deliberation_notes" 
                        rows="4" 
                        placeholder="Record the official committee deliberation summary, recommendations, consensus, or meeting date..."
                        class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                    ></textarea>
                    @error('deliberation_notes') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror

                    <div class="flex justify-end pt-1">
                        <button 
                            type="button" 
                            wire:click="completeFullReviewDeliberation" 
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#800000] hover:bg-[#660000] text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                        >
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span wire:loading.remove wire:target="completeFullReviewDeliberation">Save Deliberation Notes</span>
                            <span wire:loading wire:target="completeFullReviewDeliberation">Saving...</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-xs space-y-2">
                    <h4 class="font-semibold text-gray-700 uppercase tracking-wide text-[11px]">Deliberation Notes</h4>
                    <p class="text-gray-900 leading-relaxed whitespace-pre-line">
                        {{ $request->deliberation_notes ?: 'Deliberation notes have not yet been recorded by the ERB Chairperson.' }}
                    </p>
                </div>
            @endif
        </div>
    @endif

    <!-- Reviewer Assignment Panel -->
    @if($request->status === \App\Enums\RequestStatus::AssigningReviewers && auth()->id() !== $request->student_id && $this->isChairpersonOrAdmin())
        <div class="p-6 space-y-5 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
            <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Assign Domain Reviewers (Expedited Review)
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Select two independent committee members specialized in 
                        <strong>{{ $request->research_domain ? $request->research_domain->label() : 'the relevant field' }}</strong> 
                        to conduct the protocol review.
                    </p>
                </div>
            </div>

            <!-- Reviewer Selectors Form -->
            <div class="space-y-4 pt-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Reviewer 1 -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold uppercase text-gray-700">
                            Primary Domain Reviewer <span class="text-red-600">*</span>
                        </label>
                        <select 
                            wire:model="selected_reviewer_1" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                        >
                            <option value="">-- Choose Primary Reviewer --</option>
                            @if(isset($matchingReviewers) && $matchingReviewers->isNotEmpty())
                                <optgroup label="Domain Specialists ({{ $request->research_domain ? $request->research_domain->label() : 'Matching' }})">
                                    @foreach($matchingReviewers as $rev)
                                        <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->email }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(isset($otherCommitteeMembers) && $otherCommitteeMembers->isNotEmpty())
                                <optgroup label="Other Committee Members">
                                    @foreach($otherCommitteeMembers as $rev)
                                        <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->email }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        @error('selected_reviewer_1') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Reviewer 2 -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold uppercase text-gray-700">
                            Secondary Domain Reviewer <span class="text-red-600">*</span>
                        </label>
                        <select 
                            wire:model="selected_reviewer_2" 
                            class="w-full px-3.5 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                        >
                            <option value="">-- Choose Secondary Reviewer --</option>
                            @if(isset($matchingReviewers) && $matchingReviewers->isNotEmpty())
                                <optgroup label="Domain Specialists ({{ $request->research_domain ? $request->research_domain->label() : 'Matching' }})">
                                    @foreach($matchingReviewers as $rev)
                                        <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->email }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if(isset($otherCommitteeMembers) && $otherCommitteeMembers->isNotEmpty())
                                <optgroup label="Other Committee Members">
                                    @foreach($otherCommitteeMembers as $rev)
                                        <option value="{{ $rev->id }}">{{ $rev->name }} ({{ $rev->email }})</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        @error('selected_reviewer_2') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-200 flex justify-end">
                    <button 
                        type="button" 
                        wire:click="assignReviewers" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#800000] hover:bg-[#660000] text-white rounded-lg font-bold text-xs transition-colors cursor-pointer shadow-xs"
                    >
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span wire:loading.remove wire:target="assignReviewers">Confirm &amp; Dispatch Invitations</span>
                        <span wire:loading wire:target="assignReviewers">Dispatching Invitations...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Appointed Reviewers Status List (shown when under review or assignments exist, committee/admin/chair only) -->
    @if($request->reviewerAssignments->isNotEmpty() && auth()->id() !== $request->student_id && (auth()->user()?->hasAnyRole(['committee', 'admin', 'secretary']) || auth()->user()?->is_chairperson || auth()->user()?->is_developer))
        <div class="p-6 space-y-4 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
            <div class="pb-3 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Appointed Domain Reviewers
                    </h3>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Independent expert evaluation tracking for this protocol.
                    </p>
                </div>
                <span class="text-xs font-bold text-gray-700 font-heading">
                    {{ $request->reviewerAssignments->where('status', \App\Enums\ReviewerAssignmentStatus::Completed)->count() }} of {{ $request->reviewerAssignments->count() }} Completed
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($request->reviewerAssignments as $idx => $assignment)
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Reviewer #{{ $idx + 1 }}</span>
                                <span class="font-bold text-gray-900 text-sm block">{{ $assignment->user->name ?? 'Reviewer' }}</span>
                                <span class="text-xs text-gray-500 block">{{ $assignment->user->email ?? '' }}</span>
                            </div>
                            <span @class([
                                'px-2 py-0.5 text-xs font-semibold rounded font-heading border',
                                'bg-blue-50 text-blue-800 border-blue-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Invited,
                                'bg-amber-50 text-amber-800 border-amber-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Accepted,
                                'bg-red-50 text-red-800 border-red-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Declined,
                                'bg-emerald-50 text-emerald-800 border-emerald-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Completed,
                            ])>
                                {{ $assignment->status->label() }}
                            </span>
                        </div>

                        <div class="text-[11px] text-gray-500 space-y-0.5 pt-1 border-t border-gray-200">
                            <div>Invited: {{ $assignment->invited_at ? $assignment->invited_at->format('M d, Y h:i A') : 'N/A' }}</div>
                            @if($assignment->responded_at)
                                <div>Responded: {{ $assignment->responded_at->format('M d, Y h:i A') }}</div>
                            @endif
                            @if($assignment->completed_at)
                                <div>Completed: {{ $assignment->completed_at->format('M d, Y h:i A') }}</div>
                            @endif
                            @if($assignment->decline_reason)
                                <div class="text-red-700 italic">Decline reason: {{ $assignment->decline_reason }}</div>
                            @endif
                            @if($assignment->recommendation)
                                <div class="font-semibold text-gray-800">Recommendation: {{ $assignment->recommendation }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Assigned Reviewer Action Module -->
    @php
        $myReviewAssignment = $request->reviewerAssignments->firstWhere('user_id', auth()->id());
    @endphp

    @if($myReviewAssignment && auth()->id() !== $request->student_id && $this->canReview())
        @if($myReviewAssignment->status === \App\Enums\ReviewerAssignmentStatus::Invited)
            <!-- Review Invitation Banner -->
            <div class="p-6 space-y-4 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center shrink-0 text-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-gray-900 text-base font-heading">Committee Review Invitation</h3>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded font-heading bg-blue-50 text-blue-800 border border-blue-200">
                                    Action Required
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-medium mt-0.5">
                                You have been appointed by the ERB Chairperson to serve as an independent domain reviewer for this ethics protocol.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button 
                            type="button" 
                            wire:click="openDeclineModal({{ $myReviewAssignment->id }})"
                            class="px-3.5 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer font-heading transition"
                        >
                            Decline Invitation
                        </button>
                        <button 
                            type="button" 
                            wire:click="acceptReviewInvitation({{ $myReviewAssignment->id }})"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-xs font-bold text-white rounded-lg cursor-pointer font-heading flex items-center gap-1.5 transition disabled:opacity-50"
                            style="background-color: #800000;"
                        >
                            <svg wire:loading wire:target="acceptReviewInvitation({{ $myReviewAssignment->id }})" class="w-3.5 h-3.5 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="acceptReviewInvitation({{ $myReviewAssignment->id }})">Accept Invitation</span>
                            <span wire:loading wire:target="acceptReviewInvitation({{ $myReviewAssignment->id }})">Accepting...</span>
                        </button>
                    </div>
                </div>
            </div>
        @elseif($myReviewAssignment->status === \App\Enums\ReviewerAssignmentStatus::Accepted)
            <!-- Reviewer Assessment & Dual Upload Form -->
            <div class="p-6 space-y-5 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
                <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-base font-heading flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Reviewer Assessment & Dual Form Submission
                            </h3>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded font-heading bg-amber-50 text-amber-800 border border-amber-200">
                                Review In Progress
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            Download official assessment templates, conduct your independent appraisal, and upload both completed assessment forms with your recommendation.
                        </p>
                    </div>
                </div>

                <!-- Official Template Downloads -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-2.5">
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wide block font-heading">Official Assessment Form Templates</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a 
                            href="/templates/assessment-consent/download" 
                            target="_blank"
                            class="flex items-center justify-between p-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition group"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div class="truncate">
                                    <span class="text-xs font-bold text-gray-900 block truncate group-hover:text-blue-600">Informed Consent Assessment Form</span>
                                    <span class="text-[10px] text-gray-500 block">DEMO_ERB_Form_Informed_Consent_Assessment.docx</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>

                        <a 
                            href="/templates/assessment-protocol/download" 
                            target="_blank"
                            class="flex items-center justify-between p-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition group"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div class="truncate">
                                    <span class="text-xs font-bold text-gray-900 block truncate group-hover:text-blue-600">Study Protocol Assessment Form</span>
                                    <span class="text-[10px] text-gray-500 block">DEMO_ERB_Form_Protocol_Assessment.docx</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Dual Upload Dropzones -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Form 1: Consent Assessment -->
                    <div class="p-4 border border-gray-200 rounded-lg space-y-2 bg-white">
                        <label class="block text-xs font-bold text-gray-900 font-heading">
                            1. Filled Informed Consent Assessment <span class="text-red-600">*</span>
                        </label>
                        <p class="text-[11px] text-gray-500">Upload completed consent assessment (PDF, DOC, DOCX up to 10MB).</p>
                        
                        <div class="relative">
                            <input 
                                type="file" 
                                wire:model="assessment_consent_file" 
                                id="consent_upload_{{ $myReviewAssignment->id }}"
                                class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-maroon-800"
                                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            />
                        </div>
                        <div wire:loading wire:target="assessment_consent_file" class="text-xs text-amber-700 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Uploading Consent Assessment...
                        </div>
                        @if($assessment_consent_file && !$errors->has('assessment_consent_file'))
                            <div class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Ready: {{ is_string($assessment_consent_file) ? $assessment_consent_file : $assessment_consent_file->getClientOriginalName() }}</span>
                            </div>
                        @endif
                        @error('assessment_consent_file') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Form 2: Protocol Assessment -->
                    <div class="p-4 border border-gray-200 rounded-lg space-y-2 bg-white">
                        <label class="block text-xs font-bold text-gray-900 font-heading">
                            2. Filled Protocol Assessment <span class="text-red-600">*</span>
                        </label>
                        <p class="text-[11px] text-gray-500">Upload completed protocol assessment (PDF, DOC, DOCX up to 10MB).</p>
                        
                        <div class="relative">
                            <input 
                                type="file" 
                                wire:model="assessment_protocol_file" 
                                id="protocol_upload_{{ $myReviewAssignment->id }}"
                                class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-maroon-800"
                                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            />
                        </div>
                        <div wire:loading wire:target="assessment_protocol_file" class="text-xs text-amber-700 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Uploading Protocol Assessment...
                        </div>
                        @if($assessment_protocol_file && !$errors->has('assessment_protocol_file'))
                            <div class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Ready: {{ is_string($assessment_protocol_file) ? $assessment_protocol_file : $assessment_protocol_file->getClientOriginalName() }}</span>
                            </div>
                        @endif
                        @error('assessment_protocol_file') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Recommendation Radio Options -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-900 font-heading">
                        Official Reviewer Recommendation <span class="text-red-600">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $assessment_recommendation === 'approved' ? 'border-emerald-500 bg-emerald-50/40 ring-1 ring-emerald-500' : 'border-gray-300' }}">
                            <input type="radio" wire:model="assessment_recommendation" value="approved" class="text-emerald-700 focus:ring-emerald-700" />
                            <div class="ml-2.5">
                                <span class="text-xs font-bold text-gray-900 block font-heading">Approve</span>
                                <span class="text-[11px] text-gray-500 block">Ethical criteria fully satisfied</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $assessment_recommendation === 'revisions_required' ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500' : 'border-gray-300' }}">
                            <input type="radio" wire:model="assessment_recommendation" value="revisions_required" class="text-amber-700 focus:ring-amber-700" />
                            <div class="ml-2.5">
                                <span class="text-xs font-bold text-gray-900 block font-heading">Revisions Required</span>
                                <span class="text-[11px] text-gray-500 block">Modifications needed before clearance</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-3.5 border rounded-lg cursor-pointer transition hover:bg-gray-50 {{ $assessment_recommendation === 'disapproved' ? 'border-red-500 bg-red-50/40 ring-1 ring-red-500' : 'border-gray-300' }}">
                            <input type="radio" wire:model="assessment_recommendation" value="disapproved" class="text-red-700 focus:ring-red-700" />
                            <div class="ml-2.5">
                                <span class="text-xs font-bold text-gray-900 block font-heading">Disapprove</span>
                                <span class="text-[11px] text-gray-500 block">Protocol poses unacceptable risks</span>
                            </div>
                        </label>
                    </div>
                    @error('assessment_recommendation') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                </div>

                <!-- Reviewer Notes -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-900 font-heading">
                        Reviewer Remarks / Notes for Committee &amp; Chairperson (Optional)
                    </label>
                    <textarea 
                        wire:model="assessment_notes" 
                        rows="3" 
                        placeholder="Enter substantive comments, specific ethical observations, or required amendments..."
                        class="w-full p-3 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"
                    ></textarea>
                    @error('assessment_notes') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Evaluation Action -->
                <div class="flex items-center justify-end pt-2 border-t border-gray-200">
                    <button 
                        type="button" 
                        wire:click="submitReviewerEvaluation({{ $myReviewAssignment->id }})"
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-xs font-bold text-white rounded-lg cursor-pointer font-heading flex items-center gap-2 shadow-xs transition disabled:opacity-50"
                        style="background-color: #800000;"
                    >
                        <svg wire:loading wire:target="submitReviewerEvaluation" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="submitReviewerEvaluation">Submit Independent Evaluation</span>
                        <span wire:loading wire:target="submitReviewerEvaluation">Submitting Evaluation...</span>
                    </button>
                </div>
            </div>
        @elseif($myReviewAssignment->status === \App\Enums\ReviewerAssignmentStatus::Completed)
            <!-- Completed Summary Card -->
            <div class="p-6 space-y-4 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
                <div class="pb-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base font-heading">Your Review Evaluation</h3>
                            <p class="text-xs text-gray-500 font-medium">Completed on {{ $myReviewAssignment->completed_at ? $myReviewAssignment->completed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded font-heading bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Completed
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-lg space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Your Recommendation</span>
                        <span class="font-bold text-gray-900 capitalize text-sm">{{ str_replace('_', ' ', $myReviewAssignment->recommendation ?? 'N/A') }}</span>
                    </div>

                    <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-lg space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Assessment Documents</span>
                        <div class="space-y-0.5 text-gray-700 font-medium">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Informed Consent Assessment submitted</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Protocol Assessment submitted</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($myReviewAssignment->reviewer_notes)
                    <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-lg space-y-1 text-xs">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Confidential Notes / Remarks</span>
                        <p class="text-gray-800 font-medium">{{ $myReviewAssignment->reviewer_notes }}</p>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- Chairperson Final Decision Panel (Phase 4) -->
    @if(($request->status === \App\Enums\RequestStatus::UnderReview || $request->status === \App\Enums\RequestStatus::ReviewRouting) && auth()->id() !== $request->student_id && $this->canMakeChairDecision())
        <div class="p-6 space-y-5 bg-white shadow-xs" style="border: 1px solid #E4E1DE; border-radius: 8px;">
            <div class="pb-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-red-50 border border-red-200 flex items-center justify-center shrink-0" style="color: #800000;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-base font-heading">Chairperson Final Decision &amp; Protocol Determination</h3>
                        </div>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            Synthesize dual domain reviewer evaluations and execute the final institutional ethics determination.
                        </p>
                    </div>
                </div>
                <div class="text-xs text-left sm:text-right">
                    <span class="text-gray-500 font-medium block">Protocol Jurisdiction:</span>
                    <span class="font-bold text-gray-900 font-heading block">
                        @if($request->is_local)
                            Local (Philippines)
                        @else
                            International ({{ $request->host_country ?? 'Host Country' }})
                        @endif
                    </span>
                </div>
            </div>

            <!-- Dual Reviewer Summary Grid -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block font-heading">Independent Reviewer Evaluations Summary</span>
                @if($request->reviewerAssignments->isEmpty())
                    <p class="text-xs text-gray-500 italic p-3 bg-gray-50 rounded-lg border border-gray-200">No domain reviewers appointed yet.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($request->reviewerAssignments as $idx => $assignment)
                            <div class="p-3.5 bg-gray-50 border border-gray-200 rounded-lg space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-900 font-heading">Reviewer #{{ $idx + 1 }}: {{ $assignment->user->name ?? 'Reviewer' }}</span>
                                    <span @class([
                                        'px-2 py-0.5 text-[11px] font-semibold rounded border font-heading',
                                        'bg-blue-50 text-blue-800 border-blue-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Invited,
                                        'bg-amber-50 text-amber-800 border-amber-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Accepted,
                                        'bg-red-50 text-red-800 border-red-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Declined,
                                        'bg-emerald-50 text-emerald-800 border-emerald-200' => $assignment->status === \App\Enums\ReviewerAssignmentStatus::Completed,
                                    ])>
                                        {{ $assignment->status->label() }}
                                    </span>
                                </div>
                                @if($assignment->status === \App\Enums\ReviewerAssignmentStatus::Completed)
                                    <div class="space-y-1">
                                        <div>
                                            <span class="text-gray-500 font-medium">Recommendation:</span>
                                            <span class="font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $assignment->recommendation ?? 'N/A') }}</span>
                                        </div>
                                        @if($assignment->reviewer_notes)
                                            <div class="p-2 bg-white rounded border border-gray-200 text-gray-700 italic text-[11px]">
                                                "{{ $assignment->reviewer_notes }}"
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-500 italic text-[11px]">Evaluation pending submission.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Conditional Decision Actions -->
            <div class="pt-4 border-t border-gray-200 space-y-4">
                @if($request->is_local)
                    <!-- Local Protocol Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Option A: Approve & Issue Certificate -->
                        <div class="p-4 border border-emerald-200 bg-emerald-50/40 rounded-lg space-y-3 flex flex-col justify-between">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block font-heading">Decision 1 &bull; Full Institutional Approval</span>
                                <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                                    Approve the ethics protocol, allocate an official ERB control number, and generate the signed Certificate of Approval.
                                </p>
                            </div>
                            <div>
                                <button 
                                    type="button" 
                                    wire:click="openChairApprovalModal"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs font-heading cursor-pointer transition shadow-xs"
                                >
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Approve &amp; Issue Certificate</span>
                                </button>
                            </div>
                        </div>

                        <!-- Option B: Request Revisions (Connector D) -->
                        <div class="p-4 border border-rose-200 bg-rose-50/40 rounded-lg space-y-3">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-rose-900 uppercase tracking-wider block font-heading">Decision 2 &bull; Request Revisions</span>
                                <p class="text-xs text-rose-800 font-medium leading-relaxed">
                                    Return the protocol to the researcher with specific revision guidelines and amendments.
                                </p>
                            </div>
                            <div class="space-y-2">
                                <textarea 
                                    wire:model="decision_revision_notes" 
                                    rows="2" 
                                    placeholder="Specify revision instructions (e.g. revise consent form, clarify risk mitigation)..." 
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-heading focus:ring-2 focus:ring-maroon-800 focus:outline-none"
                                ></textarea>
                                @error('decision_revision_notes') <span class="text-xs text-red-600 font-medium block">{{ $message }}</span> @enderror
                                <button 
                                    type="button" 
                                    wire:click="requestLocalRevisions"
                                    wire:loading.attr="disabled"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg font-bold text-xs font-heading cursor-pointer transition shadow-xs disabled:opacity-50"
                                >
                                    <svg wire:loading wire:target="requestLocalRevisions" class="w-3.5 h-3.5 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="requestLocalRevisions">Request Revisions</span>
                                    <span wire:loading wire:target="requestLocalRevisions">Submitting Revisions...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Non-Local (International) Protocol Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Option A: Approve & Issue Certificate -->
                        <div class="p-4 border border-emerald-200 bg-emerald-50/40 rounded-lg space-y-3 flex flex-col justify-between">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block font-heading">Decision 1 &bull; Full Institutional Approval</span>
                                <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                                    Approve international study protocol and generate official Certificate of Approval.
                                </p>
                            </div>
                            <div>
                                <button 
                                    type="button" 
                                    wire:click="openChairApprovalModal"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs font-heading cursor-pointer transition shadow-xs"
                                >
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Approve &amp; Issue Certificate</span>
                                </button>
                            </div>
                        </div>

                        <!-- Option B: Issue Host Country Requirement -->
                        <div class="p-4 border border-purple-200 bg-purple-50/40 rounded-lg space-y-3 flex flex-col justify-between">
                            <div class="space-y-1">
                                <span class="text-xs font-bold text-purple-900 uppercase tracking-wider block font-heading">Decision 2 &bull; Require Host Country Review</span>
                                <p class="text-xs text-purple-800 font-medium leading-relaxed">
                                    Certify that this protocol conducts research in <strong>{{ $request->host_country ?? 'a foreign host country' }}</strong> and must undergo local institutional ethics review before DEMO endorsement.
                                </p>
                            </div>
                            <div>
                                <button 
                                    type="button" 
                                    wire:click="openHostCountryModal"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-bold text-xs font-heading cursor-pointer transition shadow-xs"
                                >
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Issue Requirement to Review in Host Countries</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Required Documents List — flat white card -->
    <div class="p-6 space-y-4 bg-white" style="border: 1px solid #E4E1DE; border-radius: 8px;">
        <div class="pb-3 space-y-1" style="border-bottom: 1px solid #E4E1DE;">
            <h3 class="font-bold" style="color: #231F1C; font-size: 16px; font-weight: 700;">Submitted Required Documents</h3>
            <p class="text-xs font-medium" style="color: #6B6864;">Each document is individually versioned and must be approved by the committee.</p>
        </div>

        <div class="space-y-4" data-auto-animate>
            @foreach($request->requestDocuments as $doc)
                <div @class([
                    'p-5 space-y-3 transition-colors',
                    'bg-orange-50/60 border-orange-200' => $doc->status === App\Enums\DocumentStatus::RevisionsRequested,
                    'bg-emerald-50/40 border-emerald-200' => $doc->status === App\Enums\DocumentStatus::Approved,
                    'bg-[#F8F7F6] border-[#E4E1DE]' => $doc->status !== App\Enums\DocumentStatus::RevisionsRequested && $doc->status !== App\Enums\DocumentStatus::Approved,
                ]) style="border-width: 1px; border-radius: 6px;">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h4 class="font-bold text-sm leading-snug" style="color: #231F1C; font-weight: 700;">
                            {{ $doc->documentType->name }}
                            @if(!$doc->documentType->is_required)
                                <span class="text-xs font-normal ml-1" style="color: #6B6864;">(Optional)</span>
                            @endif
                        </h4>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($doc->isResubmission() && $doc->status === \App\Enums\DocumentStatus::Pending)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold uppercase" style="background-color: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; border-radius: 6px;">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Resubmitted (Version {{ $doc->latestVersion->version_number }})</span>
                                </span>
                            @endif
                            @if(!$doc->latestVersion && !$doc->documentType->is_required)
                                <span class="px-3 py-1 font-bold text-xs uppercase" style="background-color: #F3F4F6; color: #6B6864; border: 1px solid #E4E1DE; border-radius: 9999px;">
                                    Optional &bull; Not Submitted
                                </span>
                            @else
                                <x-status-badge :status="$doc->status" />
                            @endif
                        </div>
                    </div>
                    @if($doc->latestVersion)
                        <p class="text-xs font-medium leading-relaxed flex flex-wrap items-center gap-1.5" style="color: #6B6864;">
                            <span>Current Version: <strong class="font-bold" style="color: #231F1C; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">Version {{ $doc->latestVersion->version_number }}</strong></span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center font-bold text-xs px-2 py-1" style="color: #800000; background-color: #FFF5F5; border: 1px solid #FECACA; border-radius: 6px;">
                                Uploaded {{ $doc->latestVersion->created_at->diffForHumans() }}
                            </span>
                            @if($doc->latestVersion->change_notes)
                                <span>&bull;</span>
                                <span class="italic font-normal" style="color: #6B6864;">"{{ $doc->latestVersion->change_notes }}"</span>
                            @endif
                        </p>
                    @else
                        @if($doc->documentType->is_required)
                            <p class="text-xs font-bold" style="color: #DC2626;">No version uploaded yet.</p>
                        @else
                            <p class="text-xs font-medium italic" style="color: #6B6864;">No optional file was submitted with this application.</p>
                        @endif
                    @endif

                    <div class="pt-1 flex items-center justify-start">
                        @if($doc->latestVersion)
                            @if(auth()->id() === $request->student_id && $doc->status === App\Enums\DocumentStatus::RevisionsRequested)
                                <a href="/requests/{{ $request->id }}/documents/{{ $doc->id }}?action=revision" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold text-xs transition-colors w-full sm:w-auto" style="background-color: #EA580C; border-radius: 6px;">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Upload Revised Version</span>
                                </a>
                            @else
                                <a href="/requests/{{ $request->id }}/documents/{{ $doc->id }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 font-bold text-xs transition-colors w-full sm:w-auto bg-white" style="color: #231F1C; border: 1px solid #E4E1DE; border-radius: 6px;">
                                    <span>View & Review Document</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Activity Log — flat -->
    <div class="p-6 space-y-4 bg-white" style="border: 1px solid #E4E1DE; border-radius: 8px;">
        <h3 class="font-bold pb-3" style="color: #231F1C; font-size: 16px; font-weight: 700; border-bottom: 1px solid #E4E1DE;">Status Log & Activity History</h3>
        <div class="space-y-3" data-auto-animate>
            @forelse($request->statusLogs as $log)
                <div class="flex items-start gap-3 text-xs">
                    <div class="w-2 h-2 rounded-full shrink-0 mt-2" style="background-color: #800000;"></div>
                    <div class="flex-1 p-3" style="background-color: #F8F7F6; border: 1px solid #E4E1DE; border-radius: 6px;">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold" style="color: #231F1C;">{{ $log->user->name }}</span>
                            <span class="text-xs" style="color: #6B6864; font-family: ui-monospace, SFMono-Regular, monospace;">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <p class="mt-1 leading-relaxed" style="color: #6B6864;">{{ $log->notes }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs italic" style="color: #6B6864;">No activity logs recorded yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Live Signature Modal -->
    @if($showSignatureModal && auth()->id() !== $request->student_id)
        @teleport('body')
            <div 
                x-data="{
                    signaturePad: null,
                    signatureError: '',
                    initPad() {
                        setTimeout(() => {
                            const canvas = this.$el.querySelector('canvas');
                            if (!canvas) return;
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            const ctx = canvas.getContext('2d');
                            if (ctx) ctx.scale(ratio, ratio);
                            if (typeof SignaturePad !== 'undefined') {
                                this.signaturePad = new SignaturePad(canvas, {
                                    penColor: 'rgb(0, 0, 0)',
                                    backgroundColor: 'rgb(255, 255, 255)'
                                });
                            }
                        }, 50);
                    },
                    clearPad() {
                        this.signatureError = '';
                        if (this.signaturePad) {
                            this.signaturePad.clear();
                        }
                    },
                    confirmSignature() {
                        if (!this.signaturePad || this.signaturePad.isEmpty()) {
                            this.signatureError = 'Please draw your signature on the canvas before confirming.';
                            return;
                        }
                        this.signatureError = '';
                        const dataUrl = this.signaturePad.toDataURL('image/png');
                        $wire.approveWithSignature(dataUrl);
                    }
                }"
                x-init="initPad()"
                class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading"
            >
                <div class="bg-white max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-sm relative z-[100000] my-auto mx-auto border border-neutral-200 rounded-lg">
                    <div class="p-6 pb-4 flex items-center justify-between shrink-0 border-b border-neutral-200">
                        <div>
                            <h3 class="font-bold" style="color: #231F1C; font-size: 16px; font-weight: 700;">Approval Signature</h3>
                            <p class="text-xs font-medium" style="color: #6B6864;">Sign below to approve and generate the official Certificate of Approval.</p>
                        </div>
                        <button wire:click="closeSignatureModal" class="hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer" style="color: #6B6864;">&times;</button>
                    </div>

                    <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                        <div class="p-4 space-y-1" style="background-color: #FFFBEB; border: 1px solid #FDE68A; border-radius: 6px;">
                            <div class="flex items-center gap-2 font-bold text-xs" style="color: #92400E;">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Are you sure you want to approve this ethics review request?</span>
                            </div>
                            <p class="text-xs font-medium leading-relaxed" style="color: #92400E;">
                                Confirming your signature will officially mark this request as <strong class="font-bold uppercase" style="color: #166534;">Approved</strong>, assign ERB Protocol No. <strong class="font-bold" style="color: #231F1C; font-family: ui-monospace, monospace;">{{ $request->control_number ?? 'ERB2026_XXXXX' }}</strong>, and issue the official signed Certificate.
                            </p>
                        </div>

                        <div x-show="signatureError" x-cloak class="p-3 text-xs font-bold flex items-center gap-2" style="background-color: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; border-radius: 6px;">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="signatureError"></span>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide" style="color: #6B6864;">Draw Signature Below (creagia/laravel-sign-pad):</label>
                            <div class="p-2 flex justify-center overflow-hidden" style="border: 2px dashed #E4E1DE; border-radius: 6px; background-color: #F8F7F6;">
                                <x-laravel-sign-pad::signature-pad
                                    width="460"
                                    height="180"
                                    border-color="#E4E1DE"
                                    pad-classes="bg-white rounded border shadow-inner cursor-crosshair w-full h-40 sm:h-44"
                                    button-classes="hidden"
                                    :disabled-without-signature="false"
                                />
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs pt-1">
                                <span class="italic" style="color: #6B6864;">Signed by: {{ auth()->user()->name }} ({{ auth()->user()->title ?? 'Chairperson' }})</span>
                                <button type="button" @click="clearPad()" class="text-xs font-semibold cursor-pointer self-start sm:self-auto hover:underline" style="color: #991B1B;">Clear Canvas</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 shrink-0" style="border-top: 1px solid #E4E1DE;">
                        <button wire:click="closeSignatureModal" class="w-full sm:w-auto btn-secondary text-xs text-center justify-center">Cancel</button>
                        <button 
                            type="button" 
                            @click="confirmSignature()" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="w-full sm:w-auto text-xs bg-green-700 hover:bg-green-800 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 text-center text-white font-bold"
                            style="border-radius: 6px; padding: 10px 20px;"
                        >
                            <svg wire:loading wire:target="approveWithSignature" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="approveWithSignature">Confirm Signature & Issue Certificate</span>
                            <span wire:loading wire:target="approveWithSignature">Generating Certificate...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Disapprove Protocol Modal -->
    @if($showDisapproveModal && auth()->id() !== $request->student_id)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white max-w-lg w-full overflow-hidden shadow-sm relative z-[100000] my-auto mx-auto rounded-2xl border border-red-200 flex flex-col font-heading">
                    
                    <div class="p-6 pb-4 bg-maroon-primary text-white flex items-center justify-between shrink-0">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-200 block">Formal Committee Decision</span>
                            <h3 class="font-bold text-base text-white mt-0.5">Disapprove Ethics Protocol</h3>
                        </div>
                        <button type="button" wire:click="closeDisapproveModal" class="text-white/80 hover:text-white font-bold text-xl p-1 cursor-pointer">&times;</button>
                    </div>

                    <form wire:submit.prevent="confirmDisapprove" class="p-6 space-y-4">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl space-y-1 text-xs">
                            <p class="font-bold text-red-950">You are about to issue a formal Disapproval for:</p>
                            <p class="font-medium text-red-900">"{{ $request->title }}"</p>
                            <p class="text-[11px] text-red-700 mt-1">This will finalize the review, log an immutable audit trail entry, and notify the researcher by email with your explanation.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">Official Disapproval Justification / Remarks *</label>
                            <textarea 
                                wire:model="disapprovalRemarks" 
                                rows="4" 
                                placeholder="State clearly the ethical violations, methodological deficiencies, or non-compliance reasons for disapproving this protocol..."
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"
                            ></textarea>
                            @error('disapprovalRemarks') <p class="text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" wire:click="closeDisapproveModal" class="btn-secondary text-xs cursor-pointer">Cancel</button>
                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="confirmDisapprove"
                                class="btn-danger text-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="confirmDisapprove">Confirm Protocol Disapproval</span>
                                <span wire:loading wire:target="confirmDisapprove">Processing...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        @endteleport
    @endif

    <!-- Decline Review Invitation Modal -->
    @if($showDeclineModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white max-w-lg w-full overflow-hidden shadow-sm relative z-[100000] my-auto mx-auto rounded-2xl border border-neutral-200 flex flex-col font-heading">
                    
                    <div class="p-6 pb-4 bg-gray-900 text-white flex items-center justify-between shrink-0">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-300 block">Reviewer Response</span>
                            <h3 class="font-bold text-base text-white mt-0.5">Decline Review Invitation</h3>
                        </div>
                        <button type="button" wire:click="closeDeclineModal" class="text-white/80 hover:text-white font-bold text-xl p-1 cursor-pointer">&times;</button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-1 text-xs">
                            <p class="font-bold text-amber-950">You are declining the review invitation for:</p>
                            <p class="font-medium text-amber-900">"{{ $request->title }}"</p>
                            <p class="text-[11px] text-amber-700 mt-1">The ERB Chairperson will be notified to reassign another domain reviewer.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wide">Reason for Declining (Optional)</label>
                            <textarea 
                                wire:model="decline_reason" 
                                rows="3" 
                                placeholder="State reason (e.g., conflict of interest with investigator, workload/unavailability)..."
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"
                            ></textarea>
                            @error('decline_reason') <p class="text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" wire:click="closeDeclineModal" class="btn-secondary text-xs cursor-pointer">Cancel</button>
                            <button 
                                type="button" 
                                wire:click="declineReviewInvitation({{ $decliningAssignmentId ?? ($myReviewAssignment->id ?? 0) }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-700 hover:bg-red-800 rounded-lg cursor-pointer disabled:opacity-50 flex items-center gap-1.5"
                            >
                                <span wire:loading.remove wire:target="declineReviewInvitation">Confirm Decline</span>
                                <span wire:loading wire:target="declineReviewInvitation">Processing...</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endteleport
    @endif

    <!-- Chairperson Approval & Certificate Issuance Modal -->
    @if($showChairApprovalModal && auth()->id() !== $request->student_id)
        @teleport('body')
            <div 
                x-data="{
                    signaturePad: null,
                    signatureError: '',
                    initPad() {
                        setTimeout(() => {
                            const canvas = this.$el.querySelector('canvas');
                            if (!canvas) return;
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            const ctx = canvas.getContext('2d');
                            if (ctx) ctx.scale(ratio, ratio);
                            if (typeof SignaturePad !== 'undefined') {
                                this.signaturePad = new SignaturePad(canvas, {
                                    penColor: 'rgb(0, 0, 0)',
                                    backgroundColor: 'rgb(255, 255, 255)'
                                });
                            }
                        }, 50);
                    },
                    clearPad() {
                        this.signatureError = '';
                        if (this.signaturePad) {
                            this.signaturePad.clear();
                        }
                    },
                    confirmSignature() {
                        if (!this.signaturePad || this.signaturePad.isEmpty()) {
                            this.signatureError = 'Please draw your official signature on the canvas before confirming.';
                            return;
                        }
                        this.signatureError = '';
                        const dataUrl = this.signaturePad.toDataURL('image/png');
                        $wire.approveAndIssueCertificate(dataUrl);
                    }
                }"
                x-init="initPad()"
                class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading"
            >
                <div class="bg-white max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-sm relative z-[100000] my-auto mx-auto border border-neutral-200 rounded-lg">
                    <div class="p-6 pb-4 flex items-center justify-between shrink-0 border-b border-neutral-200">
                        <div>
                            <h3 class="font-bold text-base text-gray-900 font-heading">Chairperson Protocol Approval</h3>
                            <p class="text-xs text-gray-500 font-medium">Draw signature to approve protocol and issue the Certificate of Approval.</p>
                        </div>
                        <button wire:click="closeChairApprovalModal" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg space-y-1 text-xs">
                            <span class="font-bold text-emerald-900 block font-heading">Final Ethics Approval:</span>
                            <p class="text-emerald-800 leading-relaxed font-medium">
                                Protocol <strong>"{{ $request->title }}"</strong> will be set to <strong class="uppercase">Approved</strong>, receive an official ERB control number, and be registered in the Research Monitoring registry.
                            </p>
                        </div>

                        <div x-show="signatureError" x-cloak class="p-3 text-xs font-bold flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                            <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="signatureError"></span>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Chairperson Digital Signature:</label>
                            <div class="p-2 flex justify-center overflow-hidden bg-[#F8F7F6] border-2 border-dashed border-[#E4E1DE] rounded-lg">
                                <x-laravel-sign-pad::signature-pad
                                    width="460"
                                    height="180"
                                    border-color="#E4E1DE"
                                    pad-classes="bg-white rounded border shadow-inner cursor-crosshair w-full h-40 sm:h-44"
                                    button-classes="hidden"
                                    :disabled-without-signature="false"
                                />
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs pt-1">
                                <span class="italic text-gray-500">Signer: {{ auth()->user()->name }} (Chairperson / ERB)</span>
                                <button type="button" @click="clearPad()" class="text-xs font-semibold cursor-pointer text-red-700 hover:underline">Clear Canvas</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 shrink-0 border-t border-gray-200">
                        <button type="button" wire:click="closeChairApprovalModal" class="btn-secondary text-xs text-center justify-center cursor-pointer">Cancel</button>
                        <button 
                            type="button" 
                            @click="confirmSignature()" 
                            wire:loading.attr="disabled"
                            class="text-xs bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 px-5 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
                        >
                            <svg wire:loading wire:target="approveAndIssueCertificate" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="approveAndIssueCertificate">Confirm Approval &amp; Issue Certificate</span>
                            <span wire:loading wire:target="approveAndIssueCertificate">Issuing Certificate...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Chairperson Host Country Requirement Modal -->
    @if($showHostCountryModal && auth()->id() !== $request->student_id)
        @teleport('body')
            <div 
                x-data="{
                    signaturePad: null,
                    signatureError: '',
                    initPad() {
                        setTimeout(() => {
                            const canvas = this.$el.querySelector('canvas');
                            if (!canvas) return;
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            const ctx = canvas.getContext('2d');
                            if (ctx) ctx.scale(ratio, ratio);
                            if (typeof SignaturePad !== 'undefined') {
                                this.signaturePad = new SignaturePad(canvas, {
                                    penColor: 'rgb(0, 0, 0)',
                                    backgroundColor: 'rgb(255, 255, 255)'
                                });
                            }
                        }, 50);
                    },
                    clearPad() {
                        this.signatureError = '';
                        if (this.signaturePad) {
                            this.signaturePad.clear();
                        }
                    },
                    confirmSignature() {
                        if (!this.signaturePad || this.signaturePad.isEmpty()) {
                            this.signatureError = 'Please draw your official signature on the canvas before confirming.';
                            return;
                        }
                        this.signatureError = '';
                        const dataUrl = this.signaturePad.toDataURL('image/png');
                        $wire.issueHostCountryRequirement(dataUrl);
                    }
                }"
                x-init="initPad()"
                class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading"
            >
                <div class="bg-white max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden shadow-sm relative z-[100000] my-auto mx-auto border border-neutral-200 rounded-lg">
                    <div class="p-6 pb-4 flex items-center justify-between shrink-0 border-b border-neutral-200">
                        <div>
                            <h3 class="font-bold text-base text-gray-900 font-heading">Host Country Review Certification</h3>
                            <p class="text-xs text-gray-500 font-medium">Issue formal requirement to undergo institutional ethics review in host country.</p>
                        </div>
                        <button wire:click="closeHostCountryModal" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 flex-1 overflow-y-auto">
                        <div class="p-3.5 bg-purple-50 border border-purple-200 rounded-lg space-y-1 text-xs">
                            <span class="font-bold text-purple-900 block font-heading">Designated Host Country: {{ $request->host_country ?? 'International Study Site' }}</span>
                            <p class="text-purple-800 leading-relaxed font-medium">
                                Certifies that protocol <strong>"{{ $request->title }}"</strong> involves foreign research field sites and requires host country institutional ethics clearance before final DEMO endorsement.
                            </p>
                        </div>

                        <div x-show="signatureError" x-cloak class="p-3 text-xs font-bold flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                            <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="signatureError"></span>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Chairperson Digital Signature:</label>
                            <div class="p-2 flex justify-center overflow-hidden bg-[#F8F7F6] border-2 border-dashed border-[#E4E1DE] rounded-lg">
                                <x-laravel-sign-pad::signature-pad
                                    width="460"
                                    height="180"
                                    border-color="#E4E1DE"
                                    pad-classes="bg-white rounded border shadow-inner cursor-crosshair w-full h-40 sm:h-44"
                                    button-classes="hidden"
                                    :disabled-without-signature="false"
                                />
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs pt-1">
                                <span class="italic text-gray-500">Signer: {{ auth()->user()->name }} (Chairperson / ERB)</span>
                                <button type="button" @click="clearPad()" class="text-xs font-semibold cursor-pointer text-red-700 hover:underline">Clear Canvas</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-4 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 shrink-0 border-t border-gray-200">
                        <button type="button" wire:click="closeHostCountryModal" class="btn-secondary text-xs text-center justify-center cursor-pointer">Cancel</button>
                        <button 
                            type="button" 
                            @click="confirmSignature()" 
                            wire:loading.attr="disabled"
                            class="text-xs bg-purple-700 hover:bg-purple-800 text-white font-bold py-2.5 px-5 rounded-lg flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
                        >
                            <svg wire:loading wire:target="issueHostCountryRequirement" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="issueHostCountryRequirement">Confirm &amp; Issue Host Country Requirement</span>
                            <span wire:loading wire:target="issueHostCountryRequirement">Generating Certification...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
