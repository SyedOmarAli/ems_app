<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Models\Employee;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Welcome Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * =====================
     *  ADMIN ROUTES
     * =====================
     */
    Route::middleware(['role:admin', 'verified'])->group(function () {

        // Dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Employee Management
        Route::get('/admin/employee', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
        Route::post('/admin/employee', [EmployeeController::class, 'store'])->name('admin.employee.store');
        Route::get('/admin/employee/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
        Route::put('/admin/employee/{employee}', [EmployeeController::class, 'update'])->name('admin.employee.update');
        Route::delete('/admin/employee/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');

        // Employee Details
        Route::get('admin/employee/{employee}', [EmployeeController::class, 'show'])->name('admin.employee.show');

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::get('/attendance/upload-form', fn() => Inertia::render('AttendanceUpload'))->name('attendance.upload.form');
        Route::post('/attendance/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.update_status');
        Route::get('/attendance/show', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance/upload', [AttendanceController::class, 'upload'])->name('attendance.upload');

        // Payroll
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll');
        Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');

        // Leave Management
        Route::get('/admin/leaves', [LeaveController::class, 'index'])->name('admin.leaves');
        Route::post('/admin/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('admin.leaves.approve');
        Route::post('/admin/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('admin.leaves.reject');
        Route::post('/admin/leaves/{leave}/revert', [LeaveController::class, 'revert'])->name('admin.leaves.revert');
    });
    /**
     * =====================
     *  EMPLOYEE ROUTES
     * =====================
     */
    Route::middleware(['role:employee', 'verified'])->group(function () {
        Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])
            ->name('employee.dashboard');
        Route::get('/employee/leaves', [LeaveController::class, 'myLeaves'])
            ->name('employee.leaves');
        Route::post('/employee/leaves/apply', [LeaveController::class, 'store'])
            ->name('employee.leaves.apply');


    });

});
require __DIR__ . '/auth.php';
