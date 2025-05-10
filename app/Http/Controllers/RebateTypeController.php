<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RebateType;
use Illuminate\Validation\ValidationException;

class RebateTypeController extends Controller
{
    // Get a list of rebate types with optional search filters
    public function index(Request $request)
    {
        // Validate input if needed
        $validated = $request->validate([
            'rebate_code' => 'nullable|string|max:255',
            'rebate_name' => 'nullable|string|max:255',
        ]);

        $query = RebateType::query();

        // Apply search filters if provided
        if ($request->filled('rebate_code')) {
            $query->where('rebate_code', 'LIKE', '%' . $request->rebate_code . '%');
        }

        if ($request->filled('rebate_name')) {
            $query->where('rebate_name', 'LIKE', '%' . $request->rebate_name . '%');
        }

        $rebateTypes = $query->paginate(10); // Paginate the results

        return response()->json([
            'status' => 'success',
            'data' => $rebateTypes->items(), // Only return the items for this page
            'pagination' => [
                'current_page' => $rebateTypes->currentPage(),
                'total_pages' => $rebateTypes->lastPage(),
                'total_items' => $rebateTypes->total(),
            ],
        ]);
    }

    // Show a single rebate type
    public function show($id)
    {
        $rebateType = RebateType::find($id);

        if (!$rebateType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rebate type not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $rebateType
        ]);
    }

    // Create a new rebate type
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rebate_code' => 'required|string|max:255|unique:rebate_types',
            'rebate_name' => 'required|string|max:255',
        ]);

        try {
            $rebateType = RebateType::create($validated);

            return response()->json([
                'status' => 'success',
                'data' => $rebateType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create rebate type.',
            ], 500);
        }
    }

    // Update an existing rebate type
    public function update(Request $request, $id)
    {
        $rebateType = RebateType::find($id);

        if (!$rebateType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rebate type not found.'
            ], 404);
        }

        $validated = $request->validate([
            'rebate_code' => 'required|string|max:255|unique:rebate_types,rebate_code,' . $id,
            'rebate_name' => 'required|string|max:255',
        ]);

        try {
            $rebateType->update($validated);

            return response()->json([
                'status' => 'success',
                'data' => $rebateType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update rebate type.',
            ], 500);
        }
    }

    // Delete a rebate type
    public function destroy($id)
    {
        $rebateType = RebateType::find($id);

        if (!$rebateType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rebate type not found.'
            ], 404);
        }

        try {
            $rebateType->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Rebate type deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete rebate type.',
            ], 500);
        }
    }
}
