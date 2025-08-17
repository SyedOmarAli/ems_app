<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leaves;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Employee;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        try {
            $employees = Employee::paginate(5);

            return Inertia::render('Employee', [
                'employees' => $employees,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch employees: ' . $e->getMessage());
            return back()->withErrors('Unable to load employees at the moment.');
        }

    }

    // public function index(request $request) {
    //     $query = Employee::query();
        
    //     if ($request->has('search')) {
    //         $search = $request->input('search');
    //         $query->where('name', 'like', '%' . $search . '%')
    //             ->orWhere('email', 'like', '%' . $search . '%');
    //     }   
    // }

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
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make('pass123'),
            ]);

            $user->assignRole('employee');

            $validatedData['user_id'] = $user->id;
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

            $user = User::where('email', $employee->email)->first();
            if ($employee->user) {
                $employee->user->update([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                ]);
            }


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
            if($employee->user){
                $employee->user->delete();
            }
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employeeId . '|unique:users,email,' . $employeeId,
            'phone' => 'required|string|max:20',
            'salary' => 'required|numeric|max:100',
            'hire_date' => 'required|date',
            'code' => $employeeId ? 'sometimes' : 'required|numeric|max:5000',
            'department' => 'required|string|max:100',
        ]);
    }
}
