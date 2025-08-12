<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leaves;
use Inertia\Inertia;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        \Log::info('EmployeeDashboardController@index called');
        $user = auth()->user();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $attendances = Attendance::where('employee_id', $user->employee->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        $totalDays = $attendances->count();
        $present = $attendances->where('status', 'Present')->count();
        $absent = $attendances->where('status', 'Absent')->count();
        $leaves = Leaves::where('employee_id', $user->employee->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();
        $emp_name = $user->employee->name;

        return Inertia::render('EmployeeDashboard', [
            'stats' => [
                'total_days' => $totalDays,
                'present' => $present,
                'absent' => $absent,
                'leaves' => $leaves,
                'employee_name' => $emp_name,
            ]
        ]);


    }

}
