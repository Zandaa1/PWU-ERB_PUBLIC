<?php

namespace App\Livewire\Admin;

use App\Models\StatusLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;

    public function render()
    {
        $logs = StatusLog::with(['ethicsRequest', 'user'])
            ->latest()
            ->paginate(20);

        return view('livewire.admin.audit-log', [
            'logs' => $logs,
        ])->layout('layouts.app', ['header' => 'Admin Panel: System Audit Log']);
    }
}
