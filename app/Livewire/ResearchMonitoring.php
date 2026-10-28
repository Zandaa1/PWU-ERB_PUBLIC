<?php

namespace App\Livewire;

use App\Enums\MonitoringStatus;
use App\Models\ResearchMonitor;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ResearchMonitoring extends Component
{
    use WithPagination;

    public string $activeTab = 'active'; // 'active', 'host_country', 'completed'

    public string $search = '';

    public ?int $selectedMonitorId = null;

    public string $progressNotes = '';

    public bool $showReportModal = false;

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openReportModal(int $monitorId): void
    {
        $this->selectedMonitorId = $monitorId;
        $this->progressNotes = '';
        $this->showReportModal = true;
    }

    public function closeReportModal(): void
    {
        $this->showReportModal = false;
        $this->selectedMonitorId = null;
        $this->progressNotes = '';
        $this->resetValidation();
    }

    public function submitProgressReport(): void
    {
        if (! $this->selectedMonitorId) {
            return;
        }

        $this->validate([
            'progressNotes' => 'required|string|min:3',
        ]);

        $this->recordProgressReport($this->selectedMonitorId, $this->progressNotes);
        $this->closeReportModal();
    }

    public function recordProgressReport(int $monitorId, string $notes): void
    {
        $monitor = ResearchMonitor::findOrFail($monitorId);
        $monitor->update([
            'last_report_submitted_at' => now(),
            'notes' => trim(($monitor->notes ?? '')."\n[".now()->toFormattedDateString().'] '.$notes),
        ]);

        session()->flash('status', 'Progress report logged successfully.');
    }

    public function markCompleted(int $monitorId): void
    {
        $monitor = ResearchMonitor::findOrFail($monitorId);
        $monitor->update(['monitoring_status' => MonitoringStatus::Completed]);
        session()->flash('status', 'Protocol marked as completed.');
    }

    public function render()
    {
        $query = ResearchMonitor::with(['ethicsRequest.student', 'ethicsRequest.department'])
            ->when($this->search, function ($q) {
                $q->whereHas('ethicsRequest', function ($sq) {
                    $sq->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('control_number', 'like', '%'.$this->search.'%');
                });
            });

        if ($this->activeTab === 'active') {
            $query->whereIn('monitoring_status', [
                MonitoringStatus::Active,
                MonitoringStatus::RenewalDue,
                MonitoringStatus::ProgressReportDue,
            ]);
        } elseif ($this->activeTab === 'host_country') {
            $query->where('monitoring_status', MonitoringStatus::HostCountryPending);
        } else {
            $query->whereIn('monitoring_status', [
                MonitoringStatus::Completed,
                MonitoringStatus::Terminated,
            ]);
        }

        $activeCount = ResearchMonitor::whereIn('monitoring_status', [
            MonitoringStatus::Active,
            MonitoringStatus::RenewalDue,
            MonitoringStatus::ProgressReportDue,
        ])->count();

        $hostCountryCount = ResearchMonitor::where('monitoring_status', MonitoringStatus::HostCountryPending)->count();

        $completedCount = ResearchMonitor::whereIn('monitoring_status', [
            MonitoringStatus::Completed,
            MonitoringStatus::Terminated,
        ])->count();

        return view('livewire.research-monitoring', [
            'monitors' => $query->latest('approved_at')->paginate(15),
            'activeCount' => $activeCount,
            'hostCountryCount' => $hostCountryCount,
            'completedCount' => $completedCount,
        ]);
    }
}
