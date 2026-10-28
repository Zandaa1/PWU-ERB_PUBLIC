<div class="font-heading">
    <!-- Floating Bottom-Right ? Help & Bug Button -->
    <div class="fixed bottom-6 right-6 z-[9990] font-heading">
        <button 
            type="button" 
            wire:click="openModal" 
            class="w-12 h-12 rounded-full bg-[#800000] hover:bg-red-900 text-white font-extrabold text-lg flex items-center justify-center shadow-sm border-2 border-white/80 transition-all duration-300 transform hover:scale-110 active:scale-95 group font-heading cursor-pointer"
            title="Report an issue or request assistance"
        >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <!-- Subtle Hover Tooltip Badge -->
            <span class="absolute right-14 bottom-1/2 translate-y-1/2 px-3 py-1.5 bg-gray-900 text-white text-xs font-bold rounded-xl shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap border border-gray-700 font-heading">
                Report a Bug / Need Help?
            </span>
        </button>
    </div>

    <!-- Modal Popup for Bug Report -->
    @if($isOpen)
        @teleport('body')
            <div class="fixed inset-0 w-screen h-screen min-h-screen min-w-full bg-black/75 backdrop-blur-md z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto font-heading">
                <div class="bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200 relative z-[100000] font-heading animate-in fade-in zoom-in-95 duration-200 shadow-2xl my-auto mx-auto">
                    
                    <!-- Header Banner -->
                    <div class="p-5 sm:p-6 text-white space-y-1 relative shrink-0" style="background-color: #800000;">
                        <button type="button" wire:click="closeModal" class="absolute top-4 sm:top-5 right-4 sm:right-5 text-white/80 hover:text-white font-extrabold text-2xl transition-colors cursor-pointer leading-none">&times;</button>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold shrink-0 shadow-inner">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-red-200 uppercase tracking-widest block font-heading">DEMO ERB Portal</span>
                                <h3 class="text-base sm:text-lg font-extrabold font-heading text-white leading-tight">Report a Bug / Technical Help</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-5 sm:p-6 space-y-4 font-heading text-xs flex-1 overflow-y-auto">
                        @if($submitted)
                            <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl text-center space-y-3">
                                <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold mx-auto shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-extrabold text-emerald-950">Bug Report Submitted!</h4>
                                    <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                                        Thank you for reporting. Our technical team and system administrator have received your report and will investigate promptly.
                                    </p>
                                </div>
                                <button type="button" wire:click="closeModal" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition-colors font-heading shadow-xs cursor-pointer text-center">
                                    Close Window
                                </button>
                            </div>
                        @else
                            <form wire:submit.prevent="submitReport" class="space-y-4 font-heading">
                                
                                <!-- Issue Category -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 font-heading">Issue Category</label>
                                    <select wire:model="category" class="block w-full text-xs rounded-xl border-gray-300 focus:border-red-800 focus:ring-red-800 font-heading p-2.5">
                                        <option value="bug">Bug / System Error</option>
                                        <option value="layout">UI / Layout / Mobile Responsiveness</option>
                                        <option value="question">Portal Question / Inquiry</option>
                                        <option value="feature">Feature Request / Suggestion</option>
                                    </select>
                                </div>

                                <!-- Subject -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 font-heading">Subject / Brief Summary</label>
                                    <input 
                                        type="text" 
                                        wire:model="subject" 
                                        placeholder="e.g. Upload button not responding on mobile" 
                                        class="block w-full text-xs rounded-xl border-gray-300 focus:border-red-800 focus:ring-red-800 font-heading p-2.5"
                                    />
                                    @error('subject')
                                        <p class="text-xs text-red-600 font-bold font-heading">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-gray-700 font-heading">Detailed Description / Steps to Reproduce</label>
                                    <textarea 
                                        wire:model="description" 
                                        rows="4" 
                                        placeholder="Please describe what happened, any error messages displayed, or steps to reproduce the issue..." 
                                        class="block w-full text-xs rounded-xl border-gray-300 focus:border-red-800 focus:ring-red-800 font-heading p-2.5"
                                    ></textarea>
                                    @error('description')
                                        <p class="text-xs text-red-600 font-bold font-heading">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Form Actions -->
                                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-2 border-t border-gray-100 font-heading shrink-0">
                                    <button type="button" wire:click="closeModal" class="w-full sm:w-auto px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors font-heading text-center cursor-pointer">Cancel</button>
                                    <button 
                                        type="submit" 
                                        wire:loading.attr="disabled"
                                        class="w-full sm:w-auto px-6 py-2.5 text-white font-extrabold rounded-xl text-xs transition-all flex items-center justify-center gap-2 font-heading shadow-sm cursor-pointer disabled:opacity-50"
                                        style="background-color: #800000;"
                                    >
                                        <svg wire:loading wire:target="submitReport" class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span wire:loading.remove wire:target="submitReport">Submit Report</span>
                                        <span wire:loading wire:target="submitReport">Submitting...</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</div>
