@component('emails.layouts.pwu', [
    'subject' => 'DEMO ERB Reviewer Invitation - ' . ($ethicsRequest->control_number ?: ('Request #' . $ethicsRequest->id)),
    'preheader' => 'You have been nominated as an ethics reviewer for an ethics protocol at Example Research Institution.',
])
    <!-- Heading -->
    <div style="margin:0 0 16px 0;">
        <h1 style="margin:0 0 8px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:20px; font-weight:700; line-height:1.3; color:#231F1C;">
            Invitation to Review Ethics Protocol
        </h1>
        <p style="margin:0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#6B6864;">
            Dear {{ $assignment->user->name ?? 'Reviewer' }},
        </p>
    </div>

    <p style="margin:0 0 20px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C;">
        The Research Ethics Review Board (DEMO ERB) Chairperson has nominated you as an expert reviewer for an ethics review application matching your specialty domain. Please review the application summary below and access the ERB Portal to accept or decline this review invitation.
    </p>

    <!-- Protocol Summary Card - flat, 1px border + neutral-50 bg per DESIGN §4 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0; background-color:#F8F7F6; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:16px 18px;">
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; margin-bottom:10px;">Protocol Overview</div>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; line-height:1.5;">
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; width:138px; vertical-align:top; white-space:nowrap;">Control / Ref No.</td>
                        <td style="padding:5px 0; font-weight:400; color:#800000; font-family:ui-monospace, SFMono-Regular, 'SF Mono', Consolas, monospace; font-size:13px; vertical-align:top;">
                            {{ $ethicsRequest->control_number ?: 'Pending Issuance' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Research Title</td>
                        <td style="padding:5px 0; font-weight:600; color:#231F1C; vertical-align:top;">{{ $ethicsRequest->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Principal Investigator</td>
                        <td style="padding:5px 0; font-weight:400; color:#231F1C; vertical-align:top;">{{ $ethicsRequest->principal_investigator_display }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Department</td>
                        <td style="padding:5px 0; font-weight:400; color:#231F1C; vertical-align:top;">{{ $ethicsRequest->department->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Research Domain</td>
                        <td style="padding:5px 0; font-weight:400; color:#231F1C; vertical-align:top;">
                            {{ $ethicsRequest->research_domain ? $ethicsRequest->research_domain->label() : 'General' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Review Type</td>
                        <td style="padding:8px 0 5px 0; vertical-align:top;">
                            <span style="display:inline-block; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:600; line-height:1; letter-spacing:0.3px; text-transform:uppercase; color:#6B6864; background-color:#FFFFFF; border:1px solid #E4E1DE; padding:6px 10px;">
                                {{ $ethicsRequest->review_type ? $ethicsRequest->review_type->label() : 'Expedited Review' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:400; line-height:1.6; color:#6B6864;">
        As an appointed reviewer, you are asked to conduct an objective evaluation of the participant informed consent process and the study methodology protocol. Please indicate your availability at your earliest convenience.
    </p>

    <!-- Primary CTA - maroon, 6px radius, Montserrat SemiBold per DESIGN §5 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0;">
        <tr>
            <td align="center" style="padding:4px 0;">
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" target="_blank" style="display:inline-block; background-color:#800000; color:#FFFFFF; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; line-height:1; text-decoration:none; text-align:center; padding:13px 28px; border:1px solid #800000;">
                    Respond to Review Invitation
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:8px 0 0 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:400; line-height:1.4; color:#6B6864;">
                If the button above does not work, copy and paste this link into your browser:<br>
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" style="color:#800000; text-decoration:underline; word-break:break-all;">{{ url('/requests/' . $ethicsRequest->id) }}</a>
            </td>
        </tr>
    </table>

    <!-- Notice Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0; background-color:#FFFFFF; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:12px 16px; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:12px; line-height:1.5; color:#6B6864;">
                <strong style="font-weight:600; color:#231F1C;">Conflict of interest?</strong> If you have a conflict of interest or cannot perform this review within the required timeframe, you may decline through the portal with a brief explanation so an alternate committee reviewer may be selected.
            </td>
        </tr>
    </table>
@endcomponent
