<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\EnsureActiveUser;
use App\Livewire\Admin\ArchiveQueue;
use App\Livewire\Admin\AuditLog;
use App\Livewire\Admin\DepartmentManagement;
use App\Livewire\Admin\DocumentTypeManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\CreateEthicsRequest;
use App\Livewire\Developer\DevPanel;
use App\Livewire\DocumentViewer;
use App\Livewire\RequestDetail;
use App\Livewire\ResearchMonitoring;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\EthicsRequest;
use App\Models\SystemAnnouncement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Guest Auth Routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    // If there is an active flash session message or query error, render dynamically
    if (session()->has('error') || session()->has('status') || request()->has('error')) {
        return response()->view('auth.login')->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    $announcement = SystemAnnouncement::getActive();
    $cacheKey = 'rendered_view:login_page:'.($announcement ? $announcement->id.'_'.$announcement->updated_at?->timestamp : 'none');

    $html = Cache::remember($cacheKey, now()->addHours(6), function () {
        return view('auth.login')->render();
    });

    $etag = md5($html);

    return response($html)
        ->header('Content-Type', 'text/html; charset=UTF-8')
        ->header('ETag', '"'.$etag.'"')
        ->header('Cache-Control', 'public, max-age=120, stale-while-revalidate=600');
})->name('login');

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// Public FAQ & Guidelines
Route::get('/faq', function () {
    $docTypes = DocumentType::getActive();

    // Authenticated users must always render dynamically to display their own active identity
    if (auth()->check()) {
        return response()->view('faq', ['documentTypes' => $docTypes])
            ->header('Cache-Control', 'no-cache, private, must-revalidate');
    }

    $announcement = SystemAnnouncement::getActive();
    $dtChecksum = md5($docTypes->map(fn ($dt) => $dt->id.':'.$dt->name.':'.$dt->template_file_path.':'.$dt->is_required)->join('|'));
    $cacheKey = 'rendered_view:faq_page_guest:'.($announcement ? $announcement->id.'_'.$announcement->updated_at?->timestamp : 'none').':'.$dtChecksum;

    $html = Cache::remember($cacheKey, now()->addHours(6), function () use ($docTypes) {
        return view('faq', ['documentTypes' => $docTypes])->render();
    });

    $etag = md5($html);

    return response($html)
        ->header('Content-Type', 'text/html; charset=UTF-8')
        ->header('ETag', '"'.$etag.'"')
        ->header('Cache-Control', 'public, max-age=120, stale-while-revalidate=600');
});

// Public Privacy Policy (Compliant with Google OAuth Verification & DPA 2012)
Route::get('/privacy-policy', function () {
    $html = Cache::remember('rendered_view:privacy_policy_page', now()->addHours(12), function () {
        return view('privacy-policy')->render();
    });

    $etag = md5($html);

    return response($html)
        ->header('Content-Type', 'text/html; charset=UTF-8')
        ->header('ETag', '"'.$etag.'"')
        ->header('Cache-Control', 'public, max-age=3600, stale-while-revalidate=86400');
})->name('privacy-policy');

Route::get('/privacy', function () {
    return redirect('/privacy-policy');
});

// Public Terms of Service
Route::get('/terms', function () {
    $html = Cache::remember('rendered_view:terms_page', now()->addHours(12), function () {
        return view('terms')->render();
    });

    $etag = md5($html);

    return response($html)
        ->header('Content-Type', 'text/html; charset=UTF-8')
        ->header('ETag', '"'.$etag.'"')
        ->header('Cache-Control', 'public, max-age=3600, stale-while-revalidate=86400');
})->name('terms');

