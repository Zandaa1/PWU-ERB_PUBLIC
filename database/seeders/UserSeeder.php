<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('testing')) {
            throw new \RuntimeException('Synthetic fixture accounts are restricted to automated tests.');
        }

        // Admin / Dean (Chairperson)
        $admin = User::firstOrCreate(
            ['email' => 'dean@example.test'],
            [
                'name' => 'Demo Chair',
                'password' => Hash::make('password'),
                'title' => 'Chairperson, Ethics Review Board',
                'is_chairperson' => true,
                'is_developer' => false,
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Developer
        $dev = User::firstOrCreate(
            ['email' => 'dev@example.test'],
            [
                'name' => 'System Developer',
                'password' => Hash::make('password'),
                'title' => 'Technical Lead',
                'is_chairperson' => false,
                'is_developer' => true,
                'is_active' => true,
            ]
        );
        $dev->assignRole('admin');

        // Secretary
        $secretary = User::firstOrCreate(
            ['email' => 'secretary@example.test'],
            [
                'name' => 'Demo Secretary',
                'password' => Hash::make('password'),
                'title' => 'ERB Secretary',
                'is_chairperson' => false,
                'is_developer' => false,
                'is_active' => true,
            ]
        );
        $secretary->assignRole('secretary');

        // Committee Member
        $committee = User::firstOrCreate(
            ['email' => 'reviewer@example.test'],
            [
                'name' => 'Demo Reviewer',
                'password' => Hash::make('password'),
                'title' => 'Committee Member',
                'is_chairperson' => false,
                'is_developer' => false,
                'is_active' => true,
            ]
        );
        $committee->assignRole('committee');

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@example.test'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password'),
                'title' => 'Graduate Student',
                'is_chairperson' => false,
                'is_developer' => false,
                'is_active' => true,
            ]
        );
        $student->assignRole('student');
    }
}
