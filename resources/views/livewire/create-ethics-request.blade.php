<div 
    x-data="{
        title: @entangle('title').live,
        co_authors: @entangle('co_authors').live,
        department_id: @entangle('department_id').live,
        init() {
            if (!this.title && localStorage.getItem('erb_draft_title')) {
                this.title = localStorage.getItem('erb_draft_title');
            }
            if (!this.co_authors && localStorage.getItem('erb_draft_co_authors')) {
                this.co_authors = localStorage.getItem('erb_draft_co_authors');
            }
            if (!this.department_id && localStorage.getItem('erb_draft_department_id')) {
                this.department_id = localStorage.getItem('erb_draft_department_id');
            }
            this.$watch('title', val => { if(val) localStorage.setItem('erb_draft_title', val); });
            this.$watch('co_authors', val => { if(val) localStorage.setItem('erb_draft_co_authors', val); });
            this.$watch('department_id', val => { if(val) localStorage.setItem('erb_draft_department_id', val); });
        },
        clearDraft() {
            localStorage.removeItem('erb_draft_title');
            localStorage.removeItem('erb_draft_co_authors');
            localStorage.removeItem('erb_draft_department_id');
        }
    }"
    @form-submitted.window="clearDraft()"
    class="max-w-4xl mx-auto space-y-4 sm:space-y-6"
