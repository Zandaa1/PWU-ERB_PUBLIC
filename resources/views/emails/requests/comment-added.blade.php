@component('emails.layouts.pwu', [
    'subject' => 'New Reviewer Feedback: ' . ($document->documentType->name ?? 'Document') . ' - ' . $document->ethicsRequest->control_number,
    'preheader' => 'A reviewer has added feedback on ' . ($document->documentType->name ?? 'your document') . ' (' . $document->ethicsRequest->control_number . ').',
])
    <div style="margin:0 0 16px 0;">
        <div style="display:inline-block; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; background-color:#F8F7F6; border:1px solid #E4E1DE; padding:6px 8px; margin-bottom:12px;">
            Reviewer Feedback
        </div>
        <h1 style="margin:0 0 8px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:20px; font-weight:700; line-height:1.3; color:#231F1C;">
            New reviewer feedback received
        </h1>
        <p style="margin:0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#6B6864;">
            Dear {{ $document->ethicsRequest->student->name ?? 'Researcher' }},
        </p>
    </div>

    <p style="margin:0 0 16px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C;">
        A reviewer from the DEMO Research Ethics Review Board has posted feedback on <strong style="font-weight:600; color:#231F1C;">{{ $document->documentType->name ?? 'Document' }}</strong> for application <strong style="font-weight:600; color:#800000; font-family:ui-monospace, SFMono-Regular, 'SF Mono', Consolas, monospace;">{{ $document->ethicsRequest->control_number }}</strong>.
    </p>

    <!-- Feedback Card - flat, top maroon accent 3px, no shadow, no border-l-8 -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 16px 0; background-color:#FFFFFF; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:14px 16px; border-top:3px solid #800000;">
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:600; line-height:1.4; color:#6B6864; margin-bottom:8px;">
                    <span style="color:#231F1C;">{{ $comment->commenter->name ?? 'Ethics Committee Reviewer' }}</span>
                    <span style="color:#E4E1DE;">&nbsp;&bull;&nbsp;</span>
                    <span>{{ $document->documentType->name ?? 'Document' }}</span>
                    <span style="color:#E4E1DE;">&nbsp;&bull;&nbsp;</span>
                    <span style="font-weight:400;">{{ $comment->created_at ? $comment->created_at->format('M j, Y g:i A') : now()->format('M j, Y g:i A') }}</span>
                </div>
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C; white-space:pre-wrap;">{{ $comment->comment_text }}</div>
            </td>
        </tr>
    </table>

    <!-- Application Summary - neutral-50 flat card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0; background-color:#F8F7F6; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:14px 16px;">
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; margin-bottom:8px;">Application Reference</div>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; line-height:1.5;">
                    <tr>
                        <td style="padding:4px 12px 4px 0; font-weight:600; color:#6B6864; width:138px; vertical-align:top;">Control Number</td>
                        <td style="padding:4px 0; font-weight:400; color:#800000; font-family:ui-monospace, SFMono-Regular, 'SF Mono', Consolas, monospace; font-size:13px; vertical-align:top;">{{ $document->ethicsRequest->control_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 12px 4px 0; font-weight:600; color:#6B6864; vertical-align:top;">Research Title</td>
                        <td style="padding:4px 0; font-weight:600; color:#231F1C; vertical-align:top;">{{ $document->ethicsRequest->title }}</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 12px 4px 0; font-weight:600; color:#6B6864; vertical-align:top;">Document</td>
                        <td style="padding:4px 0; font-weight:400; color:#231F1C; vertical-align:top;">{{ $document->documentType->name ?? 'Document' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0;">
        <tr>
            <td align="center" style="padding:4px 0;">
                <a href="{{ url('/requests/' . $document->ethics_request_id . '/documents/' . $document->id) }}" target="_blank" style="display:inline-block; background-color:#800000; color:#FFFFFF; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; line-height:1; text-decoration:none; text-align:center; padding:13px 28px; border:1px solid #800000;">
                    Open Document in Viewer
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:8px 0 0 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:400; line-height:1.4; color:#6B6864;">
                <a href="{{ url('/requests/' . $document->ethics_request_id . '/documents/' . $document->id) }}" style="color:#800000; text-decoration:underline; word-break:break-all;">{{ url('/requests/' . $document->ethics_request_id . '/documents/' . $document->id) }}</a>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0; background-color:#FFFFFF; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:12px 16px; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:12px; line-height:1.5; color:#6B6864;">
                Reply directly in the portal document viewer to keep the review thread consolidated.
            </td>
        </tr>
    </table>
@endcomponent
