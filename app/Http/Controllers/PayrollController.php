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

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $employees = Employee::all();

        foreach ($employees as $employee) {
            // Get attendances of employee for the selected month
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            // Total working minutes
            $totalMinutes = 0;
            $basicMinutes = 480;
            // $interval = CarbonInterval::minutes($basicMinutes);
            $overtime = 0;
            $standardEndTime = Carbon::createFromTime(17, 0, 0);

            foreach ($attendances as $attendance) {
                if ($attendance->time_in && $attendance->time_out) {
                    try {
                        $in = Carbon::createFromFormat('H:i:s', $attendance->time_in);
                        $out = Carbon::createFromFormat('H:i:s', $attendance->time_out);
                        if ($out->greaterThan($in)) {
                            $workedMinutes = $in->diffInMinutes($out);
                            $totalMinutes += $workedMinutes;
                            if ($workedMinutes > $basicMinutes) {
                                $overtime += ($workedMinutes - $basicMinutes);
                            }
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
                ? round($employee->salary / ($workingDays * 8), 2)
                : 0;

            // Calculate total salary
            $totalSalary = round(($totalMinutes / 60) * $hourlyRate, 2);

            // Save payroll
            Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                [
                    'total_minutes' => $totalMinutes,
                    'overtime' => $overtime,
                    'hourly_rate' => $hourlyRate,
                    'total_salary' => $totalSalary,
                    'total_lates' => 0,
                    'total_leaves' => 0,
                    'total_absents' => 0,
                    'total_deduction_amount' => 0,
                ]
            );
        }

        // Return generated payrolls to Vue
        $payrolls = Payroll::with('employee')
            ->where('start_date', $startDate->format('Y-m-d'))
            ->where('end_date', $endDate->format('Y-m-d'))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated successfully.',
            'data' => $payrolls,
        ]);
    }
}
