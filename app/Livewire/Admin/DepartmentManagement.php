<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use Livewire\Component;

/**
 * Admin Panel: Department Management.
 *
 * Rules:
 * - 14 Core institutional departments are permanently locked from deletion and deactivation.
 * - Custom departments can be added, toggled, or deleted.
 * - If a custom department has existing student submissions, it is safely soft-deactivated.
 */
class DepartmentManagement extends Component
{
    public string $newDepartmentName = '';

    public function addDepartment()
    {
        $this->validate([
            'newDepartmentName' => 'required|string|min:3|max:255|unique:departments,name',
        ], [
            'newDepartmentName.required' => 'Please enter a name for the new department.',
            'newDepartmentName.min' => 'Department name must be at least 3 characters.',
            'newDepartmentName.unique' => 'A department with this name already exists.',
        ]);

        Department::create([
            'name' => trim($this->newDepartmentName),
            'is_active' => true,
        ]);

        $this->newDepartmentName = '';
        $this->resetErrorBag();
        session()->flash('success', 'New department added successfully.');
    }

    public function deleteDepartment(int $deptId)
    {
        $dept = Department::findOrFail($deptId);

        if ($dept->isCore()) {
            session()->flash('error', "Core institutional department '{$dept->name}' is permanently locked and cannot be deleted.");

            return;
        }

        $name = $dept->name;

        if ($dept->ethicsRequests()->exists()) {
            $dept->update(['is_active' => false]);
            session()->flash('info', "Department '{$name}' is currently referenced in student submissions. It has been deactivated to preserve academic records.");

            return;
        }

        $dept->delete();
        session()->flash('success', "Department '{$name}' deleted successfully.");
    }

    public function toggleActive(int $deptId)
    {
        $dept = Department::findOrFail($deptId);

        if ($dept->isCore()) {
            session()->flash('error', "Core institutional department '{$dept->name}' cannot be deactivated.");

            return;
        }

        $dept->update(['is_active' => ! $dept->is_active]);
        session()->flash('success', "Department status updated for '{$dept->name}'.");
    }

    public function render()
    {
        return view('livewire.admin.department-management', [
            'departments' => Department::withCount('ethicsRequests')->orderBy('name')->get(),
        ])->layout('layouts.app', ['header' => 'Admin Panel: Departments']);
    }
}
