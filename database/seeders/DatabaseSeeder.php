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
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'employee']);

    $admin = User::firstOrCreate(
        ['email' => 'admin@gmail.com'],
        ['name' => 'Admin', 'password' => bcrypt('password')]
    );
    $admin->assignRole('admin');

    // Optional example employee
    $employee = User::firstOrCreate(
        ['email' => 'employee1@gmail.com'],
        ['name' => 'Employee One', 'password' => bcrypt('password')]
    );
    $employee->assignRole('employee');
}

}
