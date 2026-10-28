<!-- Hallmark · design-system: DESIGN.md · genre: modern-minimal -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DEMO ERB - Certification of Requirement to Review in Host Countries</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 30pt 40pt 25pt 40pt;
        }
        body {
            font-family: 'Montserrat', 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 9.5pt;
            line-height: 1.35;
        }
        .header-container {
            text-align: center;
            margin-bottom: 8px;
        }
        .header-logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
            display: block;
            margin: 0 auto 4px auto;
        }
        .header-title-main {
            font-size: 15pt;
            font-weight: bold;
            color: #800000;
            margin: 0;
            line-height: 1.1;
            text-align: center;
        }
        .header-title-sub {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin: 2px 0 0 0;
            text-align: center;
        }
        .board-title {
            text-align: center;
            font-size: 15pt;
            font-weight: bold;
            color: #000000;
            margin: 8px 0 10px 0;
            font-family: 'Montserrat', 'Helvetica', 'Arial', sans-serif;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .divider-line {
            border-bottom: 1.5px solid #000000;
            margin-bottom: 10px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .meta-table td {
            vertical-align: bottom;
            padding: 0 4px;
        }
        .meta-value {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
        }
        .meta-label {
            font-size: 7.5pt;
            color: #333333;
            text-transform: uppercase;
            font-weight: bold;
            padding-top: 2px;
        }
        .title-section {
            margin: 8px 0 10px 0;
            text-align: center;
        }
        .title-header-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #333333;
            text-transform: uppercase;
            margin-bottom: 3px;
            text-align: center;
        }
        .title-box-text {
            font-size: 10.5pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            line-height: 1.3;
            text-align: center;
            padding: 0 15px;
        }
        .date-line {
            width: 100%;
            margin: 8px 0 10px 0;
        }
        .date-line td {
            font-size: 9.5pt;
        }
        .cert-heading {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            margin: 8px 0 6px 0;
            text-transform: uppercase;
        }
        .cert-body-p {
            text-align: justify;
            margin-bottom: 6px;
            text-indent: 20pt;
        }
        .req-intro {
            margin-bottom: 4px;
            text-indent: 20pt;
        }
        .bullet-list {
            margin: 2px 0 6px 0;
            padding-left: 20pt;
            list-style-type: disc;
        }
        .bullet-list li {
            margin-bottom: 3px;
            text-align: justify;
        }
        .signature-section {
            margin-top: 14px;
            width: 340px;
        }
        .sig-img {
            max-height: 42px;
            margin-bottom: -6px;
            display: block;
        }
        .sig-name {
            font-weight: bold;
            font-size: 10.5pt;
            color: #000000;
        }
        .sig-title {
            font-size: 9pt;
            color: #000000;
        }
        .correspondence-footer {
            margin-top: 18px;
            border-top: 1px solid #000000;
            padding-top: 6px;
            font-size: 8pt;
            color: #000000;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('images/pwu-logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <!-- Header Letterhead (Centered Logo & Text) -->
    <div class="header-container">
        @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" class="header-logo" alt="DEMO Seal" />
        @endif
        <h1 class="header-title-main">Example Research Institution</h1>
        <p class="header-title-sub">Demo location</p>
    </div>

    <!-- Board Name -->
    <div class="board-title">
        Research Ethics Review Board
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        CERTIFICATION OF REQUIREMENT TO REVIEW IN HOST COUNTRIES
    </div>

    <div class="divider-line"></div>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td style="width: 40%;">
                <div class="meta-value">{{ $request->principal_investigator_display ?? $request->student->name }}</div>
                <div class="meta-label">PRINCIPAL INVESTIGATOR</div>
            </td>
            <td style="width: 35%; text-align: center;">
                <div class="meta-value">{{ $request->department->name ?? 'N/A' }}</div>
                <div class="meta-label">SCHOOL/DEPARTMENT</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div class="meta-value">{{ $request->control_number ?? 'ERB PROTOCOL' }}</div>
                <div class="meta-label">ERB PROTOCOL NUMBER</div>
            </td>
        </tr>
    </table>

    <!-- Title Section (Centered) -->
    <div class="title-section">
        <div class="title-header-label">PROTOCOL TITLE</div>
        <div class="title-box-text">
            {{ $request->title }}
        </div>
    </div>

    <!-- Dates Line -->
    <table class="date-line">
        <tr>
            <td style="width: 50%;">
                <strong>ISSUANCE DATE:</strong> {{ ($request->approval_date ?? now())->format('F d, Y') }}
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>DESIGNATED HOST COUNTRY:</strong> {{ $request->host_country ?? 'Overseas Study Site' }}
            </td>
        </tr>
    </table>

    <div class="divider-line"></div>

    <!-- Certification Heading & Body -->
    <div class="cert-heading">
        CERTIFICATION &amp; MANDATORY CONDITIONS
    </div>

    <p class="cert-body-p">
        This is to certify that the Example Research Institution Research Ethics Review Board has reviewed the submitted research protocol titled <strong>"{{ $request->title }}"</strong> proposed by Principal Investigator <strong>{{ $request->principal_investigator_display ?? $request->student->name }}</strong>.
    </p>

    <p class="cert-body-p">
        Because this research involves study sites, data collection, or human participants located in <strong>{{ $request->host_country ?? 'a foreign host country' }}</strong>, the proposal is hereby subject to the mandatory requirement of institutional ethics review in the host jurisdiction. In accordance with National Ethical Guidelines for Health and Life Sciences Research and DEMO Institutional Guidelines, research protocols to be implemented outside the Philippines must undergo prior review and approval by an accredited Research Ethics Committee (REC) or Institutional Review Board (IRB) of the host institution or host country prior to final DEMO institutional endorsement.
    </p>

    <p class="req-intro">
        The Principal Investigator is hereby formally directed to fulfill the following regulatory prerequisites:
    </p>

    <ul class="bullet-list">
        <li>Submit this research protocol and all participant-facing materials to an accredited Institutional Review Board or Research Ethics Committee within <strong>{{ $request->host_country ?? 'the host country' }}</strong>;</li>
        <li>Secure official, dated documentation of ethical clearance or institutional authorization from said host review body;</li>
        <li>Submit the certified host country ethical approval certificate to the DEMO ERB Secretariat for official archiving and protocol endorsement;</li>
        <li>Refrain strictly from initiating any participant recruitment, field interactions, or data gathering activities in the host site until host country clearance is obtained;</li>
        <li>Provide progress updates to the DEMO ERB Secretariat every six (6) months concerning the status of host country review.</li>
    </ul>

    <p class="cert-body-p">
        Upon receipt and validation of the official host country ethics approval, the DEMO Research Ethics Review Board will issue the final endorsement certificate. All research activities remain subject to monitoring and quality audits by the DEMO ERB.
    </p>

    <!-- Signature Section -->
    <div class="signature-section">
        @if($signatureData)
            <img src="{{ $signatureData }}" class="sig-img" alt="Chairperson Signature" />
        @endif
        <div class="sig-name">
            {{ $chairperson->name ?? 'DEMO CHAIRPERSON' }}
        </div>
        <div class="sig-title">
            Chairperson, DEMO Research Ethics Review Board
        </div>
    </div>

    <div class="correspondence-footer">
        Example Research Institution &bull; Research Ethics Review Board (ERB) &bull; Demo location
    </div>

</body>
</html>
