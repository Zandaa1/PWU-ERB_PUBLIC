@props(['status'])

@php
    $statusKey = is_object($status) ? $status->value : $status;
    $label = is_object($status) && method_exists($status, 'label') ? $status->label() : ucfirst(str_replace('_', ' ', $statusKey));

    $class = match($statusKey) {
        'submitted', 'pending' => 'badge-submitted',
        'under_review' => 'badge-under_review',
        'revisions_requested' => 'badge-revisions_requested',
        'approved' => 'badge-approved',
        'disapproved', 'rejected' => 'badge-disapproved',
        'archived' => 'badge-archived',
        default => 'badge-pending',
    };
@endphp

<span class="badge-status {{ $class }} inline-flex items-center gap-1.5 whitespace-nowrap shrink-0">
    @if($statusKey === 'revisions_requested')
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-600"></span>
        </span>
    @elseif($statusKey === 'under_review')
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-600"></span>
        </span>
    @else
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-75"></span>
    @endif
    {{ $label }}
</span>
