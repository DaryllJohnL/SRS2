<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    // Create a new Bank
    public function store(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string|unique:banks',
            'bank_name' => 'required|string',
            'bank_address' => 'nullable|string',
            'contact_number' => 'nullable|string',
        ]);

        $bank = Bank::create([
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'bank_address' => $request->bank_address,
            'contact_number' => $request->contact_number,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bank created successfully!',
            'data' => $bank
        ], 201);
    }

    // Get all Banks (with optional filtering)
    public function index(Request $request)
    {
        $search = $request->query('search');

        $banks = Bank::when($search, function ($query) use ($search) {
            return $query->where('bank_name', 'like', "%$search%")
                ->orWhere('bank_code', 'like', "%$search%");
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $banks
        ]);
    }

    // Update a Bank
    public function update(Request $request, $id)
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bank not found'
            ], 404);
        }

        $request->validate([
            'bank_code' => 'required|string|unique:banks,bank_code,' . $id,
            'bank_name' => 'required|string',
            'bank_address' => 'nullable|string',
            'contact_number' => 'nullable|string',
        ]);

        $bank->update([
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'bank_address' => $request->bank_address,
            'contact_number' => $request->contact_number,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bank updated successfully!',
            'data' => $bank
        ]);
    }

    public function destroy($id)
    {
        $bank = Bank::find($id);

        if (!$bank) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bank not found'
            ], 404);
        }

        $bank->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bank deleted successfully!'
        ]);
    }

}
