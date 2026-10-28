<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

/**
 * Controller managing single-sign-on (SSO) authentication via Google OAuth 2.0.
 *
 * Invariants:
 * - DEMO domain restriction: Only `@example.test` email domains are authorized.
 * - Automatic role provisioning: New users are automatically assigned the `student` role upon initial Google OAuth callback.
 * - Inactive user protection: Deactivated users are blocked from session creation.
 * - Demo mode bypass: Dev persona logins (`/auth/dev-login/{role}`) are strictly guarded by `APP_DEMO_MODE=true`.
 */
class GoogleController extends Controller
{
    /**
     * Redirect the user to Google OAuth consent screen.
     */
    public function redirect()
    {
        if (config('services.google.client_id') === 'mock-google-client-id' || empty(config('services.google.client_id'))) {
            if (config('app.demo_mode')) {
                // In demo/test mode without real Google Cloud credentials, log in as demo student
                return redirect('/auth/dev-login/student');
            }

            return redirect('/')->with('error', 'Google Sign-In is not configured. Please supply valid GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET credentials in .env.');
        }

        try {
            return Socialite::driver('google')
                ->with(['hd' => config('services.google.allowed_domain', 'example.test')])
                ->redirect();
        } catch (\Throwable $e) {
            return redirect('/')->with('error', 'Google Sign-In failed to connect. Please check credentials or try again later.');
        }
    }

    /**
     * Handle the incoming OAuth callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Authentication failed or was cancelled. Please try again.');
        }

        $email = $googleUser->getEmail();
        $domain = Str::after($email, '@');
        $allowedDomain = config('services.google.allowed_domain', 'example.test');

        if ($domain !== $allowedDomain) {
            return redirect('/')->with('error', 'Please use your official DEMO school Google account (@'.$allowedDomain.') to continue.');
        }

        $avatarUrl = $googleUser->getAvatar();
        $storedAvatar = $avatarUrl;

        if ($avatarUrl) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                    ->get($avatarUrl);

                if ($response->successful()) {
                    $avatarPath = 'avatars/'.md5($email).'.jpg';
                    Storage::disk('public')->put($avatarPath, $response->body());
                    $storedAvatar = Storage::disk('public')->url($avatarPath);
                }
            } catch (\Throwable $e) {
                // Keep default Google URL if download times out
            }
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'avatar' => $storedAvatar,
            ]
        );

        if (! $user->is_active) {
            return redirect('/')->with('error', 'Your account is currently inactive. Please contact the ERB office.');
        }

        // Ensure baseline roles exist in database
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);

        // Check if user email is authorized as a system developer via config
        $developerEmails = array_filter(array_map('trim', explode(',', config('auth.developer_emails', ''))));
        $isConfiguredDeveloper = in_array(strtolower($email), array_map('strtolower', $developerEmails), true);

        if ($isConfiguredDeveloper) {
            $user->update(['is_developer' => true]);
            if (! $user->hasRole('admin')) {
                $user->assignRole($adminRole);
            }
        } elseif (! $user->roles()->exists()) {
            $user->assignRole($studentRole);
        }

        // Cleanly invalidate any existing session and regenerate token
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect('/dashboard');
    }

    /**
     * Demo Persona Quick-Login for development, testing, and staging walkthroughs.
     */
    public function devLogin(Request $request, string $role)
    {
        if (! config('app.demo_mode')) {
            abort(404);
        }

        // If a real Google user is actively logged in, prevent dev persona switching without explicit sign out
        if (Auth::check() && ! empty(Auth::user()->google_id)) {
            return redirect('/dashboard')->with('error', 'Active Google user session detected. Please sign out before switching to demo test personas.');
        }

        // Clear and flush existing session state and tokens to prevent state bleed
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        switch ($role) {
            case 'admin':
                $user = User::where('email', 'dean@example.test')->firstOrFail();
                break;
            case 'developer':
                $user = User::where('email', 'dev@example.test')->firstOrFail();
                break;
            case 'committee':
                $user = User::where('email', 'reviewer@example.test')->firstOrFail();
                break;
            case 'secretary':
                Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);
                $user = User::firstOrCreate(
                    ['email' => 'secretary@example.test'],
                    ['name' => 'Demo Secretary', 'is_active' => true]
                );
                if (! $user->hasRole('secretary')) {
                    $user->assignRole('secretary');
                }
                break;
            default:
                $user = User::where('email', 'student@example.test')->firstOrFail();
                break;
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    /**
     * Secure session termination and token invalidation.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
