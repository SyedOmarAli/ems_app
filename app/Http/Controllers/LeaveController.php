<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leaves;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leaves::with('employee')->latest()->get();
        return Inertia::render('AdminLeave', ['leaves' => $leaves]);
    }

    public function myLeaves()
    {
        $employee = \App\Models\Employee::where('user_id', auth()->id())->first();
        $leaves = $employee ? Leaves::where('employee_id', $employee->id)->latest()->get() : [];
        $employeeName = $employee ? $employee->name : auth()->user()->name;
        return Inertia::render('EmployeeLeave', [
            'leaves' => $leaves,
            'stats' => [
                'employee_name' => $employeeName
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        $employee = \App\Models\Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            return back()->withErrors(['error' => 'Employee record not found for this user.']);
        }
        Leaves::create([
            'employee_id' => $employee->id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return back()->with('message', 'Leave applied successfully.');
    }

    public function approve($leaveId)
    {
        $leave = Leaves::findOrFail($leaveId);
        $leave->status = 'Approved';
        $leave->save();

        return back()->with('message', 'Leave approved.');
    }

    public function reject($leaveId)
    {
        $leave = Leaves::findOrFail($leaveId);
        $leave->status = 'Rejected';
        $leave->save();

        return back()->with('message', 'Leave rejected.');
    }
    public function revert($leaveId)
    {
        $leave = Leaves::findOrFail($leaveId);
        $leave->status = 'Pending';
        $leave->save();

        return back()->with('message', 'Leave reverted.');
    }
}
