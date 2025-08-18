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
    public function index(Request $request)
    {
        try {
            $query = Employee::query();

            if ($request->has('search') && $request->input('search') !== '') {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('department', 'like', '%' . $search . '%');
                });
            }

            $employees = $query->paginate(5)->withQueryString();

            return Inertia::render('Employee', [
                'employees' => $employees,
                'search' => $request->input('search', ''),
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
        $validatedData = $this->validateEmployee($request, null);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make('pass123'),
            ]);

            $user->assignRole('employee');

            $validatedData['user_id'] = $user->id;
            Employee::create($validatedData);



            return redirect()->route('admin.employee.index')
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
        $validatedData = $this->validateEmployee($request, $employee);

        try {
            $employee->update($validatedData);

            
            if ($employee->user) {
                $employee->user->update([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                ]);
            }


            return redirect()->route('admin.employee.index')
                ->with('message', 'Employee updated successfully.');
        } catch (\Throwable $e) {
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
            return redirect()->route('admin.employee.index')
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
    private function validateEmployee(Request $request, ?Employee $employee = null)
    {
        $employeeId = optional($employee)->id;
        $userId = optional($employee)->user_id;

        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employeeId . '|unique:users,email,' . $userId,
            'phone' => 'required|string|max:20',
            'salary' => 'required|numeric|max:100',
            'hire_date' => 'required|date',
            'code' => $employee ? 'sometimes' : 'required|numeric|max:5000',
            'department' => 'required|string|max:100',
        ]);
    }
}
