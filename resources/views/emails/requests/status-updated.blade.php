@component('emails.layouts.pwu', [
    'subject' => 'Ethics Review Decision Update: ' . strtoupper($ethicsRequest->status->label()) . ' - ' . $ethicsRequest->control_number,
    'preheader' => 'Your ethics review application ' . $ethicsRequest->control_number . ' status is now ' . $ethicsRequest->status->label() . '.',
])
    <div style="margin:0 0 16px 0;">
        <div style="display:inline-block; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; background-color:#F8F7F6; border:1px solid #E4E1DE; padding:6px 8px; margin-bottom:12px;">
            Decision Update
        </div>
        <h1 style="margin:0 0 8px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:20px; font-weight:700; line-height:1.3; color:#231F1C;">
            Ethics Review Update
        </h1>
        <p style="margin:0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#6B6864;">
            Dear {{ $ethicsRequest->student->name ?? 'Researcher' }},
        </p>
    </div>

    <p style="margin:0 0 20px 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C;">
        An update has been made regarding your research ethics review application with the DEMO Research Ethics Review Board.
    </p>

    <!-- Summary Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 16px 0; background-color:#F8F7F6; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:16px 18px;">
                <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; margin-bottom:10px;">Application Summary</div>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; line-height:1.5;">
                    <tr>
                        <td style="padding:5px 12px 5px 0; font-weight:600; color:#6B6864; width:138px; vertical-align:top;">Control Number</td>
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
                        <td style="padding:8px 12px 5px 0; font-weight:600; color:#6B6864; vertical-align:top;">New Status</td>
                        <td style="padding:8px 0 5px 0; vertical-align:top;">
                            {{-- Status badge: distinct from maroon per DESIGN §2 - use semantic palette, not brand color --}}
                            @php
                                $label = $ethicsRequest->status->label();
                                $lower = strtolower($label);
                                $badgeStyle = 'display:inline-block; font-family:\'Montserrat\', Helvetica, Arial, sans-serif; font-size:11px; font-weight:600; line-height:1; letter-spacing:0.3px; text-transform:uppercase; padding:6px 10px; border:1px solid;';
                                if (str_contains($lower, 'approve')) {
                                    $badgeStyle .= ' color:#166534; background-color:#f0fdf4; border-color:#bbf7d0;';
                                } elseif (str_contains($lower, 'revision') || str_contains($lower, 'resubmit')) {
                                    $badgeStyle .= ' color:#9a3412; background-color:#fff7ed; border-color:#fed7aa;';
                                } elseif (str_contains($lower, 'disapprove') || str_contains($lower, 'reject')) {
                                    $badgeStyle .= ' color:#991b1b; background-color:#fef2f2; border-color:#fecaca;';
                                } elseif (str_contains($lower, 'review')) {
                                    $badgeStyle .= ' color:#92400e; background-color:#fffbeb; border-color:#fde68a;';
                                } elseif (str_contains($lower, 'archive')) {
                                    $badgeStyle .= ' color:#475569; background-color:#f8fafc; border-color:#e2e8f0;';
                                } else {
                                    $badgeStyle .= ' color:#44403c; background-color:#FFFFFF; border-color:#E4E1DE;';
                                }
                            @endphp
                            <span style="{{ $badgeStyle }}">{{ $label }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(!empty($remarks))
        <!-- Remarks - flat container, NOT border-l-8, per mandatory UI rules -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 16px 0; background-color:#FFFFFF; border:1px solid #E4E1DE;">
            <tr>
                <td style="padding:14px 16px; border-top:3px solid #800000;">
                    <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:10px; font-weight:600; line-height:1; letter-spacing:0.6px; text-transform:uppercase; color:#6B6864; margin-bottom:6px;">Committee Remarks / Guidance</div>
                    <div style="font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; line-height:1.6; color:#231F1C; white-space:pre-wrap;">{{ $remarks }}</div>
                </td>
            </tr>
        </table>
    @endif

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0 0 20px 0;">
        <tr>
            <td align="center" style="padding:8px 0 0 0;">
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" target="_blank" style="display:inline-block; background-color:#800000; color:#FFFFFF; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; line-height:1; text-decoration:none; text-align:center; padding:13px 28px; border:1px solid #800000;">
                    View Application in Portal
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding:8px 0 0 0; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:11px; font-weight:400; line-height:1.4; color:#6B6864;">
                <a href="{{ url('/requests/' . $ethicsRequest->id) }}" style="color:#800000; text-decoration:underline; word-break:break-all;">{{ url('/requests/' . $ethicsRequest->id) }}</a>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:0; background-color:#F8F7F6; border:1px solid #E4E1DE;">
        <tr>
            <td style="padding:12px 16px; font-family:'Montserrat', Helvetica, Arial, sans-serif; font-size:12px; line-height:1.5; color:#6B6864;">
                For questions about this decision, open the application in the portal and use the document comment thread to correspond with the review panel.
            </td>
        </tr>
    </table>
@endcomponent
