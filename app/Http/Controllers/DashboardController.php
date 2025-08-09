<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index() {
        $today = Carbon::today();
        $totalEmployees = Employee::count();
        
        $presentCount = Attendance::where('date', $today)
        ->where('status', 'Present')->count();

        $absentCount = Attendance::where('date', $today)
        ->where('status', 'Absent')->count();
        
        $lateCount = Attendance::where('date', $today)
        ->where('status', 'Late')->count();
        
        $leaveCount = Attendance::where('date', $today)
        ->where('status', 'Approved')->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_employees' => $totalEmployees,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'leave' => $leaveCount,
            ]
            ]);
    }
}
