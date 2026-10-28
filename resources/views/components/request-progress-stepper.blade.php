@props(['request'])

@php
    $statusValue = is_string($request->status) ? $request->status : $request->status->value;

    $isApproved = $statusValue === 'approved';
    $isRevisions = $statusValue === 'revisions_requested';
    $isUnderReview = $statusValue === 'under_review' || $isRevisions || $isApproved;
    $isSubmitted = true;

    // Stage 1: Submitted
    $step1Done = true;

    // Stage 2: Committee Review
    $step2Done = $isUnderReview;
    $step2Active = $statusValue === 'under_review';

    // Stage 3: Revisions & Decision
    $step3Done = $isApproved;
    $step3Active = $isRevisions;

    // Stage 4: Certificate
    $step4Done = $isApproved;
@endphp

<div class="card-white p-4 sm:p-6 space-y-3 font-heading border border-gray-200 rounded-3xl">
    <div class="flex justify-end border-b border-gray-100 pb-2">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase font-heading bg-gray-100 text-gray-700 border border-gray-200">
            Stage {{ $isApproved ? '4/4 (Completed)' : ($isRevisions ? '3/4 (Revisions)' : ($isUnderReview ? '2/4 (Reviewing)' : '1/4 (Received)')) }}
        </span>
    </div>

    <!-- Multi-Step Progress Tracker Bar -->
    <div class="relative pt-2 pb-1">
        <!-- Connecting Progress Line (Aligned precisely with checkmark boxes center line) -->
        <div class="hidden md:block absolute top-8 left-12 right-12 h-1 bg-gray-200 -translate-y-1/2 rounded-full z-0">
            <div 
                class="h-full bg-maroon rounded-full transition-all duration-500" 
                style="background-color: #800000; width: {{ $isApproved ? '100%' : ($isRevisions ? '66%' : ($isUnderReview ? '33%' : '0%')) }};"
            ></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-4 relative z-10 font-heading">
            <!-- Step 1: Submission Received -->
            <div class="flex md:flex-col items-center gap-3 md:text-center p-3 rounded-2xl md:p-0 bg-gray-50/80 md:bg-transparent border border-gray-100 md:border-0">
                <div @class([
                    'w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm shrink-0 font-heading transition-all shadow-xs ring-4 ring-white',
                    'bg-maroon text-white' => $step1Done,
                ]) style="{{ $step1Done ? 'background-color: #800000;' : '' }}">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="md:pt-1.5">
                    <h4 class="text-xs font-extrabold text-gray-900 font-heading">1. Submission Received</h4>
                    <p class="text-[11px] text-gray-500 font-medium font-heading mt-0.5">Documents intake complete</p>
                </div>
            </div>

            <!-- Step 2: Under Committee Review -->
            <div class="flex md:flex-col items-center gap-3 md:text-center p-3 rounded-2xl md:p-0 bg-gray-50/80 md:bg-transparent border border-gray-100 md:border-0">
                <div @class([
                    'w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm shrink-0 font-heading transition-all shadow-xs ring-4 ring-white',
                    'bg-maroon text-white' => $step2Done && !$step2Active,
                    'bg-amber-500 text-white animate-pulse' => $step2Active,
                    'bg-gray-100 text-gray-400 border border-gray-200' => !$step2Done,
                ]) style="{{ ($step2Done && !$step2Active) ? 'background-color: #800000;' : '' }}">
                    @if($step2Done && !$step2Active)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($step2Active)
                        <svg class="w-6 h-6 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    @else
                        <span class="text-sm font-bold">2</span>
                    @endif
                </div>
                <div class="md:pt-1.5">
                    <h4 class="text-xs font-extrabold text-gray-900 font-heading">2. Committee Review</h4>
                    <p class="text-[11px] text-gray-500 font-medium font-heading mt-0.5">Evaluation by reviewers</p>
                </div>
            </div>

            <!-- Step 3: Revisions & Decision -->
            <div class="flex md:flex-col items-center gap-3 md:text-center p-3 rounded-2xl md:p-0 bg-gray-50/80 md:bg-transparent border border-gray-100 md:border-0">
                <div @class([
                    'w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm shrink-0 font-heading transition-all shadow-xs ring-4 ring-white',
                    'bg-maroon text-white' => $step3Done,
                    'bg-orange-600 text-white animate-pulse' => $step3Active,
                    'bg-gray-100 text-gray-400 border border-gray-200' => !$step3Done && !$step3Active,
                ]) style="{{ $step3Done ? 'background-color: #800000;' : '' }}">
                    @if($step3Done)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($step3Active)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    @else
                        <span class="text-sm font-bold">3</span>
                    @endif
                </div>
                <div class="md:pt-1.5">
                    <h4 class="text-xs font-extrabold text-gray-900 font-heading">3. Revisions & Decision</h4>
                    <p class="text-[11px] text-gray-500 font-medium font-heading mt-0.5">Feedback & updates</p>
                </div>
            </div>

            <!-- Step 4: Clearance & Certificate -->
            <div class="flex md:flex-col items-center gap-3 md:text-center p-3 rounded-2xl md:p-0 bg-gray-50/80 md:bg-transparent border border-gray-100 md:border-0">
                <div @class([
                    'w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-sm shrink-0 font-heading transition-all shadow-xs ring-4 ring-white',
                    'bg-emerald-600 text-white' => $step4Done,
                    'bg-gray-100 text-gray-400 border border-gray-200' => !$step4Done,
                ])>
                    @if($step4Done)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <span class="text-sm font-bold">4</span>
                    @endif
                </div>
                <div class="md:pt-1.5">
                    <h4 class="text-xs font-extrabold text-gray-900 font-heading">4. Clearance Certificate</h4>
                    <p class="text-[11px] text-gray-500 font-medium font-heading mt-0.5">Official ERB Approval</p>
                </div>
            </div>
        </div>
    </div>
</div>
