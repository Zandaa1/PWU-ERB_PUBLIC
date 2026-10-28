<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

/**
 * Admin Panel: User and Role Management.
 *
 * Institutional Rules:
 * - Admin (Dean) manages user active status and spatie role assignments (Student, Committee, Admin).
 * - Developer system accounts (`is_developer = true`) are strictly hidden from standard admin views
 *   and cannot be deactivated or modified via the Admin Panel.
 * - Chairperson designation is strictly reserved for Developer authority under Developer Operations (`/admin/dev`).
 */
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function assignRole(int $userId, string $roleName)
    {
        $user = User::where('is_developer', false)->findOrFail($userId);
        $user->syncRoles([$roleName]);
        session()->flash('success', "Role for {$user->name} updated to {$roleName}.");
    }

    public function toggleActive(int $userId)
    {
        $user = User::where('is_developer', false)->findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('success', 'User status updated.');
    }

    public function render()
    {
        $query = User::where('is_developer', false);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->roleFilter) {
            $query->role($this->roleFilter);
        }

        $users = $query->latest()->paginate(15);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => Role::all(),
        ])->layout('layouts.app', ['header' => 'Admin Panel: Users & Roles']);
    }
}