// Public XML Sitemap for Search & Verification Crawlers
Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(config('app.url'), '/');
    $lastMod = date('Y-m-d');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $xml .= '<url><loc>'.$baseUrl.'/</loc><lastmod>'.$lastMod.'</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>';
    $xml .= '<url><loc>'.$baseUrl.'/privacy-policy</loc><lastmod>'.$lastMod.'</lastmod><changefreq>monthly</changefreq><priority>0.8</priority></url>';
    $xml .= '<url><loc>'.$baseUrl.'/terms</loc><lastmod>'.$lastMod.'</lastmod><changefreq>monthly</changefreq><priority>0.8</priority></url>';
    $xml .= '<url><loc>'.$baseUrl.'/faq</loc><lastmod>'.$lastMod.'</lastmod><changefreq>weekly</changefreq><priority>0.9</priority></url>';
    $xml .= '</urlset>';

    return response($xml, 200, [
        'Content-Type' => 'application/xml; charset=UTF-8',
        'Cache-Control' => 'public, max-age=3600, stale-while-revalidate=86400',
    ]);
});

// Dev quick login (Local/Debug mode only)
Route::get('/developer', function () {
    if (! config('app.demo_mode')) {
        abort(404);
    }

    if (auth()->check()) {
        return redirect('/dashboard')->with('info', 'Active session detected. Please sign out before selecting a test persona.');
    }

    return view('auth.developer');
});
Route::get('/auth/dev-login/{role}', [GoogleController::class, 'devLogin']);
Route::post('/logout', [GoogleController::class, 'logout']);

// Authenticated Routes
Route::middleware(['auth', EnsureActiveUser::class])->group(function () {
    Route::get('/dashboard', DashboardController::class);

    // Student Submissions & Requests
    Route::get('/requests/create', CreateEthicsRequest::class);
    Route::get('/requests/ethical-considerations', [DocumentController::class, 'ethicalConsiderations']);
    Route::get('/requests/{id}', RequestDetail::class);

    // Document Viewer & Streaming
    Route::get('/requests/{requestId}/documents/{documentId}', DocumentViewer::class);
    Route::get('/documents/stream/{versionId}', [DocumentController::class, 'stream']);
    Route::get('/documents/download/{versionId}', [DocumentController::class, 'download']);
    Route::get('/templates/assessment-consent/download', [DocumentController::class, 'downloadConsentAssessmentTemplate']);
    Route::get('/templates/assessment-protocol/download', [DocumentController::class, 'downloadProtocolAssessmentTemplate']);
    Route::get('/templates/{typeId}/download', [DocumentController::class, 'downloadTemplate'])->whereNumber('typeId');
    Route::get('/requests/{id}/certificate', [DocumentController::class, 'certificate']);
    Route::get('/requests/{id}/host-country-certificate', [DocumentController::class, 'hostCountryCertificate']);

    // Research Monitoring Module
    Route::get('/monitoring', ResearchMonitoring::class)
        ->middleware(['auth', EnsureActiveUser::class, 'permission:view-monitoring']);

    // Admin Panel Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/', UserManagement::class);
        Route::get('/users', UserManagement::class);
        Route::get('/departments', DepartmentManagement::class);
        Route::get('/document-types', DocumentTypeManagement::class);
        Route::get('/archive', ArchiveQueue::class);
        Route::get('/audit-log', AuditLog::class);
    });

    // Developer Operational Panel Routes
    Route::get('/admin/dev', DevPanel::class);
    Route::get('/admin/dev/test-certificate', function () {
        if (! auth()->user() || ! auth()->user()->is_developer) {
            abort(403, 'Developer panel access restricted.');
        }

        $mockRequest = new EthicsRequest([
            'title' => 'Test Protocol: Ethical Considerations in AI-Assisted Medical Diagnostics',
            'control_number' => 'ERBTEST_'.date('Y').'_99999',
            'approval_date' => now(),
            'expiry_date' => now()->addYear(),
        ]);
        $mockRequest->student = new User([
            'name' => 'Demo Researcher (Test Student)',
        ]);
        $mockRequest->department = new Department([
            'name' => 'School of Nursing & Allied Health Sciences',
        ]);

        $chairperson = User::where('is_chairperson', true)->first() ?? auth()->user();
        $mockSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $pdf = Pdf::loadView('certificates.erb-certificate', [
            'request' => $mockRequest,
            'chairperson' => $chairperson,
            'signatureData' => $mockSignature,
        ])->setPaper([0, 0, 612, 936], 'portrait');

        return $pdf->stream('TEST_ERB_Ethics_Certificate.pdf');
    });
});
