<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::with(['role', 'department'])->get(); // Including role and department relationships
        return response()->json($users);
    }

    // Get a single user by ID
    public function show($id)
    {
        $user = User::with(['role', 'department'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // Create a new user
    public function store(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Use Hash::make() for security
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
        ]);

        return response()->json($user, 201);
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate input data
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user details
        $user->update([
            'first_name' => $request->first_name ?: $user->first_name,
            'middle_name' => $request->middle_name ?: $user->middle_name,
            'last_name' => $request->last_name ?: $user->last_name,
            'email' => $request->email ?: $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password, // Use Hash::make() here as well
            'role_id' => $request->role_id ?: $user->role_id,
            'department_id' => $request->department_id ?: $user->department_id,
        ]);

        return response()->json($user);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
    // In UserController
    public function changePassword(Request $request, $id)
    {
        // Get the authenticated user (assuming you're using Laravel's built-in authentication)
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate input data
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|different:current_password',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->new_password); // Use Hash::make() to securely store the password
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

}
