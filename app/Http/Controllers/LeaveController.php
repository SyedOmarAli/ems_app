<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leaves;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index() {
        $leaves = Leaves::with('employee')->latest()->get();
        return Inertia::render('AdminLeave',['leaves' => $leaves]);
    }

    public function myLeaves() {
        $leaves = Leaves::where('employee_id', auth()->user()->employee_id)->latest()->get();
        $employeeName = auth()->user()->name;
        return Inertia::render('EmployeeLeave', [
            'leaves' => $leaves,
            'stats' => [
                'employee_name' => $employeeName
            ]
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'is_paid' => 'required|boolean',
        ]);

        Leaves::create([
            'employee_id' => auth()->user()->employee_id,
            'date' => $request->date,
            'type' => $request->type,
            'description' => $request->description,
            'is_paid' => $request->is_paid,
            'status' => 'Pending',
        ]);

        return back()->with('message', 'Leave applied successfully.');
    }

    public function approve(Leaves $leaves) {
        $leaves->update(['status' => 'Approved']);
        return back()->with('message', 'Leave approved.');
    }

    public function reject(Leaves $leaves) {
        $leaves->update(['status' => 'Rejected']);
        return back()->with('message', 'Leave rejected.');
    }
}
