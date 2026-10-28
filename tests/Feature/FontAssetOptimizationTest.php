<?php

namespace Tests\Feature;

use Tests\TestCase;

class FontAssetOptimizationTest extends TestCase
{
    public function test_views_contain_google_fonts_montserrat_cdn_links(): void
    {
        $layoutContent = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $loginContent = file_get_contents(resource_path('views/auth/login.blade.php'));
        $welcomeContent = file_get_contents(resource_path('views/welcome.blade.php'));
        $emailContent = file_get_contents(resource_path('views/emails/layouts/pwu.blade.php'));

        $this->assertStringContainsString('fonts.googleapis.com', $layoutContent);
        $this->assertStringContainsString('family=Montserrat', $layoutContent);

        $this->assertStringContainsString('fonts.googleapis.com', $loginContent);
        $this->assertStringContainsString('family=Montserrat', $loginContent);

        $this->assertStringContainsString('fonts.googleapis.com', $welcomeContent);
        $this->assertStringContainsString('family=Montserrat', $welcomeContent);

        $this->assertStringContainsString('fonts.googleapis.com', $emailContent);
        $this->assertStringContainsString('family=Montserrat', $emailContent);
    }

    public function test_app_css_contains_google_fonts_import_and_tailwind_theme(): void
    {
        $cssContent = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('fonts.googleapis.com/css2?family=Montserrat', $cssContent);
        $this->assertStringContainsString('@theme', $cssContent);
        $this->assertStringContainsString('--font-sans:', $cssContent);
        $this->assertStringContainsString('--font-heading:', $cssContent);
        $this->assertStringContainsString('--font-body:', $cssContent);
        $this->assertStringContainsString('Montserrat', $cssContent);
    }

    public function test_compiled_css_manifest_and_assets_exist(): void
    {
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $this->assertArrayHasKey('resources/css/app.css', $manifest);
            $cssFile = public_path('build/'.$manifest['resources/css/app.css']['file']);
            $this->assertFileExists($cssFile);
        }
    }
}
