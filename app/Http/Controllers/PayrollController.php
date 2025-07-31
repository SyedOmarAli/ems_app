<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

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
            'month' => 'required|date_format:Y-m',
        ]);

        $monthDate = Carbon::parse($request->month . '-01');
        $startDate = $monthDate->copy()->startOfMonth();
        $endDate = $monthDate->copy()->endOfMonth();

        $employees = Employee::all();

        foreach ($employees as $employee) {
            // Get attendances of employee for the selected month
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            // Total working minutes
            $totalMinutes = 0;

            foreach ($attendances as $attendance) {
                if ($attendance->time_in && $attendance->time_out) {
                    try {
                        $in = Carbon::createFromFormat('H:i:s', $attendance->time_in);
                        $out = Carbon::createFromFormat('H:i:s', $attendance->time_out);
                        if ($out->greaterThan($in)) {
                            $totalMinutes += $in->diffInMinutes($out);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error parsing time for employee ID {$employee->id}: " . $e->getMessage());
                    }
                }
            }

            // Calculate working days in month (excluding weekends)
            $workingDays = 0;
            $period = Carbon::parse($startDate)->daysUntil($endDate->copy()->addDay());
            foreach ($period as $date) {
                if (!$date->isWeekend()) {
                    $workingDays++;
                }
            }

            // Calculate hourly rate dynamically
            $hourlyRate = $employee->salary > 0
                ? round($employee->salary / ($workingDays * 8), 2)  // assuming 8 hours/day
                : 0;

            // Calculate total salary
            $totalSalary = round(($totalMinutes / 60) * $hourlyRate, 2);

            // Save payroll
            Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $monthDate->format('Y-m-01'),
                ],
                [
                    'total_minutes' => $totalMinutes,
                    'hourly_rate' => $hourlyRate,
                    'total_salary' => $totalSalary,
                ]
            );
        }

        // Return generated payrolls to Vue
        $payrolls = Payroll::with('employee')
            ->where('month', $monthDate->format('Y-m-01'))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated successfully.',
            'data' => $payrolls,
        ]);
    }
}
