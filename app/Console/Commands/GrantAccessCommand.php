<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GrantAccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erb:grant-access 
                            {email : The user\'s @example.test institutional email address}
                            {--role= : Assign spatie role (admin, committee, student)}
                            {--developer : Grant system developer status (is_developer = true)}
                            {--no-developer : Revoke system developer status (is_developer = false)}
                            {--chairperson : Designate user as acting ERB Chairperson}
                            {--no-chairperson : Remove acting ERB Chairperson designation}
                            {--activate : Ensure user account is active}
                            {--name= : Name of the user (if pre-creating a new record before first OAuth login)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant or modify user roles, developer privileges, and chairperson designations in production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));

        if (! str_ends_with($email, '@'.config('services.google.allowed_domain', 'example.test'))) {
            $this->warn("Note: '{$email}' does not end in @".config('services.google.allowed_domain', 'example.test').'.');
            if (! $this->confirm('Do you want to proceed with this email anyway?', false)) {
                return self::FAILURE;
            }
        }

        // Find existing user or pre-create placeholder user for upcoming Google OAuth login
        $user = User::firstOrNew(['email' => $email]);

        if (! $user->exists) {
            $name = $this->option('name') ?: explode('@', $email)[0];
            $user->name = ucwords(str_replace(['.', '_', '-'], ' ', $name));
            $user->is_active = true;
            $user->save();
            $this->info("Created new pre-provisioned user account for {$email}.");
        }

        if ($this->option('activate')) {
            $user->is_active = true;
        }

        // Handle Spatie role assignment
        $roleOption = $this->option('role');
        if ($roleOption) {
            $validRoles = ['admin', 'committee', 'student'];
            $roleName = strtolower(trim($roleOption));

            if (! in_array($roleName, $validRoles, true)) {
                $this->error("Invalid role '{$roleOption}'. Valid roles: ".implode(', ', $validRoles));

                return self::FAILURE;
            }

            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $user->syncRoles([$roleName]);
            $this->info("Assigned role '{$roleName}' to {$email}.");
        }

        // Handle developer status
        if ($this->option('developer')) {
            $user->is_developer = true;
            // Developers automatically receive admin role if no specific role was requested
            if (! $user->roles()->exists()) {
                Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
                $user->assignRole('admin');
            }
            $this->info("Granted system developer status to {$email}.");
        } elseif ($this->option('no-developer')) {
            $user->is_developer = false;
            $this->info("Revoked system developer status from {$email}.");
        }

        // Handle chairperson status
        if ($this->option('chairperson')) {
            DB::transaction(function () use ($user) {
                User::where('is_chairperson', true)->update(['is_chairperson' => false]);
                $user->is_chairperson = true;
                $user->save();
            });
            $this->info("Designated {$email} as the active ERB Chairperson.");
        } elseif ($this->option('no-chairperson')) {
            $user->is_chairperson = false;
            $this->info("Removed ERB Chairperson designation from {$email}.");
        }

        $user->save();

        // Print summary table
        $user->refresh();
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Active', $user->is_active ? 'YES' : 'NO'],
                ['Roles', implode(', ', $user->getRoleNames()->toArray()) ?: 'None (Student)'],
                ['Developer', $user->is_developer ? 'YES' : 'NO'],
                ['Chairperson', $user->is_chairperson ? 'YES' : 'NO'],
            ]
        );

        return self::SUCCESS;
    }
}
