<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with(['transactionHeader', 'bank', 'referenceType'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_header_id' => 'required|exists:transaction_headers,id',
            'transaction_amount' => 'required|numeric',
            'bank_code_id' => 'required|exists:banks,id',
            'transaction_reference_no' => 'required|string',
            'supplier_ref_no' => 'nullable|string',
            'supplier_reference_date' => 'nullable|date',
            'transaction_reference_date' => 'nullable|date',
            'reference_type_id' => 'required|exists:reference_types,id',
        ]);

        $transaction = Transaction::create($validated);

        return response()->json(['message' => 'Transaction created successfully', 'data' => $transaction], 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['transactionHeader', 'bank', 'referenceType'])->findOrFail($id);
        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'transaction_amount' => 'sometimes|numeric',
            'bank_code_id' => 'sometimes|exists:banks,id',
            'transaction_reference_no' => 'sometimes|string',
            'supplier_ref_no' => 'nullable|string',
            'supplier_reference_date' => 'nullable|date',
            'transaction_reference_date' => 'nullable|date',
            'reference_type_id' => 'sometimes|exists:reference_types,id',
        ]);

        $transaction->update($validated);

        return response()->json(['message' => 'Transaction updated successfully', 'data' => $transaction]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
    // TransactionController.php
    public function byHeader($headerId)
    {
        // Fetch transactions by the transaction_header_id
        $transactions = Transaction::with(['transactionHeader', 'bank', 'referenceType'])
            ->where('transaction_header_id', $headerId)
            ->get();

        return response()->json(['data' => $transactions]);
    }

}
