<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">Archive Queue</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1 font-heading">Manage inactive research ethics applications and permanent searchable archives.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="/admin/users" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Users & Roles</a>
            <a href="/admin/departments" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Departments</a>
            <a href="/admin/document-types" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Document Types & Templates</a>
            <a href="/admin/archive" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-maroon text-white" style="background-color: #800000;">Archive Queue</a>
            <a href="/admin/audit-log" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Audit Log</a>
        </div>

        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search archive records..." class="px-3 py-1.5 pr-8 bg-gray-50 border border-gray-300 rounded text-xs font-medium focus:bg-white focus:outline-none" />
            <div wire:loading wire:target="search" class="absolute right-2.5 top-2">
                <svg class="w-3.5 h-3.5 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Archive Queue Table -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="search,archive,keepActive,gotoPage,previousPage,nextPage" class="card-white overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Control No.</th>
                    <th class="px-6 py-4">Study Title & Student</th>
                    <th class="px-6 py-4">Last Activity</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                @forelse($requests as $r)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-6 py-4 font-mono font-bold text-gray-700">{{ $r->control_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $r->title }}</p>
                            <p class="text-[11px] text-gray-500">{{ $r->student->name }} &bull; {{ $r->department->name }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-gray-600">
                            {{ $r->last_activity_at ? $r->last_activity_at->diffForHumans() : $r->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$r->status" />
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($r->status !== App\Enums\RequestStatus::Archived)
                                <button 
                                    wire:click="archive({{ $r->id }})" 
                                    wire:confirm="Are you sure you want to permanently archive this ethics request?"
                                    wire:loading.attr="disabled"
                                    wire:target="archive({{ $r->id }})"
                                    class="btn-danger py-1 px-3 text-[11px] cursor-pointer disabled:opacity-50"
                                >
                                    Confirm Archive
                                </button>
                            @endif
                            <button 
                                wire:click="keepActive({{ $r->id }})" 
                                wire:loading.attr="disabled"
                                wire:target="keepActive({{ $r->id }})"
                                class="btn-secondary py-1 px-3 text-[11px] cursor-pointer disabled:opacity-50"
                            >
                                Keep Active
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No requests currently flagged for archiving.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($requests->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
