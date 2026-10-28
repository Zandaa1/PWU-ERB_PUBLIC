<?php

namespace App\Livewire;

use App\Models\SystemAnnouncement;
use Livewire\Component;

class ActiveAnnouncementBanner extends Component
{
    public bool $isExpanded = false;

    public function toggleExpand()
    {
        $this->isExpanded = ! $this->isExpanded;
    }

    public function render()
    {
        return view('livewire.active-announcement-banner', [
            'announcement' => SystemAnnouncement::getActive(),
        ]);
    }
}
