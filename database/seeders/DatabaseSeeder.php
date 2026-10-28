<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            DocumentTypeSeeder::class,
            ControlNumberSeeder::class,

        ]);

        if (app()->environment('testing')) {
            $this->call(UserSeeder::class);
        }

        if (app()->environment('testing') && config('app.demo_mode')) {
            $this->call(SampleApprovableRequestSeeder::class);
        }
    }
}
