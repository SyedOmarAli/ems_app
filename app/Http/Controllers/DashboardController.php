<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $employeeCount = Employee::count();
        $presentCount = Attendance::whereDate('date', $today)->where('status', 'Present')->count();
        $absentCount = Attendance::whereDate('date', $today)->where('status', 'Absent')->count();
        $lateCount = Attendance::whereDate('date', $today)->where('status', 'Late')->count();
        $leaveCount = \App\Models\Leaves::count();

        return Inertia::render('Dashboard', [
            'employeeCount' => $employeeCount,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'lateCount' => $lateCount,
            'leaveCount' => $leaveCount,
        ]);
    }
}
