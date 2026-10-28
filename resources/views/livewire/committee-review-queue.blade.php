<div class="space-y-6">
    <!-- Header Banner (DEMO Red Box with Large White Text) -->
    <div class="rounded-3xl p-6 md:p-8 text-white shadow-sm animate-header-glide" style="background-color: #800000;">
        <h1 class="text-2xl md:text-3xl font-extrabold text-white font-heading">Ethics Review Applications Queue</h1>
        <p class="text-xs md:text-sm text-red-100 font-medium mt-1">Scan, filter, and review submitted student ethics protocol applications.</p>
    </div>

    <!-- Filters Bar -->
    <div class="card-white p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <div class="relative w-full sm:w-64">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search title, student, control no..." 
                    class="px-3.5 py-2.5 pr-9 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 w-full"
                />
                <div wire:loading wire:target="search,statusFilter,departmentFilter,resubmissionFilter" class="absolute right-3 top-3">
                    <svg class="w-4 h-4 text-red-800 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>

            <select wire:model.live="statusFilter" class="px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 shrink-0">
                <option value="">All Statuses</option>
                <option value="submitted">Submitted</option>
                <option value="under_review">Under Review</option>
                <option value="revisions_requested">Revisions Requested</option>
                <option value="approved">Approved</option>
                <option value="disapproved">Disapproved</option>
                <option value="archived">Archived</option>
            </select>

            <select wire:model.live="departmentFilter" class="px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 shrink-0">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    @php
                        $id = is_object($dept) ? ($dept->id ?? null) : (is_array($dept) ? ($dept['id'] ?? null) : null);
                        $name = is_object($dept) ? ($dept->name ?? null) : (is_array($dept) ? ($dept['name'] ?? null) : null);
                    @endphp
                    @if($id && $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endif
                @endforeach
            </select>

            <select wire:model.live="resubmissionFilter" class="px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800 shrink-0">
                <option value="">All Submissions</option>
                <option value="resubmitted">Resubmitted Docs Only</option>
                <option value="initial">Initial Submissions Only</option>
            </select>
        </div>
    </div>

    <!-- Mobile Card View (< lg) -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="search,statusFilter,departmentFilter,resubmissionFilter,gotoPage,previousPage,nextPage" class="lg:hidden space-y-4" data-auto-animate>
        @forelse($requests as $request)
            @php
                $totalDocs = $request->applicable_documents_count;
                $approvedDocs = $request->approved_documents_count;
            @endphp
            <div class="card-white p-5 space-y-4 shadow-sm">
                <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $request->status === App\Enums\RequestStatus::Approved ? $request->control_number : 'Pending' }}</span>
                        <h3 class="font-bold text-sm text-gray-900 leading-snug mt-1 font-heading">{{ $request->title }}</h3>
                    </div>
                    <div class="flex flex-col items-end">
                        <x-status-badge :status="$request->status" />
                        @if($request->hasResubmittedDocuments())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-amber-100 text-amber-900 border border-amber-300 font-heading mt-1">
                                <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Resubmitted Docs</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-400 font-semibold block text-[10px] uppercase">Student Applicant</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <div class="relative w-5 h-5 shrink-0">
                                @if($request->student->avatar)
                                    <img 
                                        src="{{ $request->student->avatar }}" 
                                        alt="{{ $request->student->name }}" 
                                        referrerpolicy="no-referrer"
                                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');"
                                        class="w-5 h-5 rounded-full object-cover shrink-0 border border-gray-200" 
                                    />
                                @endif
                                <div @class([
                                    'w-5 h-5 rounded-full bg-red-50 text-maroon font-bold text-[8px] items-center justify-center border border-red-200 shrink-0 font-heading select-none',
                                    'hidden' => !empty($request->student->avatar),
                                    'flex' => empty($request->student->avatar),
                                ]) style="background-color: #FFF5F5; color: #800000;">
                                    {{ $request->student->initials }}
                                </div>
                            </div>
                            <span class="font-semibold text-gray-800 truncate">{{ $request->student->name }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400 font-semibold block text-[10px] uppercase">Department</span>
                        <span class="text-gray-700 truncate block">{{ $request->department->name }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 gap-2">
                    <span class="px-2.5 py-1 bg-gray-100 border border-gray-200 rounded-md font-semibold text-gray-700 text-xs whitespace-nowrap">
                        {{ $approvedDocs }}/{{ $totalDocs }} approved
                    </span>

                    <a href="/requests/{{ $request->id }}" class="btn-primary py-2 px-4 text-xs whitespace-nowrap flex items-center gap-1.5">
                        <span>Review Request</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="card-white p-8 text-center text-gray-500 text-xs font-medium">
                No ethics review requests found matching your filter criteria.
            </div>
        @endforelse
    </div>

    <!-- Desktop Review Queue Table (lg+) -->
    <div wire:loading.class="opacity-60 pointer-events-none transition-opacity" wire:target="search,statusFilter,departmentFilter,resubmissionFilter,gotoPage,previousPage,nextPage" class="hidden lg:block card-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Control No.</th>
                        <th class="px-6 py-4">Study Title</th>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Progress</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium" data-auto-animate>
                    @forelse($requests as $request)
                        @php
                            $totalDocs = $request->applicable_documents_count;
                            $approvedDocs = $request->approved_documents_count;
                        @endphp
                            <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                {{ $request->status === App\Enums\RequestStatus::Approved ? $request->control_number : 'Pending' }}
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="font-bold text-gray-900 line-clamp-2 leading-snug">{{ $request->title }}</p>
                                @if($request->hasResubmittedDocuments())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-amber-100 text-amber-900 border border-amber-300 font-heading mt-1">
                                        <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Resubmitted Docs</span>
                                    </span>
                                @endif
                                <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $request->created_at->format('M d, Y h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative w-7 h-7 shrink-0">
                                        @if($request->student->avatar)
                                            <img 
                                                src="{{ $request->student->avatar }}" 
                                                alt="{{ $request->student->name }}" 
                                                referrerpolicy="no-referrer"
                                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');"
                                                class="w-7 h-7 rounded-full object-cover shrink-0 border border-gray-200" 
                                            />
                                        @endif
                                        <div @class([
                                            'w-7 h-7 rounded-full bg-red-50 text-maroon font-bold text-[10px] items-center justify-center border border-red-200 shrink-0 font-heading select-none',
                                            'hidden' => !empty($request->student->avatar),
                                            'flex' => empty($request->student->avatar),
                                        ]) style="background-color: #FFF5F5; color: #800000;">
                                            {{ $request->student->initials }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 font-heading leading-tight">{{ $request->student->name }}</p>
                                        <p class="text-[11px] text-gray-500 font-medium font-heading">{{ $request->student->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $request->department->name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 border border-gray-200 rounded font-semibold text-gray-600">
                                    {{ $approvedDocs }}/{{ $totalDocs }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$request->status" />
                            </td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation();">
                                <a href="/requests/{{ $request->id }}" class="btn-secondary py-1 px-3 text-xs">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium">
                                No ethics review requests found matching your filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