>
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm animate-header-glide" style="background-color: #800000;">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white font-heading">Submit Ethics Review Requirement</h1>
            <p class="text-xs sm:text-sm text-red-100 font-medium mt-1">Please provide your thesis study details and upload all required documents in PDF format.</p>
        </div>
        <a href="/dashboard" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white font-bold text-xs rounded-xl transition-all shrink-0 self-start sm:self-center">
            Cancel
        </a>
    </div>

    <!-- Submission Form Card -->
    <form wire:submit.prevent="submit" class="card-white p-4 sm:p-6 md:p-8 space-y-6 sm:space-y-8">

        <!-- Section 1: Research Details -->
        <div class="space-y-4 pb-6 border-b border-gray-200">
            <h3 class="text-sm sm:text-base font-bold text-gray-900 flex items-center gap-2" style="color: #800000;">
                <span class="w-6 h-6 rounded-full text-white text-xs flex items-center justify-center font-bold" style="background-color: #800000;">1</span>
                Research Study Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div class="md:col-span-2 space-y-1">
                    <label class="block text-xs font-semibold uppercase text-gray-700">Full Title of Research / Thesis Study <span class="text-red-600">*</span></label>
                    <input 
                        type="text" 
                        wire:model="title" 
                        placeholder="e.g. Willingness to Pay for Digital Health Interventions..." 
                        class="w-full px-3.5 sm:px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                    />
                    @error('title') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 space-y-1 font-heading">
                    <label class="block text-xs font-semibold uppercase text-gray-700 font-heading">Co-Authors / Co-Investigators (Optional — For Group Studies)</label>
                    <input 
                        type="text" 
                        wire:model="co_authors" 
                        placeholder="e.g. Demo Secretary, Demo Researcher (leave blank if individual study)" 
                        class="w-full px-3.5 sm:px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                    />
                    <p class="text-[11px] text-gray-500 font-medium font-heading">If filled out, your official certificate will display your name with "et al." (e.g. {{ auth()->user()->name }} et al.)</p>
                    @error('co_authors') <span class="text-xs text-red-600 font-medium font-heading">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase text-gray-700">School / Department <span class="text-red-600">*</span></label>
                    <select wire:model="department_id" class="w-full px-3.5 sm:px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            @php
                                $id = is_object($dept) ? ($dept->id ?? null) : (is_array($dept) ? ($dept['id'] ?? null) : null);
                                $name = is_object($dept) ? ($dept->name ?? null) : (is_array($dept) ? ($dept['name'] ?? null) : null);
                            @endphp
                            @if($id && $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase text-gray-700">Research Domain <span class="text-red-600">*</span></label>
                    <select wire:model="research_domain" class="w-full px-3.5 sm:px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading">
                        <option value="">-- Select Research Domain --</option>
                        @foreach(\App\Enums\ResearchDomain::cases() as $domain)
                            <option value="{{ $domain->value }}">{{ $domain->label() }}</option>
                        @endforeach
                    </select>
                    @error('research_domain') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="block text-xs font-semibold uppercase text-gray-700">Protocol Site Location <span class="text-red-600">*</span></label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-800">
                            <input type="radio" wire:model.live="is_local" :value="true" name="is_local" value="1" class="text-maroon focus:ring-maroon-800" />
                            <span>Local (Within the Philippines)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-gray-800">
                            <input type="radio" wire:model.live="is_local" :value="false" name="is_local" value="0" class="text-maroon focus:ring-maroon-800" />
                            <span>International / Overseas Study Site</span>
                        </label>
                    </div>
                    @error('is_local') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror

                    @if(!$is_local)
                        <div class="pt-2 space-y-1">
                            <label class="block text-xs font-semibold uppercase text-gray-700">Host Country <span class="text-red-600">*</span></label>
                            <input 
                                type="text" 
                                wire:model="host_country" 
                                placeholder="e.g. Singapore, United States, Japan" 
                                class="w-full px-3.5 sm:px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"
                            />
                            <p class="text-[11px] text-gray-500 font-medium">Studies conducted overseas require host country ethical clearance documentation.</p>
                            @error('host_country') <span class="text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section 2: Required Document Uploads -->
        <div class="space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2" style="color: #800000;">
                    <span class="w-6 h-6 rounded-full text-white text-xs flex items-center justify-center font-bold" style="background-color: #800000;">2</span>
                    Required Ethics Documents (max 10MB each)
                </h3>
                <p class="text-xs text-gray-500 mt-1">Upload the current PDF file for each required submission item.</p>
            </div>

            <div class="space-y-4" data-auto-animate>
                @foreach($documentTypes as $index => $type)
                    @php
                        $typeDescriptions = [
                            'Study Protocol' => 'Approved Manuscript, Chapter 1–3, or Chapter 1–2 of your research study.',
                            'Data Collection Forms (questionnaires) & CV of PI' => 'Questionnaires, survey instruments, and Curriculum Vitae of the Principal Investigator.',
                            'Official Receipt of ethics review fee' => 'Official payment receipt issued by the DEMO Cashier\'s Office.',
                            'Proof of Anti-Plagiarism Report and AI Content Report' => 'Plagiarism/similarity index report and AI content assessment report.',
                            'Certification of Approved Title and Compliance of Revisions after Proposal Defense' => 'Signed certification from your proposal defense Panel Chair.',
                            'Informed Consent Form (English)' => 'Standard English informed consent document for research participants.',
                            'Informed Consent Form (Tagalog)' => 'Tagalog translated informed consent document for local respondents.',
                            'Additional / Supplementary Thesis File (optional)' => 'Optional supplementary thesis materials, raw instruments, or co-author agreements.',
                        ];
                        $desc = $typeDescriptions[$type->name] ?? 'Official DEMO Ethics Review Board document upload requirement.';
                    @endphp

                    <div class="bg-white border border-gray-200 rounded-2xl p-5 space-y-4 shadow-sm hover:border-red-200 transition-all font-heading">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 font-heading">Document {{ $index + 1 }}</span>
                                    @if($type->is_required)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-200 inline-flex items-center gap-0.5 font-heading" style="color: #800000;">
                                            <span class="text-red-600 font-extrabold">*</span>
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase bg-gray-100 text-gray-600 border border-gray-200 font-heading">
                                            Optional
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-gray-900 font-heading">{{ $type->name }}</h4>
                                    
                                    <!-- Circular Hover ? Tooltip Badge -->
                                    <div x-data="{ showTip: false }" class="relative inline-block font-heading">
                                        <button 
                                            type="button" 
                                            @mouseenter="showTip = true" 
                                            @mouseleave="showTip = false" 
                                            @click="showTip = !showTip"
                                            class="w-5 h-5 rounded-full bg-red-100 border border-red-200 text-maroon hover:bg-red-800 hover:text-white flex items-center justify-center cursor-pointer transition-colors shrink-0 shadow-xs font-heading"
                                            style="color: #800000;"
                                            title="Click or hover for description"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>

                                        <!-- Tooltip Popup -->
                                        <div 
                                            x-show="showTip" 
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                            class="absolute left-0 sm:left-auto sm:right-0 top-7 w-64 p-3 bg-gray-900 text-white text-xs rounded-xl shadow-xl z-30 font-medium leading-relaxed border border-gray-700 pointer-events-none font-heading"
                                            style="display: none;"
                                        >
                                            <div class="flex items-start gap-1.5 mb-1">
                                                <span class="font-extrabold text-red-300 uppercase text-[10px] tracking-wider block font-heading">Requirement Details</span>
                                            </div>
                                            <p class="text-gray-200 font-medium text-xs font-heading">{{ $desc }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($type->template_file_path)
                                <a href="/templates/{{ $type->id }}/download" target="_blank" class="inline-flex items-center gap-1 text-xs text-maroon hover:underline font-bold shrink-0 self-start sm:self-auto font-heading" style="color: #800000;">
                                    <span>Download Template</span>
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            @endif
                        </div>

                        <!-- Upload Input Dropzone & Status Area -->
                        <div wire:key="upload-slot-{{ $type->id }}-{{ ($uploads[$type->id] ?? null) ? 'uploaded' : 'empty' }}" x-data="{ isDragging: false }" class="space-y-2 font-heading">
                            <input 
                                type="file" 
                                x-ref="fileInput_{{ $type->id }}"
                                accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="
                                    if ($event.target.files.length) {
                                        $wire.upload('uploads.{{ $type->id }}', $event.target.files[0]);
                                    }
                                "
                            />

                            @if(! ($uploads[$type->id] ?? null))
                                <div 
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="
                                        isDragging = false; 
                                        if ($event.dataTransfer.files.length) {
                                            $wire.upload('uploads.{{ $type->id }}', $event.dataTransfer.files[0]);
                                        }
                                    "
                                    :class="{ 'border-red-800 bg-red-50/60 scale-[1.01] shadow-sm': isDragging, 'border-gray-300 bg-gray-50/50 hover:border-red-400 hover:bg-gray-50': !isDragging }"
                                    class="border-2 border-dashed rounded-2xl p-5 text-center transition-all duration-200 cursor-pointer relative font-heading"
                                    @click="
                                        $refs.fileInput_{{ $type->id }}.value = '';
                                        $refs.fileInput_{{ $type->id }}.click();
                                    "
                                >
                                    <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                                        <div class="w-10 h-10 rounded-2xl bg-red-100/70 text-maroon flex items-center justify-center font-extrabold shrink-0 shadow-inner" style="color: #800000;">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 font-heading">
                                                <span class="text-maroon underline font-extrabold" style="color: #800000;">Drag & drop your PDF file here</span> or click to browse
                                            </p>
                                            <p class="text-[11px] text-gray-500 font-medium font-heading mt-0.5">PDF format only &bull; Max 10MB</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Progress Indicator Bar -->
                            <div wire:loading wire:target="uploads.{{ $type->id }}" class="p-3.5 bg-amber-50 border border-amber-300 rounded-xl space-y-1.5 mt-2">
                                <div class="flex items-center justify-between text-xs font-bold text-amber-900 font-heading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Uploading PDF File...
                                    </span>
                                    <span class="font-mono text-[11px]">Processing...</span>
                                </div>
                                <div class="w-full bg-amber-200 h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#800000] h-full rounded-full animate-pulse transition-all duration-300 w-3/4"></div>
                                </div>
                            </div>

                            <!-- Verified Success Container -->
                            @if($uploads[$type->id] ?? null)
                                <div wire:loading.remove wire:target="uploads.{{ $type->id }}" class="p-3.5 bg-emerald-50/80 border border-emerald-300 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-heading mt-2">
                                    <div class="space-y-0.5 flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-emerald-950 font-heading text-xs truncate">
                                                {{ $uploads[$type->id]->getClientOriginalName() }}
                                            </span>
                                            <span class="text-gray-500 font-mono text-[11px] shrink-0 font-bold">
                                                ({{ round($uploads[$type->id]->getSize() / 1024, 1) }} KB)
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">  

                                        <!-- Red Delete File Button -->
                                        <button 
                                            type="button" 
                                            wire:click.prevent="clearDocumentUpload({{ $type->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="clearDocumentUpload({{ $type->id }})"
                                            @click="$refs.fileInput_{{ $type->id }}.value = ''"
                                            class="px-3 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-900 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 font-heading shadow-xs cursor-pointer disabled:opacity-50"
                                            style="color: #800000;"
                                            title="Delete uploaded file"
                                        >
                                            <svg class="w-3.5 h-3.5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Delete</span>
                                        </button>

                                        <!-- Green Checkmark Badge -->
                                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold shrink-0 shadow-xs font-heading" title="Uploaded & Verified">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @error('uploads.' . $type->id)
                                <p class="text-xs text-red-600 font-bold mt-1 font-heading flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Action -->
        <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 font-heading">
            <a href="/dashboard" class="btn-secondary text-center justify-center">Cancel</a>
            <button type="button" wire:click="openDataPrivacyModal" class="btn-primary text-center justify-center" wire:loading.attr="disabled">
                <span wire:loading.remove>Submit Ethics Request</span>
                <span wire:loading>Submitting...</span>
            </button>
        </div>
    </form>

    <!-- Data Privacy Act of 2012 Consent Modal (RA 10173) -->
    @if($showDataPrivacyModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl sm:rounded-3xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading animate-in fade-in zoom-in-95 duration-200 shadow-2xl my-auto mx-auto">
                    
                    <!-- Header Banner -->
                    <div class="p-5 sm:p-6 md:p-8 text-white space-y-1 relative shrink-0" style="background-color: #800000;">
                        <button type="button" wire:click="closeDataPrivacyModal" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-white/80 hover:text-white font-extrabold text-2xl transition-colors cursor-pointer leading-none">&times;</button>
                        
                        <div class="flex items-center gap-3 mb-1">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold shrink-0 shadow-inner">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-red-200 uppercase tracking-widest block font-heading">Republic Act No. 10173 Compliance</span>
                                <h3 class="text-base sm:text-xl font-extrabold font-heading text-white leading-tight">Data Privacy & Confidentiality Consent</h3>
                            </div>
                        </div>
                        <p class="text-xs text-red-100 font-medium font-heading">
                            Example Research Institution Research Ethics Review Board
                        </p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-5 sm:p-6 md:p-8 space-y-4 sm:space-y-5 font-heading text-xs flex-1 overflow-y-auto">
                        
                        <!-- Privacy Notice Box -->
                        <div class="p-3.5 sm:p-4 bg-gray-50 border border-gray-200 rounded-2xl space-y-2.5 sm:space-y-3 text-gray-700 font-medium leading-relaxed">
                            <p class="font-bold text-gray-900 uppercase tracking-wider text-[11px]">Notice of Confidentiality & Data Processing:</p>
                            <p>
                                Pursuant to the <strong>Data Privacy Act of 2012 (R.A. 10173)</strong> and the institutional guidelines of Example Research Institution (DEMO), all personal information, research protocols, thesis manuscripts, and uploaded ethics documents are classified as <strong>STRICTLY CONFIDENTIAL</strong>.
                            </p>
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl space-y-1 text-red-950 font-bold">
                                <p class="text-[11px] uppercase font-bold" style="color: #800000;">Strict Access Boundary Rule:</p>
                                <p class="text-xs font-semibold text-gray-800">
                                    Access to submitted ethics protocols is strictly restricted exclusively to:
                                </p>
                                <ul class="list-disc pl-4 text-xs font-semibold text-gray-800 space-y-0.5">
                                    <li>The Student Applicant(s)</li>
                                    <li>Assigned Thesis Advisers & Department Chairs</li>
                                    <li>Authorized DEMO Ethics Review Board Staff & Review Committee</li>
                                </ul>
                            </div>
                            <p>
                                By proceeding, you declare that all uploaded materials comply with academic integrity standards, anti-plagiarism guidelines, and data privacy principles.
                            </p>
                        </div>

                        <!-- Checkbox Agreement -->
                        <div class="p-3.5 sm:p-4 bg-red-50/50 border border-red-200 rounded-2xl space-y-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="privacyConsentAccepted" 
                                    class="w-5 h-5 text-red-900 rounded border-gray-300 focus:ring-red-800 mt-0.5 shrink-0 cursor-pointer"
                                />
                                <span class="text-xs font-bold text-gray-900 leading-snug font-heading">
                                    I hereby declare that I have read, understood, and accept the DEMO ERB Data Privacy & Confidentiality Consent Notice in compliance with R.A. 10173.
                                </span>
                            </label>
                            @error('privacyConsentAccepted')
                                <p class="text-xs text-red-600 font-bold font-heading pl-8">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 border-t border-gray-100 pt-4 font-heading shrink-0">
                            <button 
                                type="button" 
                                wire:click="closeDataPrivacyModal" 
                                wire:loading.attr="disabled"
                                wire:target="confirmSubmission"
                                class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors text-center font-heading cursor-pointer disabled:opacity-50"
                            >
                                Back to Edit
                            </button>
                            <button 
                                type="button" 
                                wire:click="confirmSubmission" 
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                wire:target="confirmSubmission"
                                @class([
                                    'w-full sm:w-auto px-6 py-2.5 text-white font-extrabold rounded-xl text-xs transition-all flex items-center justify-center gap-2 font-heading shadow-sm',
                                    'opacity-50 cursor-not-allowed' => !$privacyConsentAccepted,
                                    'hover:bg-red-900 cursor-pointer' => $privacyConsentAccepted
                                ])
                                style="background-color: #800000;"
                                @if(!$privacyConsentAccepted) disabled @endif
                            >
                                <svg wire:loading.remove wire:target="confirmSubmission" class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg wire:loading wire:target="confirmSubmission" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="confirmSubmission">I Agree & Submit Ethics Request</span>
                                <span wire:loading wire:target="confirmSubmission">Submitting Ethics Request...</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Ethical Considerations Popup Modal -->
    @if($showEthicalConsiderationsModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden border-t-4 shadow-2xl relative z-[100000] font-heading my-auto mx-auto" style="border-top-color: #800000;">
                    <div class="p-4 sm:p-6 pb-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 font-heading" style="color: #800000;">Chapter 2 Ethical Considerations Guidelines</h3>
                            <p class="text-xs text-gray-500 font-medium">Official DEMO Ethics Review Board Protocol Requirements</p>
                        </div>
                        <button type="button" wire:click="$set('showEthicalConsiderationsModal', false)" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4 flex-1 overflow-y-auto text-xs text-gray-800 font-heading">
                        <!-- Section 1: Checklist -->
                        <div class="space-y-2">
                            <h4 class="font-bold uppercase text-[11px] text-maroon" style="color: #800000;">1. General Ethical Considerations Checklist</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Risk-benefit assessment to respondents</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Protection of privacy & confidentiality (Data Plan)</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Vulnerability of research participants / respondents</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Study risks & Respondent benefits</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Participant-related compensations / reimbursements</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Informed consent process, Assent, & Recruitment</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Inclusion, Exclusion, and Withdrawal Criteria</span>
                                </div>
                                <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Community considerations & Collaborative terms</span>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: AI Guidelines -->
                        <div class="space-y-2 pt-2 border-t border-gray-200">
                            <h4 class="font-bold uppercase text-[11px] text-maroon" style="color: #800000;">2. AI Integration & Integrity Guidelines</h4>
                            <div class="p-3 bg-red-50/60 border border-red-200 rounded-lg space-y-1">
                                <strong class="font-bold text-red-900">Integrity & Non-Falsification Declaration:</strong>
                                <p class="text-gray-700 leading-relaxed">Researchers must declare that AI was <em>not</em> used to falsify, fabricate, or plagiarize proposals, final manuscripts, or data collection instruments. Upload the AI content assessment report alongside your anti-plagiarism report.</p>
                            </div>
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg space-y-1">
                                <strong class="font-bold text-gray-900">Data Privacy Act Compliance (RA 10173):</strong>
                                <p class="text-gray-700 leading-relaxed">Sensitive participant data must be completely anonymized prior to any AI tool processing.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 pt-3 border-t border-gray-200 flex justify-end shrink-0">
                        <button type="button" wire:click="$set('showEthicalConsiderationsModal', false)" class="w-full sm:w-auto btn-primary text-xs text-center justify-center">
                            I Have Reviewed & Understood These Guidelines
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Submission Validation / Error Popup Modal -->
    @if($showValidationErrorModal && $errors->any())
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden border border-red-200 relative z-[100000] font-heading animate-in fade-in zoom-in-95 duration-200 shadow-2xl my-auto mx-auto">
                    <!-- Red / Alert Header -->
                    <div class="p-5 sm:p-6 bg-red-800 text-white flex items-center justify-between shrink-0" style="background-color: #800000;">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold leading-tight text-white font-heading">Submission Incomplete</h3>
                                <p class="text-[11px] sm:text-xs text-red-100 font-medium font-heading">Please address the required items below</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeValidationErrorModal" class="text-white/80 hover:text-white font-extrabold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <!-- Error List Body -->
                    <div class="p-5 sm:p-6 space-y-4 text-xs font-heading flex-1 overflow-y-auto">
                        <p class="text-gray-700 font-medium">
                            Your ethics submission could not proceed because some mandatory fields or required document files are missing or incomplete:
                        </p>
                        
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 sm:p-4 max-h-56 overflow-y-auto space-y-2" data-auto-animate>
                            @foreach($errors->all() as $error)
                                <div class="flex items-start gap-2.5 text-red-900 font-semibold">
                                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="leading-relaxed">{{ $error }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2 flex justify-end shrink-0">
                            <button type="button" wire:click="closeValidationErrorModal" class="w-full sm:w-auto px-5 py-2.5 text-white font-bold rounded-xl text-xs transition-colors cursor-pointer text-center font-heading" style="background-color: #800000;">
                                Review & Fix Requirements
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Submission Success Popup Modal -->
    @if($showSuccessModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading animate-in fade-in zoom-in-95 duration-200 shadow-2xl my-auto mx-auto">
                    <!-- Maroon Header Banner -->
                    <div class="p-5 sm:p-6 md:p-8 text-white relative shrink-0" style="background-color: #800000;">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-600 border border-emerald-400/50 flex items-center justify-center font-extrabold text-2xl shrink-0 shadow-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-red-200 block font-heading">Ethics Protocol Submission</span>
                                <h3 class="text-base sm:text-xl font-extrabold leading-tight text-white font-heading">Submitted Successfully!</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-5 sm:p-6 md:p-8 space-y-4 sm:space-y-5 text-xs font-heading flex-1 overflow-y-auto">
                        <div class="p-3.5 sm:p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-2">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Research Title</span>
                            <p class="text-xs sm:text-sm font-bold text-gray-900 leading-snug">{{ $createdRequestTitle }}</p>
                            
                            <div class="pt-2 border-t border-gray-200 flex items-center justify-between text-[11px] text-gray-600">
                                <span>Status: <strong class="text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 uppercase text-[10px]">Submitted</strong></span>
                                <span>Date: <strong>{{ now()->format('M d, Y') }}</strong></span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl text-emerald-950 font-medium leading-relaxed space-y-1">
                            <strong class="font-bold block text-emerald-900">What Happens Next?</strong>
                            <p>
                                Your protocol and documents are now submitted to the DEMO Ethics Review Board. Committee reviewers will evaluate your submission, and you will receive notifications for all status updates and feedback.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-2 font-heading shrink-0">
                            @if($createdRequestId)
                                <a href="/requests/{{ $createdRequestId }}" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-xl text-xs transition-colors text-center font-heading">
                                    View Submission Details
                                </a>
                            @endif
                            <a href="/dashboard" class="w-full sm:w-auto px-6 py-2.5 text-white font-extrabold rounded-xl text-xs transition-all text-center font-heading shadow-sm" style="background-color: #800000;">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
