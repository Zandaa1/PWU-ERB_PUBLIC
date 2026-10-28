<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">System Audit Log</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1 font-heading">Read-only audit feed of status changes, role updates, and certificate issuances.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="/admin/users" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Users & Roles</a>
            <a href="/admin/departments" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Departments</a>
            <a href="/admin/document-types" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Document Types & Templates</a>
            <a href="/admin/archive" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Archive Queue</a>
            <a href="/admin/audit-log" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-maroon text-white" style="background-color: #800000;">Audit Log</a>
        </div>
    </div>

    <!-- Audit Log Feed -->
    <div class="card-white p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 font-heading border-b border-gray-200 pb-3">Immutable System Audit Trail</h3>
        <div class="divide-y divide-gray-100" data-auto-animate>
            @forelse($logs as $log)
                <div class="py-3 flex items-start justify-between gap-4 text-xs">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900">{{ $log->user->name }}</span>
                            <span class="text-gray-400 font-mono">({{ $log->user->email }})</span>
                        </div>
                        <p class="text-gray-800 font-medium">{{ $log->notes }}</p>
                        @if($log->ethicsRequest)
                            <p class="text-[11px] text-gray-500 font-mono">
                                Target Request: {{ $log->ethicsRequest->control_number ?? 'Draft' }} &bull; "{{ $log->ethicsRequest->title }}"
                            </p>
                        @endif
                    </div>
                    <span class="text-gray-400 font-mono shrink-0">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No audit log entries recorded yet.</div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="pt-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
