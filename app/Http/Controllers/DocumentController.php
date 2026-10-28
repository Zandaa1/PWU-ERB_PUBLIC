<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\EthicsRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controller handling secure streaming and downloading of uploaded ethics review documents,
 * institutional template files, and generated approval certificates.
 *
 * Security Invariants:
 * - Documents are NEVER served statically from `public/`.
 * - Student role users can only access documents belonging to their own submissions.
 * - Reviewers and administrators have institution-wide access for audit and committee duties.
 * - HTTP byte-range requests and conditional ETag/Last-Modified caching (HTTP 304) supported.
 */
class DocumentController extends Controller
{
    /**
     * Inline file streaming with conditional caching headers.
     */
    public function stream(int $versionId): BinaryFileResponse|Response
    {
        $version = DocumentVersion::with('requestDocument.ethicsRequest', 'requestDocument.documentType')->findOrFail($versionId);

        $user = auth()->user();
        if ($user->hasRole('student') && $user->id !== $version->requestDocument->ethicsRequest->student_id) {
            abort(403, 'Unauthorized access to document.');
        }

        [$fullPath, $disk] = $this->resolveDiskPath($version->file_path);

        $ext = strtolower(pathinfo($version->file_path, PATHINFO_EXTENSION));
        $imageMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
        ];
        $mimeType = $imageMimes[$ext] ?? $this->resolveMimeType($fullPath, $ext);

        $filename = $version->file_name ?: ('v'.$version->version_number.'_'.str_replace(' ', '_', $version->requestDocument->documentType->name).'.'.($ext ?: 'pdf'));

        return $this->fileResponse($fullPath, $mimeType, 'inline; filename="'.$filename.'"');
    }

    /**
     * Attachment file download.
     */
    public function download(int $versionId): BinaryFileResponse
    {
        $version = DocumentVersion::with('requestDocument.ethicsRequest', 'requestDocument.documentType')->findOrFail($versionId);

        $user = auth()->user();
        if ($user->hasRole('student') && $user->id !== $version->requestDocument->ethicsRequest->student_id) {
            abort(403, 'Unauthorized access to document.');
        }

        [$fullPath] = $this->resolveDiskPath($version->file_path);
        $ext = strtolower(pathinfo($version->file_path, PATHINFO_EXTENSION)) ?: 'pdf';
        $filename = $version->file_name ?: ('v'.$version->version_number.'_'.str_replace(' ', '_', $version->requestDocument->documentType->name).'.'.$ext);

        return response()->download($fullPath, $filename);
    }

    /**
     * Download an official template file for a given document type.
     */
    public function downloadTemplate(int $typeId): BinaryFileResponse
    {
        $type = DocumentType::findOrFail($typeId);
        if (! $type->template_file_path) {
            abort(404, 'No template file uploaded for this document type.');
        }

        [$fullPath] = $this->resolveDiskPath($type->template_file_path);

        return response()->download($fullPath, str_replace(' ', '_', $type->name).'_Template.pdf');
    }

    /**
     * Download the DEMO ERB Informed Consent Assessment Form template.
     */
    public function downloadConsentAssessmentTemplate(): BinaryFileResponse
    {
        $filename = 'DEMO_ERB_Form_Informed_Consent_Assessment.docx';
        $path = $this->resolveTemplatePath($filename);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Download the DEMO ERB Protocol Assessment Form template.
     */
    public function downloadProtocolAssessmentTemplate(): BinaryFileResponse
    {
        $filename = 'DEMO_ERB_Form_Protocol_Assessment.docx';
        $path = $this->resolveTemplatePath($filename);

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Resolve assessment template file path.
     */
    private function resolveTemplatePath(string $filename): string
    {
        $paths = [
            storage_path('app/templates/'.$filename),
            storage_path('app/private/templates/'.$filename),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        abort(404, 'Assessment template not configured. Ask the local administrator to supply a reviewed template in private storage.');
    }

    /**
     * Stream or download an approved institutional ERB clearance certificate.
     */
    public function certificate(int $requestId): BinaryFileResponse|Response
    {
        $request = EthicsRequest::findOrFail($requestId);

        $user = auth()->user();
        if ($user->hasRole('student') && $user->id !== $request->student_id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        if (! $request->certificate_path) {
            abort(404, 'Certificate not found or not yet generated.');
        }

        [$fullPath] = $this->resolveDiskPath($request->certificate_path);

        return $this->fileResponse($fullPath, 'application/pdf', 'inline; filename="DEMO_ERB_Certificate_'.($request->control_number ?? $request->id).'.pdf"');
    }

    /**
     * Stream or download a host country requirement certificate.
     */
    public function hostCountryCertificate(int $requestId): BinaryFileResponse|Response
    {
        $request = EthicsRequest::findOrFail($requestId);

        $user = auth()->user();
        if ($user->hasRole('student') && $user->id !== $request->student_id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        if (! $request->host_country_cert_path) {
            abort(404, 'Host country certificate not found or not yet generated.');
        }

        [$fullPath] = $this->resolveDiskPath($request->host_country_cert_path);

        return $this->fileResponse($fullPath, 'application/pdf', 'inline; filename="DEMO_ERB_Host_Country_Requirement_'.($request->control_number ?? $request->id).'.pdf"');
    }

    /**
     * View ethical considerations guide.
     */
    public function ethicalConsiderations()
    {
        return view('requests.ethical-considerations');
    }

    /**
     * Resolve absolute disk path across private, local, and public storage disks.
     */
    private function resolveDiskPath(string $relativePath): array
    {
        $privatePath = Storage::disk('private')->path($relativePath);
        if (is_file($privatePath)) {
            return [$privatePath, 'private'];
        }

        $localPath = Storage::disk('local')->path($relativePath);
        if (is_file($localPath)) {
            return [$localPath, 'local'];
        }

        $publicPath = Storage::disk('public')->path($relativePath);
        if (is_file($publicPath)) {
            return [$publicPath, 'public'];
        }

        abort(404, 'File not found on server.');
    }

    /**
     * Safely resolve MIME type using PHP mime_content_type detection.
     */
    private function resolveMimeType(string $fullPath, string $ext): string
    {
        if (function_exists('mime_content_type') && is_file($fullPath)) {
            $detected = @mime_content_type($fullPath);
            if ($detected && $detected !== 'text/plain') {
                return $detected;
            }
        }

        return $ext === 'pdf' ? 'application/pdf' : 'application/octet-stream';
    }

    /**
     * Construct an HTTP response supporting conditional 304 Not Modified validation.
     */
    private function fileResponse(string $fullPath, string $mimeType, string $contentDisposition): BinaryFileResponse|Response
    {
        $size = filesize($fullPath);
        $lastModified = filemtime($fullPath);
        $etag = '"'.md5($fullPath.$size.$lastModified).'"';
        $lastModifiedHttp = gmdate('D, d M Y H:i:s', $lastModified).' GMT';

        $ifNoneMatch = request()->header('If-None-Match');
        $ifModifiedSince = request()->header('If-Modified-Since');
        if ($ifNoneMatch === $etag || ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified)) {
            return response('', 304, [
                'ETag' => $etag,
                'Last-Modified' => $lastModifiedHttp,
                'Cache-Control' => 'private, max-age=3600, must-revalidate',
            ]);
        }

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $contentDisposition,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600, must-revalidate',
            'ETag' => $etag,
            'Last-Modified' => $lastModifiedHttp,
        ];

        $response = response()->file($fullPath, $headers);
        $response->headers->set('X-Sendfile-Type', 'X-Accel-Redirect');

        return $response;
    }
}
