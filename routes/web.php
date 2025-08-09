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
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard', [
//         'employeeCount' => Employee::count(),
//     ]);
// })->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
   
    // Admin Routes
   
    Route::middleware(['auth', 'role:admin'])->group(function() {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('employee', EmployeeController::class);

    Route::get('/attendance', [AttendanceController::class, 'index'] )
    ->middleware(['auth', 'verified'])->name('attendance');

    Route::get('/attendance/sample-csv', [AttendanceController::class, 'downloadSample'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.sample.csv');

    Route::get('/attendance/upload-form', function () {
    return Inertia::render('AttendanceUpload');
})->middleware(['auth', 'verified'])->name('attendance.upload.form');


    Route::post('/attendance/update-status', [AttendanceController::class, 'updateStatus'] )
    ->middleware(['auth', 'verified'])->name('attendance.update_status');

    Route::get('/attendance/show', [AttendanceController::class, 'show'] )
    ->middleware(['auth', 'verified'])->name('attendance.show');

    Route::post('/attendance/upload', [AttendanceController::class, 'upload'])
    ->middleware(['auth', 'verified'])->name('attendance.upload');

    Route::get('/payroll', [PayrollController::class, 'index'] )
    ->middleware(['auth', 'verified'])->name('payroll');

    Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');

    Route::get('/admin/leaves', [LeaveController::class, 'index'])->name('admin.leaves');
    Route::post('/admin/leaves/{leave}', [LeaveController::class, 'approve'])->name('admin.leaves.approve');
    Route::post('/admin/leaves/{leave}', [LeaveController::class, 'reject'])->name('admin.leaves.reject');  

});

// Employee Routes

Route::middleware(['auth', 'role:employee'])->group(function() {
    Route::get('/employee/dashboard',[EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/employee/leaves',[LeaveController::class, 'myLeaves'])->name('employee.leaves');
    Route::post('/employee/leaves/apply',[LeaveController::class, 'store'])->name('employee.leaves.apply');
});
    
});




require __DIR__.'/auth.php';
