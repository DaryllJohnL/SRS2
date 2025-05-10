<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // Display all departments
    public function index()
    {
        $departments = Department::all();
        return response()->json($departments);
    }

    // Show a specific department by ID
    public function show($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }

    // Create a new department
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = new Department();
        $department->name = $request->name;
        $department->save();

        return response()->json($department, 201);  // Return the created department
    }

    // Update a specific department by ID
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department = Department::findOrFail($id);
        $department->name = $request->name;
        $department->save();

        return response()->json($department);  // Return the updated department
    }

    // Delete a department by ID
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
