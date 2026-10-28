<div class="space-y-6">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">User & Role Management</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Manage accounts, assign roles, and grant admin access across the university.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="/admin/users" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-maroon text-white" style="background-color: #800000;">Users & Roles</a>
            <a href="/admin/departments" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Departments</a>
            <a href="/admin/document-types" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Document Types & Templates</a>
            <a href="/admin/archive" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Archive Queue</a>
            <a href="/admin/audit-log" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Audit Log</a>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search user name/email..." class="px-3 py-1.5 pr-8 bg-gray-50 border border-gray-300 rounded text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-maroon-800" />
                <div wire:loading wire:target="search,roleFilter" class="absolute right-2.5 top-2">
                    <svg class="w-3.5 h-3.5 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
            <select wire:model.live="roleFilter" class="px-3 py-1.5 bg-gray-50 border border-gray-300 rounded text-xs font-medium focus:bg-white focus:outline-none">
                <option value="">All Roles</option>
                <option value="student">Student</option>
                <option value="committee">Committee</option>
                <option value="admin">Admin</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="search,roleFilter,assignRole,toggleActive,gotoPage,previousPage,nextPage" class="card-white overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">User Details</th>
                    <th class="px-6 py-4">Title / Role</th>
                    <th class="px-6 py-4">Role Assignment</th>
                    <th class="px-6 py-4">Chairperson Status</th>
                    <th class="px-6 py-4">Account Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                @foreach($users as $u)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $u->name }}</div>
                            <div class="text-gray-500 text-[11px] font-mono">{{ $u->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 font-semibold">{{ $u->title ?? 'N/A' }}</div>
                            <div class="text-[10px] uppercase font-bold text-maroon mt-0.5" style="color: #800000;">
                                {{ implode(', ', $u->getRoleNames()->toArray()) ?: 'Student' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <select 
                                wire:change="assignRole({{ $u->id }}, $event.target.value)" 
                                wire:loading.attr="disabled"
                                class="px-2 py-1 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-700 cursor-pointer disabled:opacity-50"
                            >
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}" {{ $u->hasRole($r->name) ? 'selected' : '' }}>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            @if($u->is_chairperson)
                                <span class="px-2.5 py-1 rounded text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>Acting Chairperson</span>
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-medium">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button 
                                wire:click="toggleActive({{ $u->id }})" 
                                wire:confirm="Are you sure you want to change this user account's active status?"
                                wire:loading.attr="disabled"
                                @class([
                                    'px-2.5 py-1 rounded text-xs font-semibold cursor-pointer disabled:opacity-50',
                                    'bg-green-100 text-green-800' => $u->is_active,
                                    'bg-red-100 text-red-800' => !$u->is_active,
                                ])
                            >
                                {{ $u->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
