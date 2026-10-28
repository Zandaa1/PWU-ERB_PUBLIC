@extends('layouts.app', ['header' => 'FAQ & Submission Guidelines'])

@section('content')
<div class="max-w-5xl mx-auto space-y-8 font-heading">

    <!-- Header Hero Banner (DEMO Red Box with Large White Text - Montserrat Only) -->
    <div class="rounded-3xl p-6 md:p-8 text-white relative overflow-hidden text-center" style="background-color: #800000;">
        <h1 class="text-4xl md:text-5xl font-extrabold font-heading text-white tracking-tight">
            FAQ
        </h1>
    </div>

    <!-- 1. SITE PROCESS RUNDOWN (ALTERNATING TOP / BOTTOM TIMELINE: 1 TOP, 2 BOTTOM, 3 TOP, 4 BOTTOM, 5 TOP) -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-6">
        <div class="p-6 md:p-8 text-white space-y-1" style="background-color: #800000;">
            <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                How the Submission & Review Process Works
            </h2>
            <p class="text-xs md:text-sm text-red-100 font-medium font-heading">
                Follow this 5-step portal workflow from initial application to final certificate issuance:
            </p>
        </div>

        <!-- Alternating Top/Bottom Horizontal Line Timeline (Desktop Red Number Circles, Hidden on Mobile) -->
        <div class="p-4 sm:p-6 md:p-8 pt-0 font-heading">
            <div class="relative py-2 sm:py-4">
                <!-- Center Horizontal Line (Large Desktop Only) -->
                <div class="absolute top-1/2 left-8 right-8 h-1 -translate-y-1/2 z-0 hidden lg:block" style="background-color: #fca5a5;"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-5 items-stretch relative z-10 font-heading">
                    
                    <!-- Column 1 (Step 1 - Top Card, Center Node 1, Bottom Spacer) -->
                    <div class="flex flex-col items-center justify-between h-full space-y-3 font-heading">
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 w-full text-center hover:border-red-300 transition-all font-heading shadow-sm flex-1 flex flex-col justify-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-100 font-heading inline-block lg:hidden mb-1.5 self-center">Step 1</span>
                            <h3 class="text-sm font-extrabold text-gray-900 font-heading">1. Sign In</h3>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed mt-1 font-heading">Sign in with your official <span class="bg-gray-100 px-1 rounded font-bold text-gray-900 font-heading">@example.test</span> email.</p>
                        </div>
                        
                        <!-- Desktop Red Circle Number (Hidden on Small/Medium) -->
                        <div class="hidden lg:flex w-9 h-9 rounded-full text-white font-extrabold text-sm items-center justify-center shrink-0 border-4 border-white z-10 font-heading shadow" style="background-color: #800000;">1</div>
                        
                        <div class="hidden lg:block w-full min-h-[80px]"></div>
                    </div>

                    <!-- Column 2 (Step 2 - Top Spacer, Center Node 2, Bottom Card) -->
                    <div class="flex flex-col items-center justify-between h-full space-y-3 font-heading">
                        <div class="hidden lg:block w-full min-h-[80px]"></div>
                        
                        <!-- Desktop Red Circle Number (Hidden on Small/Medium) -->
                        <div class="hidden lg:flex w-9 h-9 rounded-full text-white font-extrabold text-sm items-center justify-center shrink-0 border-4 border-white z-10 font-heading my-2 shadow" style="background-color: #800000;">2</div>
                        
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 w-full text-center hover:border-red-300 transition-all font-heading shadow-sm flex-1 flex flex-col justify-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-100 font-heading inline-block lg:hidden mb-1.5 self-center">Step 2</span>
                            <h3 class="text-sm font-extrabold text-gray-900 font-heading">2. Upload PDFs</h3>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed mt-1 font-heading">Fill study details and upload all required files in PDF format.</p>
                        </div>
                    </div>

                    <!-- Column 3 (Step 3 - Top Card, Center Node 3, Bottom Spacer) -->
                    <div class="flex flex-col items-center justify-between h-full space-y-3 font-heading">
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 w-full text-center hover:border-red-300 transition-all font-heading shadow-sm flex-1 flex flex-col justify-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-100 font-heading inline-block lg:hidden mb-1.5 self-center">Step 3</span>
                            <h3 class="text-sm font-extrabold text-gray-900 font-heading">3. Review</h3>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed mt-1 font-heading">Committee members evaluate each document independently.</p>
                        </div>
                        
                        <!-- Desktop Red Circle Number (Hidden on Small/Medium) -->
                        <div class="hidden lg:flex w-9 h-9 rounded-full text-white font-extrabold text-sm items-center justify-center shrink-0 border-4 border-white z-10 font-heading my-2 shadow" style="background-color: #800000;">3</div>
                        
                        <div class="hidden lg:block w-full min-h-[80px]"></div>
                    </div>

                    <!-- Column 4 (Step 4 - Top Spacer, Center Node 4, Bottom Card) -->
                    <div class="flex flex-col items-center justify-between h-full space-y-3 font-heading">
                        <div class="hidden lg:block w-full min-h-[80px]"></div>
                        
                        <!-- Desktop Red Circle Number (Hidden on Small/Medium) -->
                        <div class="hidden lg:flex w-9 h-9 rounded-full text-white font-extrabold text-sm items-center justify-center shrink-0 border-4 border-white z-10 font-heading my-2 shadow" style="background-color: #800000;">4</div>
                        
                        <div class="bg-white border border-gray-200 rounded-2xl p-4 w-full text-center hover:border-red-300 transition-all font-heading shadow-sm flex-1 flex flex-col justify-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-100 font-heading inline-block lg:hidden mb-1.5 self-center">Step 4</span>
                            <h3 class="text-sm font-extrabold text-gray-900 font-heading">4. Resubmit</h3>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed mt-1 font-heading">Address committee feedback and upload updated PDF versions.</p>
                        </div>
                    </div>

                    <!-- Column 5 (Step 5 - Top Card, Center Node 5, Bottom Spacer) -->
                    <div class="flex flex-col items-center justify-between h-full space-y-3 font-heading">
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 w-full text-center hover:border-red-400 transition-all font-heading shadow-sm flex-1 flex flex-col justify-center">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-white bg-red-900 px-2 py-0.5 rounded font-heading inline-block lg:hidden mb-1.5 self-center" style="background-color: #800000;">Step 5</span>
                            <h3 class="text-sm font-extrabold font-heading" style="color: #800000;">5. Certificate</h3>
                            <p class="text-xs text-gray-700 font-medium leading-relaxed mt-1 font-heading">Chairperson signs live and issues your official ERB Certificate.</p>
                        </div>
                        
                        <!-- Desktop Red Circle Number (Hidden on Small/Medium) -->
                        <div class="hidden lg:flex w-9 h-9 rounded-full text-white font-extrabold text-sm items-center justify-center shrink-0 border-4 border-white z-10 font-heading my-2 shadow" style="background-color: #800000;">5</div>
                        
                        <div class="hidden lg:block w-full min-h-[80px]"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- 2. MANDATORY RULES: DO'S vs DON'TS -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-6">
        <div class="p-6 md:p-8 text-white space-y-1" style="background-color: #800000;">
            <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                Submission Do's & Don'ts
            </h2>
            <p class="text-xs md:text-sm text-red-100 font-medium font-heading">
                Strict compliance rules for research ethics submissions:
            </p>
        </div>

        <div class="p-6 md:p-8 pt-0 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- THINGS TO DO -->
            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-5 space-y-3">
                <div class="flex items-center gap-2 border-b border-emerald-200 pb-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs font-heading">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h3 class="text-base font-extrabold text-emerald-950 font-heading">THINGS TO DO</h3>
                </div>
                <ul class="space-y-2.5 text-xs text-gray-700 font-medium font-heading">
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Use Official DEMO Email:</strong> Always sign in using your <span class="bg-emerald-100 px-1 rounded font-bold text-emerald-900">@example.test</span> Google account.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Convert Files to PDF:</strong> Export all manuscript and form files to PDF format before uploading.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Use Official Templates:</strong> Download and fill out the official DEMO Google Docs templates for forms.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-emerald-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Upload Separate Files:</strong> Attach each mandatory document into its corresponding file slot.</span>
                    </li>
                </ul>
            </div>

            <!-- THINGS NOT TO DO -->
            <div class="bg-red-50/70 border border-red-200 rounded-2xl p-5 space-y-3">
                <div class="flex items-center gap-2 border-b border-red-200 pb-2">
                    <span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs font-heading">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                    <h3 class="text-base font-extrabold text-red-950 font-heading">THINGS NOT TO DO</h3>
                </div>
                <ul class="space-y-2.5 text-xs text-gray-700 font-medium font-heading">
                    <li class="flex items-start gap-2.5">
                        <span class="text-red-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Do NOT Use Personal Emails:</strong> Personal accounts (<span class="bg-red-100 px-1 rounded font-bold text-red-900">@gmail.com</span>) will be denied authorization.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-red-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Do NOT Upload Word Documents:</strong> Raw <span class="bg-red-100 px-1 rounded font-bold text-red-900">.doc</span> or <span class="bg-red-100 px-1 rounded font-bold text-red-900">.docx</span> files will fail system validation.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-red-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Do NOT Merge Documents:</strong> Do not combine all documents into one single massive PDF file.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-red-600 font-extrabold shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <span><strong class="text-gray-900 font-bold">Do NOT Omit Anti-Plagiarism / AI Reports:</strong> Both reports must be uploaded for ethics clearance.</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- 3. REQUIREMENTS & TEMPLATES -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-6">
        <div class="p-6 md:p-8 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="background-color: #800000;">
            <div class="space-y-1">
                <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                    Required Protocol Documents & Templates
                </h2>
            </div>
        </div>

        <div class="p-6 md:p-8 pt-0 divide-y divide-gray-100 space-y-2">
            @foreach($documentTypes as $index => $dt)
                <div class="pt-3 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-heading">
                    <div class="space-y-1">
                        <h3 class="text-sm md:text-base font-extrabold text-gray-900 font-heading">{{ $dt->sort_order ?? ($index + 1) }}. {{ $dt->name }}</h3>
                        <p class="text-xs text-gray-600 font-heading">
                            @if($dt->name === 'Study Protocol')
                                Approved Manuscript, Chapter 1–3, or Chapter 1–2 of your research study.
                            @elseif($dt->name === 'Data Collection Forms (questionnaires) & CV of PI')
                                Questionnaires, survey instruments, and Curriculum Vitae of the Principal Investigator.
                            @elseif($dt->name === 'Official Receipt of ethics review fee')
                                Official payment receipt issued by the DEMO Cashier's Office.
                            @elseif($dt->name === 'Proof of Anti-Plagiarism Report and AI Content Report')
                                Plagiarism/similarity index report and AI content assessment report.
                            @elseif($dt->name === 'Certification of Approved Title and Compliance of Revisions after Proposal Defense')
                                Signed certification from your proposal defense Panel Chair.
                            @elseif($dt->name === 'Informed Consent Form (English)')
                                Standard English informed consent document for research participants.
                            @elseif($dt->name === 'Informed Consent Form (Tagalog)')
                                Tagalog translated informed consent document for local respondents.
                            @elseif($dt->name === 'Additional / Supplementary Thesis File (optional)')
                                Optional supplementary thesis materials, raw instruments, or co-author agreements.
                            @else
                                Required institutional submission file for research ethics committee review.
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto font-heading">
                        @if($dt->is_required)
                            <span class="px-3 py-1 bg-red-100 text-red-900 rounded-full font-bold text-[10px] uppercase border border-red-200 shrink-0 self-start sm:self-auto font-heading" style="color: #800000;">* Mandatory</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full font-bold text-[10px] uppercase border border-gray-200 shrink-0 self-start sm:self-auto font-heading">Optional</span>
                        @endif

                        @if($dt->template_url)
                            <a href="{{ $dt->template_url }}" target="_blank" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-maroon font-bold text-xs rounded-xl border border-red-200 transition-colors flex items-center gap-1.5 font-heading" style="color: #800000;">
                                @if($dt->is_google_doc)
                                    <span>Download Template</span>
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                @else
                                    <span>Download Template</span>
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. CHAPTER 2 SECTIONS -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-6">
        <div class="p-6 md:p-8 text-white space-y-1" style="background-color: #800000;">
            <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                Mandatory Sections Needed in Chapter 2
            </h2>
            <p class="text-xs md:text-sm text-red-100 font-medium font-heading">
                Ensure the following 8 mandatory sections are explicitly addressed under Chapter 2 (Ethical Considerations):
            </p>
        </div>

        <div class="p-6 md:p-8 pt-0 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs font-heading">
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Risk-benefit assessment to respondents (including privacy issues)</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Protection of privacy & confidentiality (Data Safety Protection Plan)</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Vulnerability of research participants / respondents</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Study risks (including social risks) & Respondent benefits</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Patient-related compensations / reimbursements / entitlements</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Informed consent process, Assent, & Recruitment procedures</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Inclusion, Exclusion, and Withdrawal Criteria</span>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 font-heading" style="background-color: #800000;">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="font-bold text-gray-900 text-xs font-heading">Community considerations & Terms of collaborative study</span>
            </div>
        </div>
    </div>

    <!-- 5. GUIDELINES (AI INTEGRITY & GOVERNANCE) -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-6">
        <div class="p-6 md:p-8 text-white space-y-1" style="background-color: #800000;">
            <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                AI Integration & Integrity Guidelines
            </h2>
            <p class="text-xs md:text-sm text-red-100 font-medium font-heading">
                DEMO policy governing AI-assisted research and digital manuscript compliance:
            </p>
        </div>

        <div class="p-6 md:p-8 pt-0 space-y-4 text-xs font-heading">
            <div class="p-5 bg-red-50/60 border border-red-200 rounded-2xl space-y-1.5">
                <h3 class="text-base font-extrabold font-heading text-red-950" style="color: #800000;">Integrity & Non-Fabrication Declaration</h3>
                <p class="text-xs md:text-sm text-gray-700 leading-relaxed font-medium font-heading">
                    Researchers must explicitly declare that AI was <em>not</em> used to falsify, fabricate, or plagiarize proposals, final manuscripts, or data collection instruments. An AI Content Assessment Report must accompany the anti-plagiarism report.
                </p>
            </div>

            <div class="p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-1.5">
                <h3 class="text-base font-extrabold font-heading text-gray-900">Transparency & Disclosure Requirement</h3>
                <p class="text-xs md:text-sm text-gray-700 leading-relaxed font-medium font-heading">
                    Disclose what AI tools were used, exact sections/page numbers applied, and rationale including advantages and disadvantages.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-1">
                    <h4 class="font-extrabold text-sm text-gray-900 font-heading">Human Responsibility</h4>
                    <p class="text-xs text-gray-600 font-medium font-heading">The primary researcher remains 100% accountable for all AI-assisted work.</p>
                </div>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-1">
                    <h4 class="font-extrabold text-sm text-gray-900 font-heading">Data Privacy (RA 10173)</h4>
                    <p class="text-xs text-gray-600 font-medium font-heading">All participant data must be anonymized before submitting to AI tools.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. CONTACT US (FIXED BUTTON & MONTSERRAT ONLY) -->
    <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden space-y-4">
        <div class="p-6 md:p-8 text-white space-y-1" style="background-color: #800000;">
            <h2 class="text-xl md:text-2xl font-extrabold font-heading text-white">
                Contact Us & ERB Office
            </h2>
            <p class="text-xs md:text-sm text-red-100 font-medium font-heading">
                Research Ethics Review Board Secretariat & Office Correspondence:
            </p>
        </div>

        <div class="p-6 md:p-8 pt-0">
            <div class="p-6 bg-red-50/50 border border-red-200 rounded-2xl font-heading">
                <div class="space-y-1.5 font-heading">
                    <span class="text-xs font-extrabold uppercase tracking-widest block font-heading" style="color: #800000;">Chairperson & Secretariat</span>
                    <h3 class="font-extrabold text-xl font-heading" style="color: #800000;">Demo Research Coordinator</h3>
                    <p class="text-xs md:text-sm text-gray-700 font-medium leading-relaxed font-heading">
                        Program Chair, Information Technology &bull; Head of DEMO Technology Integration Team &bull; Chairperson, DEMO Ethics Review Board
                    </p>
                    <p class="text-xs md:text-sm text-gray-800 font-bold pt-1 font-heading">
                        Direct Email: <a href="mailto:coordinator@example.invalid" class="font-extrabold underline hover:text-red-900" style="color: #800000;">coordinator@example.invalid</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
