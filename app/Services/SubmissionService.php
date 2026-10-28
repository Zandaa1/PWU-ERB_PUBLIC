<?php

namespace App\Services;

use App\Contracts\DocumentStorage;
use App\Contracts\DocumentTypeRepository;
use App\Data\SubmissionInput;
use App\Enums\DocumentStatus;
use App\Enums\RequestStatus;
use App\Mail\EthicsRequestSubmittedMail;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use App\Models\Notification;
use App\Models\RequestDocument;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Service managing student ethics review submissions.
 *
 * Invariants:
 * - RA 10173 Data Privacy Act compliance requires explicit privacy consent prior to processing.
 * - Dynamic validation rules are built based on active DocumentType configurations.
 * - Atomic database transaction with local storage compensation on failure.
 * - Notification emails are dispatched post-commit to prevent transaction abortion on transport error.
 */
class SubmissionService
{
    public function __construct(
        private DocumentTypeRepository $types,
        private DocumentStorage $storage,
    ) {}

    public function types(): DocumentTypeRepository
    {
        return $this->types;
    }

    /**
     * Build validation rules for all active document types.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|min:5|max:255',
            'co_authors' => 'nullable|string|max:500',
            'department_id' => 'required|exists:departments,id',
            'uploads' => 'array',
        ];

        foreach ($this->types->active() as $type) {
            $key = 'uploads.'.$type->id;
            $rules[$key] = $type->is_required
                ? 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240'
                : 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240';
        }

        return $rules;
    }

    /**
     * Build friendly validation error messages.
     */
    public function messages(): array
    {
        $messages = [
            'title.required' => 'Please enter your research study title.',
            'department_id.required' => 'Please select your school or department.',
            'uploads.*.mimes' => 'Document must be a PDF file or an Image (JPG, PNG, WEBP).',
            'uploads.*.max' => 'File size must not exceed 10MB.',
        ];

        foreach ($this->types->active() as $type) {
            $messages['uploads.'.$type->id.'.required'] = 'The '.$type->name.' file is required.';
            $messages['uploads.'.$type->id.'.mimes'] = 'The '.$type->name.' must be a PDF file or an Image (JPG, PNG, WEBP).';
            $messages['uploads.'.$type->id.'.max'] = 'The '.$type->name.' file size must not exceed 10MB.';
        }

        return $messages;
    }

    /**
     * Custom field names for validation error formatting.
     */
    public function validationAttributes(): array
    {
        $attributes = [];
        foreach ($this->types->active() as $type) {
            $attributes['uploads.'.$type->id] = $type->name;
        }

        return $attributes;
    }

    /**
     * Process and persist a student ethics review submission.
     *
     * @throws ValidationException
     */
    public function submit(SubmissionInput $input): EthicsRequest
    {
        if (! $input->privacyConsentAccepted) {
            throw ValidationException::withMessages([
                'privacyConsentAccepted' => 'You must read and accept the Data Privacy Policy pursuant to RA 10173 before submitting.',
            ]);
        }

        $storedPaths = [];
        $request = null;
        $student = User::find($input->studentId);

        try {
            $request = DB::transaction(function () use ($input, $student, &$storedPaths) {
                $req = EthicsRequest::create([
                    'student_id' => $input->studentId,
                    'title' => $input->title,
                    'co_authors' => $input->coAuthors,
                    'department_id' => $input->departmentId,
                    'research_domain' => $input->researchDomain,
                    'is_local' => $input->isLocal,
                    'host_country' => $input->hostCountry,
                    'status' => RequestStatus::Submitted,
                    'last_activity_at' => now(),
                ]);

                foreach ($this->types->active() as $type) {
                    $requestDoc = RequestDocument::create([
                        'ethics_request_id' => $req->id,
                        'document_type_id' => $type->id,
                        'status' => DocumentStatus::Pending,
                    ]);

                    $file = $input->uploads[$type->id] ?? null;
                    if ($file) {
                        $path = $this->storage->store($file, $req->id, $type->id, 1);
                        $storedPaths[] = $path;

                        DocumentVersion::create([
                            'request_document_id' => $requestDoc->id,
                            'version_number' => 1,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'uploaded_by' => $input->studentId,
                            'change_notes' => 'Initial submission',
                        ]);
                    }
                }

                StatusLog::create([
                    'ethics_request_id' => $req->id,
                    'old_status' => null,
                    'new_status' => RequestStatus::Submitted->value,
                    'changed_by' => $input->studentId,
                    'notes' => 'Initial ethics review submission',
                ]);

                $committeeMembers = User::role('committee')->get();
                foreach ($committeeMembers as $member) {
                    Notification::create([
                        'user_id' => $member->id,
                        'type' => 'new_submission',
                        'message' => 'New ethics request submitted: "'.$req->title.'" by '.($student->name ?? 'Student'),
                        'link' => '/requests/'.$req->id,
                    ]);
                }

                return $req;
            });
        } catch (\Throwable $e) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }

        // Dispatch confirmation email post-commit
        if ($student && ! empty($student->email) && $request) {
            try {
                Mail::to($student->email)->send(new EthicsRequestSubmittedMail($request));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $request;
    }
}
