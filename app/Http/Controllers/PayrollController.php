<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use Carbon\CarbonInterval;

class PayrollController extends Controller
{
    // Show payroll list
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->get();

        return Inertia::render('Payroll', [
            'payrolls' => $payrolls,
        ]);
    }

    // Generate payroll for a selected month
    public function generate(Request $request)
{
    $request->validate([
        'start_date' => 'required|date_format:Y-m-d',
        'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
    ]);

    $startDate = Carbon::parse($request->start_date)->startOfDay();
    $endDate = Carbon::parse($request->end_date)->endOfDay();

    // store month as the FIRST day of that month (DATE column: Y-m-d)
    $monthDate = $startDate->copy()->startOfMonth()->toDateString();

    $employees = Employee::all();

    foreach ($employees as $employee) {
        // Attendances for the selected range
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // Totals
        $totalMinutes = 0;
        $basicMinutes = 480; // 8 hours
        $overtime = 0;

        // consider 09:15 as standard start (adjust to your rule)
        $standardStartTime = Carbon::createFromTime(9, 15, 0);

        foreach ($attendances as $attendance) {
            if ($attendance->time_in && $attendance->time_out) {
                try {
                    // parse flexibly; supports 'HH:mm' and 'HH:mm:ss'
                    $in = Carbon::parse($attendance->time_in);
                    $out = Carbon::parse($attendance->time_out);

                    // only count if out > in
                    if ($out->greaterThan($in)) {
                        $workedMinutes = $in->diffInMinutes($out);
                        $totalMinutes += $workedMinutes;

                        if ($workedMinutes > $basicMinutes) {
                            $overtime += ($workedMinutes - $basicMinutes);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::error("Error parsing time for employee {$employee->id}: {$e->getMessage()}");
                }
            }
        }

        // Working days excluding weekends
        $workingDays = 0;
        $period = Carbon::parse($startDate)->daysUntil($endDate->copy()->addDay());
        foreach ($period as $date) {
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        // Guard against zero working days
        $hourlyRate = ($employee->salary > 0 && $workingDays > 0)
            ? round($employee->salary / ($workingDays * 8), 2)
            : 0;

        $totalSalary = round(($totalMinutes / 60) * $hourlyRate, 2);

        // === Leaves & absents ===
        // Count Approved leaves in the range (to exclude from absents)
        $totalLeaves = $employee->leaves()
            ->where('status', 'Approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->count();

        // Absents = workingDays - attendancesCount - approvedLeaves
        $totalAbsents = max(0, $workingDays - $attendances->count() - $totalLeaves);

        // Lates: time_in after standard start
        $totalLates = $attendances->filter(function ($attendance) use ($standardStartTime) {
            if (!$attendance->time_in) return false;
            try {
                return Carbon::parse($attendance->time_in)->greaterThan($standardStartTime);
            } catch (\Throwable $e) {
                return false;
            }
        })->count();

        // Count unpaid approved leaves (these should deduct full-day)
        $unpaidLeavesCount = $employee->leaves()
            ->where('status', 'Approved')
            ->where('leave_type', 'Unpaid')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->count();

        // Count rejected leaves (treated as unpaid / deduct full day)
        $rejectedLeavesCount = $employee->leaves()
            ->where('status', 'Rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('from_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('to_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->count();

        // Monetary deductions
        $absentDeduction = $totalAbsents * $hourlyRate * 8;
        $unpaidLeavesAmount = $unpaidLeavesCount * $hourlyRate * 8;
        $rejectedLeavesAmount = $rejectedLeavesCount * $hourlyRate * 8;

        // Late deduction: you assumed half-day penalty per late; keep that rule
        $lateDeduction = $totalLates * ($hourlyRate / 2);

        // Total deduction
        $totalDeductionAmount = $absentDeduction + $unpaidLeavesAmount + $rejectedLeavesAmount + $lateDeduction;

        // Persist payroll for the employee+month
        Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month' => $monthDate, // DATE: 'YYYY-MM-01'
            ],
            [
                // keep the period stored/updated for reference
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),

                'total_minutes' => $totalMinutes,
                'overtime' => $overtime,
                'hourly_rate' => $hourlyRate,
                'total_salary' => $totalSalary,
                'total_lates' => $totalLates,
                'total_leaves' => $totalLeaves,
                'total_absents' => $totalAbsents,
                'total_deduction_amount' => round($totalDeductionAmount, 2),
            ]
        );
    }

    // Return only the month’s payrolls
    $payrolls = Payroll::with('employee')
        ->where('month', $monthDate)
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Payroll generated successfully.',
        'data' => $payrolls,
    ]);
}

}
