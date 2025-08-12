<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Attendance;
use App\Models\Employee;
use League\Csv\Reader;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::with('employee')
            ->whereDate('date', today())
            ->get();

        $employees = Employee::all();

        return Inertia::render('Attendance', [
            'attendance' => $attendance,
            'employees' => $employees,
            'date' => today()->toDateString(),
            'routes' => [
                'updateAttendance' => route('attendance.update_status'),
            ],
        ]);
    }

    public function updateStatus(Request $request)
    {
        // Accept both single record and multiple records
        $records = $request->input('records', []);

        // If a single record is sent instead of an array
        $records = [
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'time_in' => $request->time_in,
                'time_out' => $request->time_out,
            ]
        ];
        // Loop through each record and update or create
        foreach ($records as $record) {
            Attendance::updateOrCreate(
                ['employee_id' => $record['employee_id'], 'date' => $record['date']],
                [
                    'status' => $record['status'],
                    'time_in' => !empty($record['time_in']) ? $record['time_in'] . (strlen($record['time_in']) === 5 ? ':00' : '') : null,
                    'time_out' => !empty($record['time_out']) ? $record['time_out'] . (strlen($record['time_out']) === 5 ? ':00' : '') : null,
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Attendance updated successfully.']);
    }

    public function show()
    {
        // Get all attendance records with employee info
        $attendances = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->get();

        return Inertia::render('AttendanceShow', [
            'attendances' => $attendances,
            'routes' => [
                'attendanceShow' => route('attendance.show'),
            ]
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $imported = 0;
        $skipped = 0;
        $processedDates = []; // keep track of dates processed from CSV


        foreach ($csv as $record) {
            if (empty($record['employee_id']) || empty($record['time_in'])) {
                $skipped++;
                continue;
            }

            $employeeId = trim($record['employee_id']);
            if (!Employee::where('id', $employeeId)->exists()) {
                $skipped++;
                continue;
            }

            // Normalize time_in: insert a space before AM/PM if missing
            $timeInRaw = trim($record['time_in']);
            $timeInNormalized = preg_replace('/(AM|PM)$/i', ' $1', $timeInRaw);

            // Try both with and without AM/PM
            $dateTime = null;
            $formats = ['d/m/Y h:i:s A', 'd/m/Y H:i:s'];
            foreach ($formats as $format) {
                try {
                    $dateTime = Carbon::createFromFormat($format, $timeInNormalized);
                    break;
                } catch (\Exception $e) {
                    // Try next format
                }
            }
            if (!$dateTime) {
                $skipped++;
                continue;
            }

            // Skip weekends
            if ($dateTime->isWeekend()) {
                $skipped++;
                continue;
            }

            $date = $dateTime->toDateString();
            $processedDates[$date] = true; // mark this date as processed

            $noon = $dateTime->copy()->setTime(12, 0, 0);
            $lateThreshold = $dateTime->copy()->setTime(9, 15, 0);

            // Find existing attendance for the employee on the same date
            $attendance = Attendance::where('employee_id', $employeeId)
                ->where('date', $date)
                ->first();

            if ($attendance) {
                // Second scan → update time_out if it's later than current time_out
                if (is_null($attendance->time_out) || $dateTime->greaterThan($attendance->time_out)) {
                    $attendance->time_out = $dateTime;
                    $attendance->save();
                }
            } else {
                // First scan → determine time_in, status, and possibly time_out
                $timeIn = null;
                $timeOut = null;
                $status = 'Absent';

                if ($dateTime->lessThan($noon)) {
                    $timeIn = $dateTime;

                    if ($dateTime->greaterThan($lateThreshold)) {
                        $status = 'Late';
                    } else {
                        $status = 'Present';
                    }
                } else {
                    $timeOut = $dateTime;
                    $status = 'Absent';
                }

                Attendance::create([
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'status' => $status,
                ]);
            }

            $imported++;
        }

        // After processing all CSV records → create absent entries for employees who have no record for the day
        foreach (array_keys($processedDates) as $date) {
            if (Carbon::parse($date)->isWeekend())
                continue;

            $allEmployees = Employee::pluck('id')->toArray();
            $attendedEmployees = Attendance::where('date', $date)->pluck('employee_id')->toArray();

            $absentEmployees = array_diff($allEmployees, $attendedEmployees);

            foreach ($absentEmployees as $absentId) {
                Attendance::create([
                    'employee_id' => $absentId,
                    'date' => $date,
                    'time_in' => null,
                    'time_out' => null,
                    'status' => 'Absent',
                ]);
            }
        }

        return redirect()->route('attendance.show')
            ->with('success', "Attendance uploaded successfully! Imported: {$imported}, Skipped: {$skipped}");
    }
}
