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

        foreach ($employees as $employee) {
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            if ($attendances->isEmpty()) {
                continue; 
            }

            $totalMinutes = $this->calculateTotalMinutes($attendances);
            $hourlyRate = $employee->hourly_rate; // hour
            $totalSalary = round($totalMinutes * ($hourlyRate / 60), 2);

            $payroll = Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $startDate->format('Y-m-01'),
                ],
                [
                    'total_minutes' => $totalMinutes,
                    'hourly_rate' => $hourlyRate,
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
                    $in = Carbon::parse($attendance->time_in);
                    $out = Carbon::parse($attendance->time_out);

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
}
