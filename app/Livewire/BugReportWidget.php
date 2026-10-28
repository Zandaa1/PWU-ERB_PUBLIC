<?php

namespace App\Livewire;

use App\Models\BugReport;
use Livewire\Component;

class BugReportWidget extends Component
{
    public bool $isOpen = false;

    public string $category = 'bug';

    public string $subject = '';

    public string $description = '';

    public array $clientDiagnostics = [];

    public bool $submitted = false;

    protected array $rules = [
        'category' => 'required|in:bug,question,layout,feature',
        'subject' => 'required|string|min:3|max:255',
        'description' => 'required|string|min:5|max:2000',
    ];

    protected array $messages = [
        'subject.required' => 'Please enter a brief subject line.',
        'description.required' => 'Please describe the bug or issue.',
    ];

    public function openModal()
    {
        $this->reset(['subject', 'description', 'submitted']);
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function submitReport()
    {
        $this->validate();

        BugReport::create([
            'user_id' => auth()->id(),
            'category' => $this->category,
            'subject' => $this->subject,
            'description' => $this->description,
            'page_url' => request()->fullUrl() ?: url()->previous(),
            'user_agent' => request()->userAgent(),
            'status' => 'open',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.bug-report-widget');
    }
}
