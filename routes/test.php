<?php
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\LeaveController;

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])
            ->name('employee.dashboard')
            ->middleware(RoleMiddleware::class.':employee,auth')
            ->withoutMiddleware('verified');
        Route::get('/employee/leaves', [LeaveController::class, 'myLeaves'])
            ->name('employee.leaves');
        Route::post('/employee/leaves/apply', [LeaveController::class, 'store'])
            ->name('employee.leaves.apply');


?>