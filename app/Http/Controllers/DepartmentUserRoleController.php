<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentUserRoleController extends Controller
{
    // Assign role to user in a department
    public function assignRoleToUser($departmentId, $userId, Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',  // Ensure the role_id exists
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Find the department and user
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->role_id);

        // Attach the user to the department with the specified role
        $department->users()->attach($user->id, ['role_id' => $role->id]);

        return response()->json([
            'message' => 'User role assigned successfully',
            'data' => [
                'department' => $department,
                'user' => $user,
                'role' => $role,
            ]
        ], 200);
    }

    // Get all users in a department with their roles
    public function getUsersInDepartment($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        // Get users with their pivot role_id and fetch the role_name manually
        $users = $department->users()->withPivot('role_id')->get()->map(function ($user) {
            $role = \App\Models\Role::find($user->pivot->role_id);  // Get role from pivot
            $user->role_name = $role ? $role->role_name : null;
            return $user;
        });

        return response()->json([
            'department' => $department,
            'users' => $users
        ], 200);
    }

}
