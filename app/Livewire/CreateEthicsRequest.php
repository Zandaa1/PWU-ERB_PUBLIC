<?php

namespace App\Livewire;

use App\Data\SubmissionInput;
use App\Models\Department;
use App\Services\SubmissionService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateEthicsRequest extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $co_authors = '';

    public ?int $department_id = null;

    public string $research_domain = '';

    public bool $is_local = true;

    public string $host_country = '';

    /** @var array<int, mixed> */
    public array $uploads = [];

    public $doc_1 = null;

    public $doc_2 = null;

    public $doc_3 = null;

    public $doc_4 = null;

    public $doc_5 = null;

    public $doc_6 = null;

    public $doc_7 = null;

    public $doc_8 = null;

    public $doc_9 = null;

    public $doc_10 = null;

    public $doc_11 = null;

    public $doc_12 = null;

    public $doc_13 = null;

    public $doc_14 = null;

    public $doc_15 = null;

    public $doc_16 = null;

    public $doc_17 = null;

    public $doc_18 = null;

    public $doc_19 = null;

    public $doc_20 = null;

    public $doc_21 = null;

    public $doc_22 = null;

    public $doc_23 = null;

    public $doc_24 = null;

    public $doc_25 = null;

    public $doc_26 = null;

    public $doc_27 = null;

    public $doc_28 = null;

    public $doc_29 = null;

    public $doc_30 = null;

    public bool $showEthicalConsiderationsModal = false;

    public bool $showDataPrivacyModal = false;

    public bool $privacyConsentAccepted = false;

    public bool $showValidationErrorModal = false;

    public bool $showSuccessModal = false;

    public ?int $createdRequestId = null;

    public string $createdRequestTitle = '';

    public function mount(): void
    {
        $activeTypes = app(SubmissionService::class)->types()->active();
        foreach ($activeTypes as $type) {
            $this->uploads[$type->id] = $this->uploads[$type->id] ?? null;
        }
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'doc_')) {
            $id = (int) substr($propertyName, 4);
            $this->uploads[$id] = $this->{$propertyName};
        }
    }

    public function clearDocumentUpload(int $typeId): void
    {
        unset($this->uploads[$typeId]);
        $this->uploads[$typeId] = null;
        if (property_exists($this, 'doc_'.$typeId)) {
            $this->{'doc_'.$typeId} = null;
        }
        $this->resetErrorBag('uploads.'.$typeId);
    }

    public function closeValidationErrorModal(): void
    {
        $this->showValidationErrorModal = false;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
    }

    protected function rules(): array
    {
        $rules = app(SubmissionService::class)->rules();

        $rules['research_domain'] = ['required', 'string', 'in:health,social,arts_music,engineering_science,other'];
        $rules['is_local'] = ['required', 'boolean'];
        $rules['host_country'] = ['nullable', 'string', 'max:150', 'required_if:is_local,false'];

        return $rules;
    }

    protected function messages(): array
    {
        $messages = app(SubmissionService::class)->messages();
        $messages['research_domain.required'] = 'Please select a research domain.';
        $messages['research_domain.in'] = 'Please select a valid research domain.';
        $messages['host_country.required_if'] = 'Please specify the host country for international studies.';

        return $messages;
    }

    protected function validationAttributes(): array
    {
        $attributes = app(SubmissionService::class)->validationAttributes();
        $attributes['research_domain'] = 'Research Domain';
        $attributes['is_local'] = 'Protocol Location';
        $attributes['host_country'] = 'Host Country';

        return $attributes;
    }

    public function openDataPrivacyModal()
    {
        $this->syncDocUploads();

        try {
            $this->validate();
            $this->showValidationErrorModal = false;
            $this->showDataPrivacyModal = true;
        } catch (ValidationException $e) {
            $this->showValidationErrorModal = true;
            throw $e;
        }
    }

    public function closeDataPrivacyModal()
    {
        $this->showDataPrivacyModal = false;
    }

    public function confirmSubmission()
    {
        if (! $this->privacyConsentAccepted) {
            $this->addError('privacyConsentAccepted', 'You must read and accept the Data Privacy Policy pursuant to RA 10173 before submitting.');
            $this->showValidationErrorModal = true;

            return;
        }

        try {
            $this->submit();
        } catch (ValidationException $e) {
            $this->showDataPrivacyModal = false;
            $this->showValidationErrorModal = true;
            throw $e;
        }
    }

    public function submit()
    {
        $this->syncDocUploads();

        $this->validate();

        $input = SubmissionInput::fromLivewire($this, auth()->id());

        $request = app(SubmissionService::class)->submit($input);

        $this->createdRequestId = $request->id;
        $this->createdRequestTitle = $request->title;
        $this->showDataPrivacyModal = false;
        $this->showValidationErrorModal = false;
        $this->showSuccessModal = true;
        $this->dispatch('form-submitted');

        session()->flash('success', 'Ethics review request submitted successfully!');
    }

    protected function syncDocUploads(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            if (isset($this->{"doc_{$i}"}) && $this->{"doc_{$i}"} !== null) {
                $this->uploads[$i] = $this->{"doc_{$i}"};
            }
        }
    }

    public function render()
    {
        return view('livewire.create-ethics-request', [
            'departments' => Department::getActive(),
            'documentTypes' => app(SubmissionService::class)->types()->active(),
        ])->layout('layouts.app', ['header' => 'New Ethics Review Submission']);
    }
}
