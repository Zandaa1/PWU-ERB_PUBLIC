<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssetBundleOptimizationTest extends TestCase
{
    public function test_layout_does_not_contain_external_signature_pad_cdn(): void
    {
        $layoutContent = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/signature_pad', $layoutContent);
    }

    public function test_app_js_imports_and_exposes_signature_pad(): void
    {
        $jsContent = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('SignaturePad', $jsContent);
    }
}
