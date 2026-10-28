<div x-data="{ activeTab: 'pdf' }" class="h-full flex flex-col min-h-full bg-neutral-900">
    <!-- Top Action Bar -->
    <div class="bg-white border-b border-gray-200 px-3 md:px-6 py-2.5 flex flex-col md:flex-row md:items-center justify-between gap-3 shrink-0 shadow-xs font-heading">
        <!-- Title & Navigation -->
        <div class="flex items-center gap-2.5 min-w-0 flex-1 flex-wrap sm:flex-nowrap">
            <a href="/requests/{{ $requestDocument->ethics_request_id }}" class="text-xs text-gray-700 hover:text-maroon font-bold flex items-center gap-1 whitespace-nowrap shrink-0 font-heading" style="color: #800000;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Back to Request</span>
            </a>
            <span class="text-gray-300 shrink-0 hidden sm:inline">|</span>
            <div class="shrink-0 min-w-0 flex-1 sm:flex-initial">
                <h2 class="font-bold text-xs sm:text-sm text-gray-900 leading-snug font-heading truncate">{{ $requestDocument->documentType->name }}</h2>
                <p class="text-[11px] text-gray-500 font-mono whitespace-nowrap">v{{ $selectedVersion->version_number }} ({{ $selectedVersion->created_at->format('M d, Y') }})</p>
            </div>
            <x-status-badge :status="$requestDocument->status" />
        </div>

        <!-- Buttons Container (Version History + Action Buttons) -->
        <div class="flex flex-wrap items-center gap-2 shrink-0 justify-between md:justify-end">
            <!-- Version History Button -->
            <button wire:click="$set('showVersionHistoryModal', true)" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-800 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5 font-mono shrink-0 cursor-pointer shadow-xs">
                <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Version History (v{{ $selectedVersion->version_number }})</span>
            </button>

            @if(auth()->user()->hasAnyRole(['committee', 'admin']))
                @if($requestDocument->status === App\Enums\DocumentStatus::Approved)
                    <button 
                        wire:click="requestRevision" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-wait"
                        class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 whitespace-nowrap shrink-0 cursor-pointer disabled:opacity-50 font-heading"
                    >
                        <svg wire:loading wire:target="requestRevision" class="w-4 h-4 text-amber-700 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="requestRevision" class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span wire:loading.remove wire:target="requestRevision">Re-open Revision</span>
                        <span wire:loading wire:target="requestRevision">Processing...</span>
                    </button>
                @else
                    <button 
                        wire:click="requestRevision" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-wait"
                        class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 whitespace-nowrap shrink-0 cursor-pointer disabled:opacity-50 font-heading"
                    >
                        <svg wire:loading wire:target="requestRevision" class="w-4 h-4 text-amber-700 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="requestRevision" class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span wire:loading.remove wire:target="requestRevision">Request Revision</span>
                        <span wire:loading wire:target="requestRevision">Processing...</span>
                    </button>
                    <button 
                        wire:click="promptApproveDocument" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-wait"
                        class="px-3 py-1.5 text-xs bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold flex items-center gap-1.5 whitespace-nowrap shrink-0 font-heading shadow-xs cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading wire:target="approveDocument,promptApproveDocument" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="approveDocument,promptApproveDocument" class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span wire:loading.remove wire:target="approveDocument,promptApproveDocument">Approve Document</span>
                        <span wire:loading wire:target="approveDocument,promptApproveDocument">Processing...</span>
                    </button>
                @endif
            @endif

            @if(auth()->id() === $requestDocument->ethicsRequest->student_id && $requestDocument->status === App\Enums\DocumentStatus::RevisionsRequested)
                <button wire:click="$set('showRevisionModal', true)" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-bold shadow-xs flex items-center gap-1.5 transition-all whitespace-nowrap shrink-0 font-heading">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Upload Revision</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Flash Message Alert Bar -->
    @if(session('success') || session('error') || session('info') || session('warning'))
        <div class="px-3 md:px-6 py-2 shrink-0 font-heading bg-neutral-900">
            @if(session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-300 text-emerald-950 font-bold rounded-xl text-xs flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="p-3 bg-red-50 border border-red-300 text-red-950 font-bold rounded-xl text-xs flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if(session('info'))
                <div class="p-3 bg-blue-50 border border-blue-300 text-blue-950 font-bold rounded-xl text-xs flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('info') }}</span>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Mobile Tab Switcher (< md) -->
    <div class="flex md:hidden border-b border-gray-800 bg-neutral-900 p-2 gap-2 shrink-0">
        <button 
            @click="activeTab = 'pdf'" 
            :class="activeTab === 'pdf' ? 'bg-[#800000] text-white font-bold shadow' : 'bg-neutral-800 text-gray-300 hover:bg-neutral-700'" 
            class="flex-1 py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>PDF Document</span>
        </button>
        <button 
            @click="activeTab = 'comments'" 
            :class="activeTab === 'comments' ? 'bg-[#800000] text-white font-bold shadow' : 'bg-neutral-800 text-gray-300 hover:bg-neutral-700'" 
            class="flex-1 py-2 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            <span>Comments ({{ $selectedVersion->comments->count() }})</span>
        </button>
    </div>

    <!-- Main Two-Pane Split -->
    <div class="flex-1 flex min-h-0 overflow-hidden min-h-[600px] md:min-h-[750px]">
        <!-- File Viewer Pane (Left - PDF / Image Viewer) -->
        <div 
            :class="{ 'hidden md:flex': activeTab !== 'pdf', 'flex': activeTab === 'pdf' }"
            class="flex-1 bg-neutral-900 flex-col min-w-0 relative"
        >
            @php $isImg = $this->isImageFile($selectedVersion); @endphp
            <div class="bg-neutral-800 border-b border-neutral-700 px-4 py-2 flex items-center justify-between text-xs text-gray-300 shrink-0">
                <span class="font-mono flex items-center gap-2 truncate">
                    @if($isImg)
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="truncate md:hidden">Image Document</span>
                    @else
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                        <span class="truncate md:hidden">PDF Document</span>
                    @endif
                </span>
                <a href="/documents/download/{{ $selectedVersion->id }}" target="_blank" class="hover:text-white underline font-medium whitespace-nowrap shrink-0 ml-2 inline-flex items-center gap-1">
                    <span>Download {{ $isImg ? 'Image' : 'PDF' }}</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
            </div>

            <!-- File Viewer Container -->
            <div class="flex-1 p-2 bg-neutral-950 flex justify-center items-center h-full min-h-[550px] md:min-h-[720px] overflow-auto">
                @if($isImg)
                    <!-- Interactive High-Res Image Viewer -->
                    <div x-data="{ zoom: false }" class="relative w-full h-full flex flex-col items-center justify-center space-y-2 p-2">
                        <div class="flex items-center gap-3 bg-neutral-900/90 border border-neutral-700 px-3 py-1.5 rounded-full text-xs text-gray-300 font-heading shrink-0 shadow-sm z-10">
                            <span class="font-bold text-emerald-400">Image Viewer</span>
                            <span>&bull;</span>
                            <button type="button" @click="zoom = !zoom" class="hover:text-white underline cursor-pointer font-medium text-xs">
                                <span x-text="zoom ? 'Fit to Viewport' : 'Zoom 100% (Original Size)'"></span>
                            </button>
                        </div>
                        <div class="flex-1 w-full flex items-center justify-center overflow-auto p-2">
                            <img 
                                src="/documents/stream/{{ $selectedVersion->id }}" 
                                alt="{{ $selectedVersion->requestDocument->documentType->name }}" 
                                :class="zoom ? 'max-w-none w-auto h-auto' : 'max-h-[700px] w-auto max-w-full object-contain'"
                                class="rounded-xl shadow-2xl transition-all duration-200 border border-neutral-800 bg-black/40"
                            />
                        </div>
                    </div>
                @else
                    <div wire:ignore.self x-data="{ pdfLoaded: false, showFallback: false, versionId: {{ $selectedVersion->id }} }" x-init="setTimeout(function(){ if(!pdfLoaded){ pdfLoaded=true; showFallback=true } }, 3500)" x-on:pdf-version-changed.window="versionId = $event.detail.versionId; pdfLoaded=false; showFallback=false" class="relative w-full h-full min-h-[550px] md:min-h-[720px]">
                        <!-- PDF Stream Loading Placeholder -->
                        <div 
                            x-show="!pdfLoaded" 
                            class="absolute inset-0 bg-neutral-900 flex flex-col items-center justify-center gap-3 text-neutral-300 z-10 rounded shadow-2xl font-heading"
                        >
                            <svg class="w-8 h-8 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <p class="text-xs font-bold font-heading text-neutral-200">Loading Document Stream...</p>
                            <p class="text-[11px] text-neutral-400 font-medium">Fetching PDF...</p>
                        </div>

                        <div 
                            x-show="showFallback"
                            class="absolute inset-0 bg-neutral-900/95 flex flex-col items-center justify-center gap-4 text-neutral-200 z-20 p-6 text-center font-heading rounded"
                        >
                            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3"/></svg>
                            <div>
                                <p class="text-sm font-bold text-white">PDF preview not available inline on this device</p>
                                <p class="text-xs text-neutral-400 mt-1">Mobile Chrome often downloads PDFs instead of previewing in iframe. Your file was fetched successfully — open it directly.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full max-w-xs">
                                <a x-bind:href="'/documents/stream/' + versionId" target="_blank" class="flex-1 px-4 py-2.5 bg-white text-neutral-900 rounded-lg text-xs font-bold text-center hover:bg-gray-100 transition-colors">Open PDF in New Tab</a>
                                <a x-bind:href="'/documents/download/' + versionId" class="flex-1 px-4 py-2.5 bg-[#800000] text-white rounded-lg text-xs font-bold text-center hover:bg-[#5C0000] transition-colors">Download PDF</a>
                            </div>
                            <button @click="showFallback=false; pdfLoaded=false; $refs.pdfFrame.src = '/documents/stream/' + versionId + '?t=' + Date.now()" class="text-xs text-neutral-400 hover:text-white underline">Retry inline preview</button>
                        </div>

                        <iframe 
                            x-ref="pdfFrame"
                            x-bind:src="'/documents/stream/' + versionId"
                            x-on:load="pdfLoaded=true; showFallback=false"
                            x-on:error="pdfLoaded=true; showFallback=true"
                            class="w-full h-full min-h-[550px] md:min-h-[720px] rounded border-0 bg-white shadow-2xl" 
                            title="PDF Document Viewer"
                            loading="eager"
                        ></iframe>
                    </div>
                @endif
            </div>
        </div>

        <!-- Comments Panel (Right, responsive width) -->
        <div 
            :class="{ 'hidden md:flex': activeTab !== 'comments', 'flex': activeTab === 'comments' }"
            class="w-full md:w-80 bg-white border-l border-gray-200 flex-col shrink-0"
        >
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-700">Document Comments & Review</h3>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-200 text-gray-700 rounded-full">v{{ $selectedVersion->version_number }}</span>
            </div>

            <!-- Comment List -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" data-auto-animate>
                @forelse($selectedVersion->comments as $comment)
                    <div @class([
                        'p-3 rounded-lg border text-xs space-y-2',
                        'bg-gray-50 border-gray-200' => !$comment->resolved,
                        'bg-gray-100/60 border-gray-200 opacity-60' => $comment->resolved,
                    ])>
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900">{{ $comment->commenter->name }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-red-100 text-maroon border border-red-200" style="color: #800000;">
                                    v{{ $comment->documentVersion->version_number ?? 1 }}
                                </span>
                            </div>
                            <span class="text-[10px] text-gray-400 font-mono">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        <p class="text-gray-800 leading-normal font-medium">{{ $comment->comment_text }}</p>

                        <div class="flex items-center justify-between pt-1 border-t border-gray-200 text-[10px]">
                            @if(auth()->user()->hasAnyRole(['committee', 'admin']))
                                <button wire:click="toggleResolve({{ $comment->id }})" class="text-xs font-semibold {{ $comment->resolved ? 'text-green-700' : 'text-gray-500 hover:text-gray-800' }} inline-flex items-center gap-1">
                                    @if($comment->resolved)
                                        <svg class="w-3.5 h-3.5 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Resolved</span>
                                    @else
                                        <span>Mark Resolved</span>
                                    @endif
                                </button>
                            @else
                                <span class="text-gray-400 font-semibold inline-flex items-center gap-1">
                                    @if($comment->resolved)
                                        <svg class="w-3.5 h-3.5 text-green-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Resolved</span>
                                    @else
                                        <span>Open</span>
                                    @endif
                                </span>
                            @endif

                            <button wire:click="$set('replyToCommentId', {{ $comment->id }})" class="text-maroon font-semibold hover:underline" style="color: #800000;">
                                Reply
                            </button>
                        </div>

                        <!-- Nested Replies -->
                        @if($comment->replies->count() > 0)
                            <div class="pl-3 border-l-2 border-red-200 space-y-2 mt-2 pt-2" data-auto-animate>
                                @foreach($comment->replies as $reply)
                                    <div class="space-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-gray-900 text-[11px]">{{ $reply->commenter->name }}</span>
                                                <span class="px-1 py-0.2 rounded text-[9px] font-mono font-bold bg-gray-200 text-gray-700">
                                                    v{{ $reply->documentVersion->version_number ?? 1 }}
                                                </span>
                                            </div>
                                            <span class="text-[9px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700 text-[11px]">{{ $reply->comment_text }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Reply Box -->
                        @if($replyToCommentId === $comment->id)
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-2">
                                <textarea wire:model="replyText" rows="2" placeholder="Write clarification reply..." class="w-full p-2 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-maroon-800 focus:outline-none"></textarea>
                                <div class="flex justify-end gap-2">
                                    <button wire:click="$set('replyToCommentId', null)" class="text-[10px] text-gray-500 cursor-pointer hover:underline">Cancel</button>
                                    <button 
                                        wire:click="addReply({{ $comment->id }})" 
                                        wire:loading.attr="disabled"
                                        class="btn-primary py-0.5 px-2.5 text-[10px] flex items-center gap-1 cursor-pointer disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="addReply({{ $comment->id }})">Send Reply</span>
                                        <span wire:loading wire:target="addReply({{ $comment->id }})">Sending...</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-xs italic">
                        No comments left on version {{ $selectedVersion->version_number }} yet.
                    </div>
                @endforelse
            </div>

            <!-- Add Comment Form (Committee) -->
            @if(auth()->user()->hasAnyRole(['committee', 'admin']))
                <div class="p-4 border-t border-gray-200 bg-gray-50 space-y-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase">Add Review Comment:</label>
                    <textarea 
                        wire:model="newComment" 
                        rows="3" 
                        placeholder="Write feedback for the whole document..." 
                        class="w-full p-2.5 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-maroon-800 focus:outline-none"
                    ></textarea>
                    @error('newComment') <p class="text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    <button 
                        wire:click="addComment" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-wait"
                        class="btn-primary w-full text-xs flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading wire:target="addComment" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="addComment">Post Feedback Comment</span>
                        <span wire:loading wire:target="addComment">Posting Comment...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Version History Modal -->
    @if($showVersionHistoryModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading shadow-2xl my-auto mx-auto">
                    <div class="p-4 sm:p-6 pb-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 font-heading">Document Version History & Revision Logs</h3>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">Total Versions: {{ $requestDocument->versions->count() }}</p>
                        </div>
                        <button wire:click="$set('showVersionHistoryModal', false)" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="bg-white p-3 sm:p-4 divide-y divide-gray-100 flex-1 overflow-y-auto text-xs font-heading" data-auto-animate>
                        @foreach($requestDocument->versions->sortByDesc('version_number') as $v)
                            <div @class([
                                'p-3.5 sm:p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 transition-colors rounded-xl mb-2',
                                'bg-red-50/50 border-t-[3px] border-t-[#800000]' => $selectedVersion->id === $v->id,
                                'hover:bg-gray-50 border border-gray-200' => $selectedVersion->id !== $v->id,
                            ]) style="{{ $selectedVersion->id === $v->id ? 'border-left-color: #800000;' : '' }}">
                                <div class="space-y-1 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded font-mono font-bold text-xs {{ $selectedVersion->id === $v->id ? 'bg-maroon text-white' : 'bg-gray-100 text-gray-800' }}" style="{{ $selectedVersion->id === $v->id ? 'background-color: #800000;' : '' }}">
                                            v{{ $v->version_number }}
                                        </span>
                                        <span class="text-gray-500 font-medium text-[11px] sm:text-xs">
                                            Uploaded by <strong class="text-gray-900">{{ $v->uploader->name }}</strong> &bull; {{ $v->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 font-medium pt-1 text-xs">
                                        <strong class="text-gray-900">Change Notes:</strong>
                                        "{{ $v->change_notes ?: 'Initial document submission.' }}"
                                    </p>
                                </div>

                                <div class="shrink-0 flex items-center justify-between sm:justify-end gap-3 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100">
                                    <span class="text-gray-500 font-mono text-[11px] inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>{{ $v->allComments->count() }} comments</span>
                                    </span>
                                    @if($selectedVersion->id === $v->id)
                                        <span class="px-3 py-1 bg-gray-200 text-gray-800 font-bold rounded-lg text-xs">
                                            Viewing
                                        </span>
                                    @else
                                        <button wire:click="selectVersion({{ $v->id }})" class="btn-primary py-1 px-3 text-xs">
                                            Switch to v{{ $v->version_number }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-3 sm:p-4 flex justify-end border-t border-gray-200 shrink-0">
                        <button wire:click="$set('showVersionHistoryModal', false)" class="w-full sm:w-auto btn-secondary text-xs text-center justify-center">Close</button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Upload Revision Modal (Student) -->
    @if($showRevisionModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading shadow-2xl my-auto mx-auto">
                    <div class="p-4 sm:p-6 pb-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 font-heading">Upload Revised Document</h3>
                        <button wire:click="$set('showRevisionModal', false)" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4 flex-1 overflow-y-auto text-xs font-heading">
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl space-y-0.5" style="border-left-color: #800000;">
                            <p class="text-[11px] font-bold text-red-950 uppercase tracking-wider font-heading">Allowed File Format Rules:</p>
                            <p class="text-xs text-red-900 font-bold font-heading">ACCEPTED FORMATS: PDF DOCUMENTS AND IMAGES (JPG, PNG, WEBP). MAX FILE SIZE 10MB EACH.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold uppercase text-gray-700 font-heading">Select Document or Image File (PDF / JPG / PNG / WEBP) <span class="text-red-600">*</span></label>
                                <input 
                                    type="file" 
                                    accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp" 
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-maroon hover:file:bg-red-100 cursor-pointer"
                                    @change="
                                        if ($event.target.files.length) {
                                            $wire.upload('newVersionFile', $event.target.files[0]);
                                        }
                                    "
                                />
                                
                                <!-- Upload Progress Indicator Bar -->
                                <div wire:loading wire:target="newVersionFile" class="p-3 bg-amber-50 border border-amber-300 rounded-xl space-y-1.5">
                                    <div class="flex items-center justify-between text-xs font-bold text-amber-900 font-heading">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Uploading Revised PDF...
                                        </span>
                                        <span class="font-mono text-[11px]">Processing...</span>
                                    </div>
                                    <div class="w-full bg-amber-200 h-2 rounded-full overflow-hidden">
                                        <div class="bg-[#800000] h-full rounded-full animate-pulse transition-all duration-300 w-3/4"></div>
                                    </div>
                                </div>

                                <!-- Verified Success Check Badge -->
                                @if($newVersionFile)
                                    <div wire:loading.remove wire:target="newVersionFile" class="p-3 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-between gap-3 text-xs font-heading">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-sm font-heading">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-extrabold text-emerald-950 font-heading text-xs">PDF Ready</span>
                                                    <span class="px-2 py-0.2 bg-emerald-200 text-emerald-900 font-bold rounded text-[10px] uppercase font-heading inline-flex items-center gap-0.5">
                                                        <svg class="w-3 h-3 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Ready</span>
                                                    </span>
                                                </div>
                                                <p class="text-gray-600 font-mono text-[11px] font-bold">
                                                    {{ $newVersionFile->getClientOriginalName() }} ({{ round($newVersionFile->getSize() / 1024, 1) }} KB)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @error('newVersionFile') <p class="text-xs text-red-600 font-medium font-heading flex items-center gap-1"><svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> {{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-semibold uppercase text-gray-700">Change Notes (What was revised?) <span class="text-red-600">*</span></label>
                                <textarea wire:model="changeNotes" rows="3" placeholder="Briefly describe what changes were made in response to reviewer feedback..." class="w-full p-2.5 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-maroon-800 focus:outline-none font-heading"></textarea>
                                @error('changeNotes') <p class="text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 pt-3 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 border-t border-gray-200 shrink-0 font-heading">
                        <button 
                            type="button"
                            wire:click="$set('showRevisionModal', false)" 
                            wire:loading.attr="disabled"
                            wire:target="submitRevision"
                            class="w-full sm:w-auto btn-secondary text-xs text-center justify-center disabled:opacity-50"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button"
                            wire:click="submitRevision" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            wire:target="submitRevision"
                            class="w-full sm:w-auto btn-primary text-xs text-center justify-center flex items-center gap-1.5"
                        >
                            <svg wire:loading wire:target="submitRevision" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="submitRevision">Upload & Resubmit Version</span>
                            <span wire:loading wire:target="submitRevision">Uploading & Resubmitting...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Request Revision Without Comment Warning Modal -->
    @if($showNoCommentWarningModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-md w-full max-h-[90vh] flex flex-col overflow-hidden border border-amber-200 relative z-[100000] font-heading shadow-2xl my-auto mx-auto">
                    <div class="p-4 sm:p-6 pb-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                        <h3 class="text-sm font-bold text-amber-800 font-heading flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            No Comments Provided
                        </h3>
                        <button wire:click="$set('showNoCommentWarningModal', false)" class="text-gray-400 hover:text-gray-600 font-bold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <div class="p-4 sm:p-6 space-y-3 flex-1 overflow-y-auto text-xs text-gray-700 font-heading">
                        <p class="font-semibold text-gray-900">You haven't posted any feedback comments on this document version (v{{ $selectedVersion->version_number }}).</p>
                        <p class="text-gray-500">Leaving specific comments helps the student understand exactly what revisions are needed before resubmitting.</p>
                        <p class="font-bold text-amber-900 bg-amber-50 p-2.5 rounded-xl border border-amber-200 mt-2">
                            Are you sure you want to request revision without adding a feedback comment?
                        </p>
                    </div>

                    <div class="p-4 sm:p-6 pt-3 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 border-t border-gray-200 shrink-0 font-heading">
                        <button wire:click="$set('showNoCommentWarningModal', false)" class="w-full sm:w-auto btn-secondary text-xs text-center justify-center">
                            Go Back & Add Comment
                        </button>
                        <button 
                            wire:click="confirmRequestRevision" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="w-full sm:w-auto btn-primary text-xs bg-amber-700 hover:bg-amber-800 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 text-center"
                        >
                            <svg wire:loading wire:target="confirmRequestRevision" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="confirmRequestRevision">Yes, Request Revision</span>
                            <span wire:loading wire:target="confirmRequestRevision">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <!-- Document Approval Confirmation Modal -->
    @if($showApproveConfirmationModal)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden border border-emerald-200 relative z-[100000] shadow-2xl my-auto animate-in fade-in zoom-in-95 duration-150">
                    <!-- Modal Header -->
                    <div class="p-5 bg-emerald-800 text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold leading-tight">Confirm Document Approval</h3>
                                <p class="text-xs text-emerald-100 font-medium">ERB Committee Evaluation</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeApproveModal" class="text-white/80 hover:text-white font-extrabold text-2xl leading-none cursor-pointer">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4 text-xs font-heading">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1.5 text-emerald-950 font-medium">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Target Document</span>
                            <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ $requestDocument->documentType->name }}</h4>
                            <p class="text-xs text-gray-600">
                                Version <strong class="text-gray-900">v{{ $selectedVersion->version_number ?? 1 }}</strong> submitted by <strong class="text-gray-900">{{ $requestDocument->ethicsRequest->student->name }}</strong>
                            </p>
                        </div>

                        <!-- Alert Banner -->
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-1 text-gray-700 font-medium leading-relaxed">
                            <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Approval Effect:</span>
                            </div>
                            <p>
                                Are you sure you want to approve this document? Approving marks this document as compliant in the review process and unblocks the student's submission toward final ethics certification.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 font-heading">
                            <button type="button" wire:click="closeApproveModal" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors cursor-pointer font-heading">
                                Cancel
                            </button>
                            <button 
                                type="button" 
                                wire:click="confirmApproveDocument" 
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 shadow-md cursor-pointer disabled:opacity-50 font-heading"
                            >
                                <svg wire:loading wire:target="confirmApproveDocument" class="w-4 h-4 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="confirmApproveDocument">Yes, Approve Document</span>
                                <span wire:loading wire:target="confirmApproveDocument">Approving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
