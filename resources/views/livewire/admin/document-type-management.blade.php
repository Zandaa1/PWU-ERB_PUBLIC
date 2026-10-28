<div class="space-y-6 max-w-5xl mx-auto font-heading">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">Document Types & Templates</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Manage required protocol document types and configure official download templates or Google Docs links.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-wrap items-center justify-between gap-4 font-heading">
        <div class="flex items-center gap-2 font-heading">
            <a href="/admin/users" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Users & Roles</a>
            <a href="/admin/departments" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Departments</a>
            <a href="/admin/document-types" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-maroon text-white" style="background-color: #800000;">Document Types & Templates</a>
            <a href="/admin/archive" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Archive Queue</a>
            <a href="/admin/audit-log" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Audit Log</a>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session()->has('info'))
        <div class="p-4 bg-blue-50 border border-blue-300 text-blue-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Add Document Type Card -->
    <form wire:submit.prevent="addDocumentType" class="card-white p-6 space-y-4 font-heading">
        <h3 class="text-sm font-bold text-gray-900 font-heading">Add New Protocol Document Type</h3>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model="newTypeName" 
                    placeholder="e.g. Vulnerable Participant Safeguard Plan" 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-maroon-800 focus:outline-none" 
                />
                @error('newTypeName') <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-xs font-semibold text-gray-700 cursor-pointer shrink-0">
                <input type="checkbox" wire:model="newTypeRequired" class="rounded text-maroon" /> Required
            </label>
            <button 
                type="submit" 
                wire:confirm="Are you sure you want to add this new document type requirement?"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="addDocumentType"
                class="btn-primary text-xs shrink-0 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
            >
                <svg wire:loading wire:target="addDocumentType" class="w-3.5 h-3.5 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="addDocumentType">+ Add Document Type</span>
                <span wire:loading wire:target="addDocumentType">Adding...</span>
            </button>
        </div>
    </form>

    <!-- Document Types Clean Table -->
    <div class="card-white overflow-hidden font-heading">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">#</th>
                    <th class="px-6 py-4">Document Type</th>
                    <th class="px-6 py-4">Requirement</th>
                    <th class="px-6 py-4">Template Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                @foreach($documentTypes as $dt)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-6 py-4 font-mono font-bold text-gray-400 text-center">{{ $dt->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $dt->name }}</div>
                            @if(!$dt->is_active)
                                <span class="text-[10px] text-amber-700 font-bold uppercase">(Inactive / Deprecated)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($dt->is_required)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-200 inline-flex items-center gap-1">
                                    <span class="text-red-600 font-extrabold">*</span> Mandatory
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase bg-gray-100 text-gray-600 border border-gray-200">
                                    Optional
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($dt->template_url)
                                <a 
                                    href="{{ $dt->template_url }}" 
                                    target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-800 transition-colors"
                                >
                                    @if($dt->is_google_doc)
                                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span>Google Doc</span>
                                        <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-[#800000] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Uploaded File</span>
                                    @endif
                                </a>
                            @else
                                <span class="text-gray-400 italic text-xs">No template configured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <button 
                                    type="button"
                                    wire:click="openTemplateModal({{ $dt->id }})" 
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-maroon font-bold text-xs rounded-xl border border-red-200 transition-colors inline-flex items-center gap-1 cursor-pointer"
                                    style="color: #800000;"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>{{ $dt->template_file_path ? 'Edit Template' : 'Set Template' }}</span>
                                </button>

                                @if($dt->isCore())
                                    <span class="p-1.5 rounded-lg bg-gray-50 text-gray-400 border border-gray-200 inline-flex items-center justify-center" title="Core institutional requirement (locked)">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </span>
                                @else
                                    <button 
                                        type="button"
                                        wire:click="toggleActive({{ $dt->id }})" 
                                        wire:loading.attr="disabled"
                                        class="px-2.5 py-1.5 rounded-xl text-xs font-semibold {{ $dt->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-amber-100 text-amber-900 border border-amber-300' }} cursor-pointer disabled:opacity-50"
                                    >
                                        {{ $dt->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button 
                                        type="button"
                                        wire:click="deleteDocumentType({{ $dt->id }})" 
                                        wire:confirm="Are you sure you want to delete this custom document type?"
                                        wire:loading.attr="disabled"
                                        class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 cursor-pointer disabled:opacity-50"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Clean Focused Template Management Modal Popup -->
    @if($showTemplateModal && $selectedType)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden border border-gray-200 shadow-2xl relative z-[100000] my-auto mx-auto font-heading flex flex-col">
                    
                    <!-- Modal Header -->
                    <div class="p-6 text-white flex items-center justify-between" style="background-color: #800000;">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-200 block">Template Configuration</span>
                            <h3 class="text-base font-extrabold text-white leading-tight mt-0.5">{{ $selectedType->name }}</h3>
                        </div>
                        <button type="button" wire:click="closeTemplateModal" class="text-white/80 hover:text-white font-bold text-xl p-1 leading-none cursor-pointer">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5">
                        
                        <!-- Mode Switcher Tabs -->
                        <div class="flex items-center p-1 bg-gray-100 rounded-xl">
                            <button 
                                type="button" 
                                wire:click="$set('modalMode', 'google_doc')" 
                                @class([
                                    'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-center cursor-pointer inline-flex items-center justify-center gap-1.5',
                                    'bg-white text-gray-900 shadow-xs' => $modalMode === 'google_doc',
                                    'text-gray-600 hover:text-gray-900' => $modalMode !== 'google_doc',
                                ])
                            >
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                <span>Google Doc Link</span>
                            </button>

                            <button 
                                type="button" 
                                wire:click="$set('modalMode', 'file')" 
                                @class([
                                    'flex-1 py-2 rounded-lg text-xs font-bold transition-all text-center cursor-pointer inline-flex items-center justify-center gap-1.5',
                                    'bg-white text-gray-900 shadow-xs' => $modalMode === 'file',
                                    'text-gray-600 hover:text-gray-900' => $modalMode !== 'file',
                                ])
                            >
                                <svg class="w-3.5 h-3.5 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Upload File</span>
                            </button>
                        </div>

                        <!-- Tab 1: Google Doc Link -->
                        @if($modalMode === 'google_doc')
                            <form wire:submit.prevent="saveGoogleDocLink" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-gray-700 uppercase">Google Docs / Drive Shareable URL *</label>
                                    <input 
                                        type="url" 
                                        wire:model="modalTemplateUrl" 
                                        placeholder="https://example.invalid/template-not-configured" 
                                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"
                                    />
                                    @error('modalTemplateUrl') <p class="text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                                    <p class="text-[11px] text-gray-500 font-medium">Ensure link sharing is set to <strong>"Anyone with the link can view"</strong> so students can access and make a copy.</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button 
                                        type="button" 
                                        wire:click="closeTemplateModal" 
                                        class="btn-secondary text-xs cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit" 
                                        wire:loading.attr="disabled"
                                        wire:target="saveGoogleDocLink"
                                        class="btn-primary text-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="saveGoogleDocLink">Save Google Doc Link</span>
                                        <span wire:loading wire:target="saveGoogleDocLink">Saving...</span>
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- Tab 2: Upload File -->
                        @if($modalMode === 'file')
                            <form wire:submit.prevent="saveTemplateFile" class="space-y-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-gray-700 uppercase">Select Template Document (.pdf, .docx, .doc) *</label>
                                    <input 
                                        type="file" 
                                        wire:model="modalTemplateFile" 
                                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800"
                                    />
                                    @error('modalTemplateFile') <p class="text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                                    <p class="text-[11px] text-gray-500 font-medium">Upload an official form template file up to 10MB.</p>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button 
                                        type="button" 
                                        wire:click="closeTemplateModal" 
                                        class="btn-secondary text-xs cursor-pointer"
                                    >
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit" 
                                        wire:loading.attr="disabled"
                                        wire:target="saveTemplateFile"
                                        class="btn-primary text-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="saveTemplateFile">Upload Template File</span>
                                        <span wire:loading wire:target="saveTemplateFile">Uploading...</span>
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- Remove Template Option -->
                        @if($selectedType->template_file_path)
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-xs text-gray-500 font-medium">Currently attached: <strong class="text-gray-800">{{ $selectedType->is_google_doc ? 'Google Doc Link' : 'File in Storage' }}</strong></span>
                                <button 
                                    type="button" 
                                    wire:click="removeTemplate" 
                                    wire:confirm="Are you sure you want to remove the template for {{ $selectedType->name }}?"
                                    class="text-xs font-bold text-red-600 hover:text-red-800 hover:underline cursor-pointer"
                                >
                                    Remove Template
                                </button>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        @endteleport
    @endif
</div>
