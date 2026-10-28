<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DEMO ERB Certificate of Approval</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 30pt 40pt 25pt 40pt;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
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
            font-family: 'Helvetica', 'Arial', sans-serif;
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
            margin-top: 10px;
            width: 320px;
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
        .recommendation-box {
            margin-top: 10px;
            font-size: 7.5pt;
            color: #000000;
            text-align: center;
        }
        .checkbox-table {
            width: 85%;
            margin: 4px auto 0 auto;
            border-collapse: collapse;
            text-align: left;
        }
        .checkbox-table td {
            vertical-align: top;
            font-size: 7.5pt;
        }
        .line-fill {
            border-bottom: 1px solid #777777;
            display: inline-block;
            width: 100%;
            height: 8px;
        }
        .correspondence-footer {
            margin-top: 12px;
            border-top: 1px solid #000000;
            padding-top: 6px;
            font-size: 8pt;
            color: #000000;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Letterhead (Centered Logo & Text) -->
    <div class="header-container">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/pwu-logo.png'))) }}" class="header-logo" alt="DEMO Seal" />
        <h1 class="header-title-main">Example Research Institution</h1>
        <p class="header-title-sub">Demo location</p>
    </div>

    <!-- Board Name -->
    <div class="board-title">
        Research Ethics Review Board
    </div>

    <!-- Document Title -->
    <div class="doc-title">
        CERTIFICATE OF APPROVAL AND ETHICS REVIEW
    </div>

    <div class="divider-line"></div>

    <!-- Metadata Grid -->
    <table class="meta-table">
        <tr>
            <td style="width: 45%;">
                <div class="meta-value">{{ $request->principal_investigator_display }}</div>
                <div class="meta-label">PRINCIPAL INVESTIGATOR</div>
            </td>
            <td style="width: 35%; text-align: center;">
                <div class="meta-value">{{ $request->department->name }}</div>
                <div class="meta-label">SCHOOL/DEPARTMENT</div>
            </td>
            <td style="width: 20%; text-align: right;">
                <div class="meta-value">{{ $request->control_number }}</div>
                <div class="meta-label">ERB PROTOCOL NUMBER</div>
            </td>
        </tr>
    </table>

    <!-- Title Section (Centered) -->
    <div class="title-section">
        <div class="title-header-label">TITLE</div>
        <div class="title-box-text">
            {{ $request->title }}
        </div>
    </div>

    <!-- Dates Line -->
    <table class="date-line">
        <tr>
            <td style="width: 50%;">
                <strong>APPROVAL DATE:</strong> {{ $request->approval_date->format('F d, Y') }}
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>EXPIRY DATE:</strong> {{ $request->expiry_date->format('F d, Y') }}
            </td>
        </tr>
    </table>

    <div class="divider-line"></div>

    <!-- Certification Heading & Body -->
    <div class="cert-heading">
        CERTIFICATION
    </div>

    <p class="cert-body-p">
        The Example Research Institution Research Ethics Review Board reviewed the above-named research project. The proposal was found to be acceptable on ethical grounds. The principal investigator has the responsibility for any other administrative or regulatory approvals that may pertain to this research project, and for ensuring that the authorized research is carried out according to the conditions outlined in the original protocol submitted for ethics review. This Certificate of Approval is valid for the above time period.
    </p>

    <p class="req-intro">
        The Principal Researcher is required to notify the Secretary of the Ethics Committee, via amendment or progress report, of :
    </p>

    <ul class="bullet-list">
        <li>Any significant change to the project and the reason for that change, including an indication of ethical implications (if any);</li>
        <li>Serious adverse effects on participants and the action taken to address those effects;</li>
        <li>Any other unforeseen events or unexpected developments that merit notification;</li>
        <li>The inability of the Principal Researcher to continue in that role, or any other change in research personnel involved in the project;</li>
        <li>A delay of more than 12 months in the commencement of the project; and,</li>
        <li>Termination or closure of the project. Additionally, the Principal Researcher is required to submit;</li>
        <li>A Progress Report on the anniversary of approval and on completion of the project (forms to be provided);</li>
        <li>Submission of revisions, specifically cited in the comments and recommendation section of this certificate.</li>
    </ul>

    <p class="cert-body-p">
        This is an illustrative software demonstration generated from synthetic records. It does not represent approval by an institution or ethics committee and is not a valid research approval certificate.
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


</body>
</html>
