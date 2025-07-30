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
    public function index()
    {
        $payrolls = Payroll::latest()->with('employee')->get(); 
        return Inertia::render('Payroll', [
            'payrolls' => $payrolls,
        ]);
    }

    public function generate(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $employees = Employee::all();
        $payrolls = [];

        // Get list of working days in the month (excluding weekends)
        $workingDays = $this->getWorkingDaysInMonth($startDate, $endDate);

        foreach ($employees as $employee) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            if ($attendances->isEmpty()) {
                continue; 
            }

            // Count number of distinct days present
            $presentDays = $attendances->pluck('date')->unique()->count();

            // Calculate dynamic hourly rate
            $monthlySalary = $employee->salary ?? 0;
            $dailySalary = $workingDays > 0 ? $monthlySalary / $workingDays : 0;
            $hourlyRate = $dailySalary / 8; // assuming 8 hours = 1 full workday

            $totalMinutes = $this->calculateTotalMinutes($attendances);
            $totalSalary = round($totalMinutes * ($hourlyRate / 60), 2); // salary = worked_minutes × rate_per_minute

            $payroll = Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $startDate->format('Y-m-01'),
                ],
                [
                    'total_minutes' => $totalMinutes,
                    'hourly_rate' => round($hourlyRate, 2),
                    'total_salary' => $totalSalary,
                ]
            );

            $payrolls[] = $payroll;
        }

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated successfully.',
            'data' => $payrolls,
        ]);
    }

    private function calculateTotalMinutes($attendances): int
    {
        $totalMinutes = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->time_in && $attendance->time_out) {
                try {
                    $in = Carbon::parse($attendance->date . ' ' . $attendance->time_in);
                    $out = Carbon::parse($attendance->date . ' ' . $attendance->time_out);

                    if ($out->greaterThan($in)) {
                        $totalMinutes += $in->diffInMinutes($out);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing attendance time: " . $e->getMessage());
                }
            }
        }

        return $totalMinutes;
    }

    private function getWorkingDaysInMonth($startDate, $endDate): int
    {
        $days = 0;
        while ($startDate->lte($endDate)) {
            if (!$startDate->isWeekend()) {
                $days++;
            }
            $startDate->addDay();
        }
        return $days;
    }
}
