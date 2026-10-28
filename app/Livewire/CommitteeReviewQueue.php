<?php

namespace App\Livewire;

use App\Enums\DocumentStatus;
use App\Models\Department;
use App\Models\EthicsRequest;
use Livewire\Component;
use Livewire\WithPagination;

class CommitteeReviewQueue extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $departmentFilter = '';

    public string $resubmissionFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatingResubmissionFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EthicsRequest::with(['student', 'department', 'requestDocuments.documentType', 'requestDocuments.versions']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('control_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('student', function ($sq) {
                        $sq->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->departmentFilter) {
            $query->where('department_id', $this->departmentFilter);
        }

        if ($this->resubmissionFilter === 'resubmitted') {
            $query->whereHas('requestDocuments', function ($dq) {
                $dq->where('status', DocumentStatus::Pending)
                    ->whereHas('versions', function ($vq) {
                        $vq->where('version_number', '>', 1);
                    });
            });
        } elseif ($this->resubmissionFilter === 'initial') {
            $query->whereDoesntHave('requestDocuments.versions', function ($vq) {
                $vq->where('version_number', '>', 1);
            });
        }

        $requests = $query->latest()->paginate(15);

        $departments = Department::getActive();

        return view('livewire.committee-review-queue', [
            'requests' => $requests,
            'departments' => $departments,
        ])->layout('layouts.app', ['header' => 'Committee Review Queue']);
    }
}
