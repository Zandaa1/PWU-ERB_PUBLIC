<?php

namespace App\Livewire\Admin;

use App\Models\DocumentType;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Admin Panel: Document Types & Template Management.
 *
 * Lifecycle & Data Invariants:
 * - 8 Core institutional document types are permanently locked from deletion.
 * - Custom document types can be added, template files replaced, or deleted.
 * - Supports both local uploaded template files (.pdf, .docx) and external Google Docs / Drive URLs via focused modal.
 * - If a custom document type is deleted while in use by existing student submissions,
 *   it is automatically soft-deactivated (`is_active = false`) to preserve historical student
 *   records and audit logs while hiding it from future submissions.
 */
class DocumentTypeManagement extends Component
{
    use WithFileUploads;

    public string $newTypeName = '';

    public bool $newTypeRequired = true;

    // Modal State
    public bool $showTemplateModal = false;

    public ?int $selectedTypeId = null;

    public string $modalMode = 'google_doc'; // 'google_doc' or 'file'

    public string $modalTemplateUrl = '';

    public $modalTemplateFile = null;

    public function addDocumentType()
    {
        $this->validate([
            'newTypeName' => 'required|string|min:3|max:255|unique:document_types,name',
        ], [
            'newTypeName.required' => 'Please enter a name for the new document type.',
            'newTypeName.min' => 'Document type name must be at least 3 characters.',
            'newTypeName.unique' => 'A document type with this name already exists.',
        ]);

        $maxSort = DocumentType::max('sort_order') ?? 0;

        DocumentType::create([
            'name' => trim($this->newTypeName),
            'is_required' => $this->newTypeRequired,
            'is_active' => true,
            'sort_order' => $maxSort + 1,
        ]);

        $this->newTypeName = '';
        $this->resetErrorBag();
        session()->flash('success', 'New document type requirement added successfully.');
    }

    public function openTemplateModal(int $typeId)
    {
        $this->selectedTypeId = $typeId;
        $dt = DocumentType::findOrFail($typeId);

        if ($dt->is_google_doc) {
            $this->modalMode = 'google_doc';
            $this->modalTemplateUrl = $dt->template_file_path;
        } elseif ($dt->template_file_path) {
            $this->modalMode = 'file';
            $this->modalTemplateUrl = '';
        } else {
            $this->modalMode = 'google_doc';
            $this->modalTemplateUrl = '';
        }

        $this->modalTemplateFile = null;
        $this->showTemplateModal = true;
        $this->resetErrorBag();
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->selectedTypeId = null;
        $this->modalTemplateFile = null;
        $this->modalTemplateUrl = '';
        $this->resetErrorBag();
    }

    public function saveGoogleDocLink()
    {
        $this->validate([
            'modalTemplateUrl' => 'required|url|max:500',
        ], [
            'modalTemplateUrl.required' => 'Please enter a valid Google Docs/Drive URL.',
            'modalTemplateUrl.url' => 'Please enter a valid URL starting with http:// or https://',
        ]);

        $type = DocumentType::findOrFail($this->selectedTypeId);
        $url = trim($this->modalTemplateUrl);

        $type->update(['template_file_path' => $url]);
        $this->closeTemplateModal();

        session()->flash('success', "Google Docs template link updated for '{$type->name}'.");
    }

    public function saveTemplateFile()
    {
        $this->validate([
            'modalTemplateFile' => 'required|file|mimes:pdf,docx,doc|max:10240',
        ], [
            'modalTemplateFile.required' => 'Please choose a file to upload.',
            'modalTemplateFile.mimes' => 'Template file must be a PDF or Word document (.pdf, .docx, .doc).',
            'modalTemplateFile.max' => 'Template file size must not exceed 10MB.',
        ]);

        $type = DocumentType::findOrFail($this->selectedTypeId);
        $path = $this->modalTemplateFile->store('templates', 'public');

        $type->update(['template_file_path' => $path]);
        $this->closeTemplateModal();

        session()->flash('success', "Template file uploaded for '{$type->name}'.");
    }

    public function removeTemplate()
    {
        if (! $this->selectedTypeId) {
            return;
        }

        $type = DocumentType::findOrFail($this->selectedTypeId);
        $type->update(['template_file_path' => null]);
        $this->closeTemplateModal();

        session()->flash('success', "Template removed for '{$type->name}'.");
    }

    public function deleteDocumentType(int $typeId)
    {
        $type = DocumentType::findOrFail($typeId);

        if ($type->isCore()) {
            session()->flash('error', "Core institutional document type '{$type->name}' is permanently locked and cannot be deleted.");

            return;
        }

        $name = $type->name;

        // If submissions exist for this document type, soft-deactivate to prevent breaking student thesis records
        if ($type->requestDocuments()->exists()) {
            $type->update(['is_active' => false]);
            session()->flash('info', "Document type '{$name}' is currently attached to existing student submissions. It has been deactivated so new submissions will not require it, while preserving historical thesis records.");

            return;
        }

        $type->delete();
        session()->flash('success', "Document type '{$name}' deleted successfully.");
    }

    public function toggleActive(int $typeId)
    {
        $type = DocumentType::findOrFail($typeId);

        if ($type->isCore()) {
            session()->flash('error', "Core institutional document type '{$type->name}' cannot be deactivated.");

            return;
        }

        $type->update(['is_active' => ! $type->is_active]);
        session()->flash('success', "Document type status updated for '{$type->name}'.");
    }

    public function render()
    {
        $selectedType = $this->selectedTypeId ? DocumentType::find($this->selectedTypeId) : null;

        return view('livewire.admin.document-type-management', [
            'documentTypes' => DocumentType::orderBy('sort_order')->get(),
            'selectedType' => $selectedType,
        ])->layout('layouts.app', ['header' => 'Admin Panel: Document Types & Templates']);
    }
}
