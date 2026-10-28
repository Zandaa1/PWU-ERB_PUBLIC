@component('emails.layouts.pwu', [
    'subject' => 'Ethics Review Submission Acknowledgment - ' . $ethicsRequest->control_number,
    'preheader' => 'Your ethics review application ' . $ethicsRequest->control_number . ' has been received by the DEMO ERB.',
])
    <!-- Heading -->
    <div style="margin:0 0 16px 0;">
        <h1 style="margin:0 0 8px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:20px; font-weight:700; line-height:1.3; color:#231F1C;">
            Your application has been received
        </h1>
        <p style="margin:0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#6B6864;">
            Dear {{ $ethicsRequest->student->name ?? 'Researcher' }},
        </p>
    </div>

    <p style="margin:0 0 20px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C;">
        This is to confirm that your research ethics review application has been officially received by the <strong style="font-weight:600; color:#231F1C;">Example Research Institution Research Ethics Review Board (DEMO ERB)</strong> and queued for initial evaluation.
    </p>

    <!-- Application Summary Card - flat, 1px border + neutral-50 bg per DESIGN §4 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0; background-color:#F8F7F6; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:16px 18px;">
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; margin-bottom:10px;">Application Summary</div>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; line-height:1.5;">
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; width:138px; vertical-align:top; white-space:nowrap;">Control Number</td>
                        <td style="padding:5px 0; font-weight:400; color:#800000; font-family:ui-monospace, SFMono-Regular, 'SF Mono', Consolas, monospace; font-size:13px; vertical-align:top;">{{ $ethicsRequest->control_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Research Title</td>
                        <td style="padding:5px 0; font-weight:600; color:#231F1C; vertical-align:top;">{{ $ethicsRequest->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Department</td>
                        <td style="padding:5px 0; font-weight:400; color:#231F1C; vertical-align:top;">{{ $ethicsRequest->department->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">Status</td>
                        <td style="padding:8px 0 5px 0; vertical-align:top;">
                            <span style="display:inline-block; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:600; line-height:1; letter-spacing:0.3px; text-transform:uppercase; color:#6B6864; background-color:#FFFFFF; border:1px solid #E4E1DE; padding:6px 10px;">
                                {{ $ethicsRequest->status->label() }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:400; line-height:1.6; color:#6B6864;">
        You will receive email notifications as reviewers assess individual documents and provide feedback. Please ensure your contact information remains up to date and monitor the portal for revision requests.
    </p>

    <!-- Primary CTA - maroon, 6px radius, Montserrat SemiBold per DESIGN §5 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0;">
        <tr>
            <td align="center" style="padding:4px 0;">
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" target="_blank" style="display:inline-block; background-color:#800000; color:#FFFFFF; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; line-height:1; text-decoration:none; text-align:center; padding:13px 28px; border:1px solid #800000;">
                    View Application Details
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:8px 0 0 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:400; line-height:1.4; color:#6B6864;">
                If the button does not work, copy and paste this link into your browser:<br>
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" style="color:#800000; text-decoration:underline; word-break:break-all;">{{ url('/requests/' . $ethicsRequest->id) }}</a>
            </td>
        </tr>
    </table>

    <!-- Help note flat container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0; background-color:#FFFFFF; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:12px 16px; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:12px; line-height:1.5; color:#6B6864;">
                <strong style="font-weight:600; color:#231F1C;">What happens next?</strong> Initial evaluation &rarr; Committee review &rarr; Decision. Response times vary by submission volume; the portal dashboard always shows the latest status.
            </td>
        </tr>
    </table>
@endcomponent
