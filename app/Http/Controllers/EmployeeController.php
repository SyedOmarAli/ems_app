<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        try {
            $employees = Employee::all();

            return Inertia::render('Employee', [
                'employees' => $employees,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch employees: ' . $e->getMessage());
            return back()->withErrors('Unable to load employees at the moment.');
        }
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return Inertia::render('CreateEmployee');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateEmployee($request);

        try {
            Employee::create($validatedData);
            return redirect()->route('employee.index')
                ->with('message', 'Employee registered successfully.');
        } catch (QueryException $e) {
            Log::error('Failed to create employee: ' . $e->getMessage());
            return back()->withErrors('Error creating employee. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('EditEmployee', [
            'employee' => $employee
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $this->validateEmployee($request, $employee->id);

        try {
            $employee->update($validatedData);
            return redirect()->route('employee.index')
                ->with('message', 'Employee updated successfully.');
        } catch (QueryException $e) {
            Log::error('Failed to update employee ID ' . $employee->id . ': ' . $e->getMessage());
            return back()->withErrors('Error updating employee. Please try again.');
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return Inertia::render('ShowEmployee', [
            'employee' => $employee
        ]);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('employee.index')
                ->with('message', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete employee ID ' . $employee->id . ': ' . $e->getMessage());
            return back()->withErrors('Error deleting employee. Please try again.');
        }
    }

    /**
     * Validate employee data.
     * Centralized validation for store and update.
     */
    private function validateEmployee(Request $request, $employeeId = null)
    {
        return $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:employees,email,' . $employeeId,
            'phone'      => 'required|string|max:20',
            'salary'     => 'required|numeric|max:100',
            'hire_date'  => 'required|date',
            'code'       => $employeeId ? 'sometimes' : 'required|numeric|max:5000',
            'department' => 'required|string|max:100',
        ]);
    }
}
