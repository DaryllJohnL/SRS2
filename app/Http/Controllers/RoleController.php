<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Get all roles
    public function index()
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->role_name, // alias for frontend consistency
            ];
        });

        return response()->json($roles);
    }


    // Get a single role by ID
    public function show($id)
    {
        $role = Role::find($id);
        if ($role) {
            return response()->json($role);
        } else {
            return response()->json(['message' => 'Role not found'], 404);
        }
    }

    // Create a new role
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = Role::create([
            'role_name' => $request->role_name,
        ]);

        return response()->json($role, 201);  // 201 is the status code for resource creation
    }

    // Update an existing role
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = Role::find($id);

        if ($role) {
            $role->role_name = $request->role_name;
            $role->save();
            return response()->json($role);
        } else {
            return response()->json(['message' => 'Role not found'], 404);
        }
    }

    // Delete a role
    public function destroy($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully']);
        } else {
            return response()->json(['message' => 'Role not found'], 404);
        }
    }
}
