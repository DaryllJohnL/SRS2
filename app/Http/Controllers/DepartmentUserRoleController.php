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
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->role_id);

        // Attach user with role
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

    // Update user's role in a department
    public function updateRoleOfUser($departmentId, $userId, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($request->role_id);

        // Update the pivot table
        $department->users()->updateExistingPivot($user->id, ['role_id' => $role->id]);

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => [
                'department' => $department,
                'user' => $user,
                'role' => $role,
            ]
        ], 200);
    }

    // Remove user from a department
    public function removeUserFromDepartment($departmentId, $userId)
    {
        $department = Department::findOrFail($departmentId);
        $user = User::findOrFail($userId);

        // Detach user from department
        $department->users()->detach($user->id);

        return response()->json([
            'message' => 'User removed from department successfully',
        ], 200);
    }

    // Get all users in a department with their roles
    public function getUsersInDepartment($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        $users = $department->users()->withPivot('role_id')->get()->map(function ($user) {
            $role = Role::find($user->pivot->role_id);
            $user->role_name = $role ? $role->role_name : null;
            return $user;
        });

        return response()->json([
            'department' => $department,
            'users' => $users
        ], 200);
    }
}
