<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $open = false;

    public function toggle()
    {
        $this->open = ! $this->open;
    }

    public function markAsRead(int $id)
    {
        Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->update(['is_read' => true]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->select(['id', 'user_id', 'type', 'message', 'link', 'is_read', 'created_at'])
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return view('livewire.notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
