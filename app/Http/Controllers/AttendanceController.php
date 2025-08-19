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
    public function index(Request $request)
    {
        try {
            $query = Attendance::with('employee');

            if ($request->filled('date')) {
                $query->whereDate('date', $request->input('date'));
            }

            // paginate and then convert to array for predictable shape
            $attendances = $query->orderBy('date', 'desc')->paginate(5)->withQueryString();

            return Inertia::render('AttendanceShow', [
                // explicitly serialize to array (ensures 'links' exists when >1 page)
                'attendances' => $attendances->toArray(),
                'date' => $request->input('date', ''),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch attendance: ' . $e->getMessage());
            return back()->withErrors('Unable to load attendance at the moment.');
        }
    }


    public function updateStatus(Request $request)
    {
        $input = $request->input();

        // Accept either: payload.records = [ ... ] OR single-record fields directly
        $records = $request->input('records', null);

        if (is_null($records)) {
            // maybe single record posted not wrapped in records array
            $single = [
                'employee_id' => $request->input('employee_id'),
                'date' => $request->input('date'),
                'status' => $request->input('status'),
                'time_in' => $request->input('time_in'),
                'time_out' => $request->input('time_out'),
            ];
            // if employee_id missing, abort
            if (empty($single['employee_id']) || empty($single['date'])) {
                return response()->json(['success' => false, 'message' => 'Invalid payload.'], 422);
            }
            $records = [$single];
        }

        foreach ($records as $record) {
            // Validate required keys per record
            if (empty($record['employee_id']) || empty($record['date'])) {
                continue; // skip invalid
            }

            // Normalize time strings: we expect HH:mm:ss or HH:mm (convert to HH:mm:00)
            $timeIn = $record['time_in'] ?? null;
            $timeOut = $record['time_out'] ?? null;

            if (!empty($timeIn) && strlen($timeIn) === 5) {
                $timeIn = $timeIn . ':00';
            }
            if (!empty($timeOut) && strlen($timeOut) === 5) {
                $timeOut = $timeOut . ':00';
            }

            Attendance::updateOrCreate(
                ['employee_id' => $record['employee_id'], 'date' => $record['date']],
                [
                    'status' => $record['status'] ?? 'Absent',
                    'time_in' => !empty($timeIn) ? $timeIn : null,
                    'time_out' => !empty($timeOut) ? $timeOut : null,
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
                    $attendance->time_out = $dateTime->format('H:i:s');
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
                    'time_in' => $timeIn ? $timeIn->format('H:i:s') : null,
                    'time_out' => $timeOut ? $timeOut->format('H:i:s') : null,
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
