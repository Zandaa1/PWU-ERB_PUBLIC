<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">University Departments</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Configure academic schools and departments active in research ethics reviews.</p>
    </div>

    <!-- Sub Nav Header -->
    <div class="card-white p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="/admin/users" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Users & Roles</a>
            <a href="/admin/departments" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-maroon text-white" style="background-color: #800000;">Departments</a>
            <a href="/admin/document-types" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Document Types & Templates</a>
            <a href="/admin/archive" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Archive Queue</a>
            <a href="/admin/audit-log" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100">Audit Log</a>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session()->has('info'))
        <div class="p-4 bg-blue-50 border border-blue-300 text-blue-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-300 text-red-900 rounded-2xl font-bold font-heading flex items-center gap-2">
            <svg class="w-4 h-4 text-red-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Add Department Card -->
    <form wire:submit.prevent="addDepartment" class="card-white p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 font-heading">Add New Academic Department</h3>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1">
                <input 
                    type="text" 
                    wire:model="newDepartmentName" 
                    placeholder="e.g. School of Allied Health Sciences" 
                    class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-maroon-800 focus:outline-none" 
                />
                @error('newDepartmentName') <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p> @enderror
            </div>
            <button 
                type="submit" 
                wire:confirm="Are you sure you want to add this new department across the university?"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="addDepartment"
                class="btn-primary text-xs shrink-0 flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50"
            >
                <svg wire:loading wire:target="addDepartment" class="w-3.5 h-3.5 text-white animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="addDepartment">+ Add Department</span>
                <span wire:loading wire:target="addDepartment">Adding...</span>
            </button>
        </div>
    </form>

    <!-- Departments Table -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="toggleActive,addDepartment,deleteDepartment" class="card-white overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Department Name</th>
                    <th class="px-6 py-4">Submitted Ethics Requests</th>
                    <th class="px-6 py-4 text-right">Status / Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                @foreach($departments as $dept)
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-6 py-4 font-bold text-gray-900">{{ $dept->name }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-600">{{ $dept->ethics_requests_count }} requests</td>
                        <td class="px-6 py-4 text-right">
                            @if($dept->isCore())
                                <div class="inline-flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span class="p-1.5 rounded-lg bg-gray-50 text-gray-400 border border-gray-200 inline-flex items-center justify-center" title="Core institutional school (locked)">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2">
                                    <button 
                                        wire:click="toggleActive({{ $dept->id }})" 
                                        wire:confirm="Are you sure you want to change this department's active status?"
                                        wire:loading.attr="disabled"
                                        class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer disabled:opacity-50 {{ $dept->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                    >
                                        {{ $dept->is_active ? 'Active' : 'Deactivated' }}
                                    </button>
                                    <button 
                                        wire:click="deleteDepartment({{ $dept->id }})" 
                                        wire:confirm="Are you sure you want to delete this custom department?"
                                        wire:loading.attr="disabled"
                                        class="px-2.5 py-1 rounded text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 cursor-pointer disabled:opacity-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
