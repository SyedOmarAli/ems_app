<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'employee']);

        $admin = User::where('email', 'admin@gmail.com')->first();
        $admin->assignRole('admin');

        $employee = User::where('email', 'employee1@gmail.com')->first();
        $employee->assignRole('employee');
        
    }
}
