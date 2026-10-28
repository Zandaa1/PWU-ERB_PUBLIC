@extends('layouts.app', ['header' => 'Chapter 2: Ethical Considerations Guidelines'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="card-white p-8 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-heading">Ethical Considerations Requirements Outline</h1>
            <p class="text-xs text-gray-500 mt-1">Per DEMO Ethics Review Board Memo — Chapter 2 Manuscript Integration Guidelines</p>
        </div>

        <div class="space-y-4 text-xs text-gray-700 leading-relaxed border-t border-gray-200 pt-6">
            <p>Every submitted <strong>Study Protocol</strong> (Chapter 1–3 manuscript) must contain a dedicated <strong>Ethical Considerations</strong> section covering the following required components:</p>

            <div class="space-y-3">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold text-gray-900 text-sm mb-1" style="color: #800000;">1. Risk-Benefit Assessment</h4>
                    <p>Explicitly describe any potential physical, psychological, social, or legal risks to participants and mitigation strategies put in place. Benefits must outweigh identified risks.</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold text-gray-900 text-sm mb-1" style="color: #800000;">2. Participant Vulnerability & Selection</h4>
                    <p>Detail inclusion/exclusion criteria. If vulnerable groups (minors, prisoners, pregnant women, economically disadvantaged) are involved, provide additional protective protocols.</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold text-gray-900 text-sm mb-1" style="color: #800000;">3. Informed Consent Process</h4>
                    <p>Explain how informed consent/assent will be obtained (voluntary participation, right to withdraw without penalty, plain-language explanation in English/Tagalog).</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold text-gray-900 text-sm mb-1" style="color: #800000;">4. Privacy & Data Confidentiality</h4>
                    <p>Compliance with the Philippine Data Privacy Act of 2012 (RA 10173). Storage security, anonymization, data retention, and secure disposal plans.</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="font-bold text-gray-900 text-sm mb-1" style="color: #800000;">5. Generative AI Use Disclosure</h4>
                    <p>Declare any AI tools (e.g. ChatGPT, Claude) used in literature search, writing assistance, or data synthesis — specifying scope, transparency, and human accountability.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
