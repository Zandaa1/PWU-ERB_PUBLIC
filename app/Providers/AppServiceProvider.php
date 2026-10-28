<?php

namespace App\Providers;

use App\Contracts\DocumentStorage;
use App\Contracts\DocumentTypeRepository;
use App\Livewire\CreateEthicsRequest;
use App\Models\DocumentType;
use App\Services\CachedDocumentTypeRepository;
use App\Services\LocalDocumentStorage;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Drawer\Utils;

use function Livewire\on;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DocumentTypeRepository::class, CachedDocumentTypeRepository::class);
        $this->app->singleton(DocumentStorage::class, LocalDocumentStorage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Guard against negative Blade compiler forElseCounter producing invalid `$__empty_-1` variables during exception rendering
        Blade::precompiler(function ($contents) {
            $compiler = app('blade.compiler');
            $ref = new \ReflectionProperty($compiler, 'forElseCounter');
            $ref->setAccessible(true);
            if ($ref->getValue($compiler) < 0) {
                $ref->setValue($compiler, 0);
            }

            return $contents;
        });

        // Guard against tinker var_dump of large stores (e.g. Cache::getStore()) — 128M default too low
        ini_set('memory_limit', '512M');

        if (
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->header('CF-Visitor') ||
            config('app.env') === 'production' ||
            env('FORCE_HTTPS', false)
        ) {
            URL::forceScheme('https');
        }

        // Testing mail redirect — all non-production mail → recipient@example.invalid
        // Keeps testing/demo accounts from spamming real inboxes; preserves original
        // recipient in X-Original-To and logs for audit. Disabled in `testing`
        // env so Mail::fake() assertions keep working, and disabled when
        // MAIL_REDIRECT_TO is empty (production).
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $redirectTo = config('mail.redirect_to');

            if (empty($redirectTo) || app()->environment('testing')) {
                return;
            }

            // Only redirect in local/staging/demo — never in production unless explicitly set and desired.
            // Allow production redirect if MAIL_REDIRECT_TO is set and APP_ENV !== 'production' check is bypassed via explicit flag.
            // Current policy: redirect when not production.
            if (app()->environment('production') && ! config('app.debug')) {
                return;
            }

            $message = $event->message;

            // Symfony Email vs RawMessage — only handle Email
            if (! method_exists($message, 'getTo') || ! method_exists($message, 'getHeaders')) {
                return;
            }

            $originalTo = $message->getTo();
            if (empty($originalTo)) {
                return;
            }

            $originalAddresses = array_map(fn ($a) => $a->getAddress(), $originalTo);
            // Skip if already directed to redirect address
            if (in_array(strtolower($redirectTo), array_map('strtolower', $originalAddresses), true) && count($originalAddresses) === 1) {
                return;
            }

            // Preserve original recipients for audit
            $headers = $message->getHeaders();
            if (! $headers->has('X-Original-To')) {
                $headers->addTextHeader('X-Original-To', implode(', ', $originalAddresses));
            }
            if (! $headers->has('X-Redirected-By')) {
                $headers->addTextHeader('X-Redirected-By', 'MAIL_REDIRECT_TO='.$redirectTo);
            }

            Log::info('Mail redirected for testing', [
                'original_to' => $originalAddresses,
                'redirect_to' => $redirectTo,
                'subject' => method_exists($message, 'getSubject') ? $message->getSubject() : null,
            ]);

            // Replace To with redirect address — keep name as "Original Name (redirected)"
            $message->to($redirectTo);
        });

        // Bust DocumentType active cache when types change
        DocumentType::saved(fn () => CachedDocumentTypeRepository::bust());
        DocumentType::deleted(fn () => CachedDocumentTypeRepository::bust());

        // Future-proof Livewire 4 dynamic doc_* uploads beyond the 1..500 declared range:
        // HandleComponents::updateProperty checks Utils::getPublicPropertiesDefinedOnSubclass
        // (isDefault=true only), so truly dynamic doc_501+ would still throw.
        // Inject any doc_* path into the reflection cache on-the-fly so
        // dehydrate/hydrate/update all see it as a public property.
        on('update', function ($component, string $path, mixed $value) {
            if (! $component instanceof CreateEthicsRequest) {
                return;
            }
            if (! str_starts_with($path, 'doc_')) {
                return;
            }

            $class = get_class($component);

            // Ensure instance has the property (AllowDynamicProperties) so getValue works.
            if (! property_exists($component, $path)) {
                $component->{$path} = null;
            }

            // Inject into BaseUtils::$reflectionCache so getPublicPropertiesDefinedOnSubclass sees it.
            $ref = new \ReflectionClass(Utils::class);
            $prop = $ref->getProperty('reflectionCache');
            $prop->setAccessible(true);
            $cache = $prop->getValue();

            if (! isset($cache[$class][$path])) {
                $cache[$class][$path] = ['name' => $path, 'type' => null];
                $prop->setValue(null, $cache);
            }
        });
    }
}
