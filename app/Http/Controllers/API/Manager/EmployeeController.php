<?php

namespace App\Http\Controllers\API\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::where('role', 'employee')->get();

        return response()->json([
            "error" => false,
            "message" => "Employees fetched successfully",
            "listEmployee" => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors(),
            ]);
        }

        $input = $request->all();
        $input['role'] = "employee";
        $input['password'] = bcrypt($input['password']);

        $employee = User::create($input);

        return response()->json([
            "error" => false,
            "message" => "Employee created successfully",
            "employee" => $employee
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = User::find($id);

        if (!$employee || $employee->role !== 'employee') {
            return response()->json([
                "error" => true,
                'message' => 'Employee not found'
            ]);
        }

        return response()->json([
            "error" => false,
            "message" => "Employee fetched successfully",
            "employee" => $employee
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = User::find($id);

        if (!$employee || $employee->role !== 'employee') {
            return response()->json([
                "error" => true,
                'message' => 'Employee not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $employee->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $employee->id,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $employee->id,
            'password' => 'sometimes|required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors(),
            ]);
        }

        $input = $request->all();

        if (isset($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        } else {
            unset($input['password']);
        }

        $employee->update($input);

        return response()->json([
            "error" => false,
            "message" => "Employee updated successfully",
            "employee" => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $employee = User::find($id);

        if (!$employee || $employee->role !== 'employee') {
            return response()->json([
                "error" => true,
                'message' => 'Employee not found'
            ]);
        }

        $employee->delete();

        return response()->json([
            "error" => false,
            "message" => "Employee deleted successfully"
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors(),
            ]);
        }

        $query = $request->input('q');

        $employees = User::search($query);

        return response()->json([
            "error" => false,
            "message" => "Employees fetched successfully",
            "listEmployee" => $employees
        ]);
    }
}
