<?php

namespace App\Http\Controllers;

use App\Models\ReferenceType;
use Illuminate\Http\Request;

class ReferenceTypeController extends Controller
{
    // Create a new Reference Type
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:reference_types',
            'description' => 'nullable|string',
            'data_type' => 'required|string|in:+,-',  // Ensure it's either '+' or '-'
        ]);

        $referenceType = ReferenceType::create([
            'name' => $request->name,
            'description' => $request->description,
            'data_type' => $request->data_type,  // Add the new field
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reference Type created successfully!',
            'data' => $referenceType
        ], 201);
    }

    // Get all Reference Types (with optional filtering)
    public function index(Request $request)
    {
        $search = $request->query('search');

        $referenceTypes = ReferenceType::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $referenceTypes
        ]);
    }

    // Update a Reference Type
    public function update(Request $request, $id)
    {
        $referenceType = ReferenceType::find($id);
        if (!$referenceType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reference Type not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:reference_types,name,' . $id,
            'description' => 'nullable|string',
            'data_type' => 'required|string|in:+,-',  // Ensure it's either '+' or '-'
        ]);

        $referenceType->update([
            'name' => $request->name,
            'description' => $request->description,
            'data_type' => $request->data_type,  // Update the new field
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reference Type updated successfully!',
            'data' => $referenceType
        ]);
    }

    // Delete a Reference Type
    public function destroy($id)
    {
        $referenceType = ReferenceType::find($id);

        if (!$referenceType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reference Type not found'
            ], 404);
        }

        $referenceType->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Reference Type deleted successfully!'
        ]);
    }
}
