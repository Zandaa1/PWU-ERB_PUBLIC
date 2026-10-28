<?php

namespace App\Livewire\Admin;

use App\Enums\RequestStatus;
use App\Models\EthicsRequest;
use Livewire\Component;
use Livewire\WithPagination;

class ArchiveQueue extends Component
{
    use WithPagination;

    public string $search = '';

    public function archive(int $requestId)
    {
        $request = EthicsRequest::findOrFail($requestId);
        $request->update(['status' => RequestStatus::Archived]);
        session()->flash('success', "Request {$request->control_number} archived successfully.");
    }

    public function keepActive(int $requestId)
    {
        $request = EthicsRequest::findOrFail($requestId);
        $request->update(['last_activity_at' => now()]);
        session()->flash('success', 'Request marked active.');
    }

    public function render()
    {
        $query = EthicsRequest::with(['student', 'department'])
            ->where(function ($q) {
                $q->where('status', RequestStatus::Archived)
                    ->orWhere(function ($sub) {
                        $sub->whereNotNull('last_activity_at')
                            ->where('last_activity_at', '<', now()->subYear());
                    })
                    ->orWhere(function ($sub) {
                        $sub->whereNull('last_activity_at')
                            ->where('created_at', '<', now()->subYear());
                    });
            });

        if ($this->search) {
            $query->where(function ($sq) {
                $sq->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('control_number', 'like', '%'.$this->search.'%');
            });
        }

        $requests = $query->latest()->paginate(15);

        return view('livewire.admin.archive-queue', [
            'requests' => $requests,
        ])->layout('layouts.app', ['header' => 'Admin Panel: Archive Queue']);
    }
}
