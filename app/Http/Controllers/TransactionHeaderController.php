<?php
namespace App\Http\Controllers;

use App\Models\TransactionHeader;
use App\Models\Supplier;
use App\Models\RebateType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TransactionHeaderController extends Controller
{
    // Method to display all transaction headers with related data
    public function index()
    {
        $user = Auth::user(); // Get authenticated user

        $transactionHeaders = TransactionHeader::with(['supplier', 'rebateType', 'preparedBy', 'approvedBy', 'reviewBy', 'department'])
            ->where('department_id', $user->department_id) // Filter by user's department
            ->get();

        // Transform the data to include names instead of IDs
        $transactionHeaders = $transactionHeaders->map(function ($header) {
            return [
                'id' => $header->id,
                'supplier_name' => $header->supplier->supplier_name,
                'rebate_type_name' => $header->rebateType->rebate_name,
                'particulars' => $header->particulars,
                'incentive' => $header->incentive,
                'conversion' => $header->conversion,
                'prepared_by' => $header->preparedBy->full_name,
                'approved_by' => $header->approved_by ? $header->approvedBy->full_name : null,
                'review_by' => $header->review_by ? $header->reviewBy->full_name : null,
                'approve_date' => $header->approve_date,
                'status' => $header->status,
                'created_at' => $header->created_at,
                'updated_at' => $header->updated_at,
                'department_name' => $header->department->name ?? null,
            ];
        });

        return response()->json($transactionHeaders);
    }

    // Method to store a new transaction header
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'supplier_code' => 'required|exists:suppliers,id',  // Supplier code must exist in the suppliers table
                'rebate_type' => 'required|exists:rebate_types,id',  // Rebate type must exist in the rebate_types table
                'particulars' => 'required|string',  // Particulars (description) is required and must be a string
                'incentive' => 'required|string',  // Incentive is optional and must be a number if provided
                'conversion' => 'required|string',  // Conversion is optional and must be a number if provided
                'prepared_by' => 'required|exists:users,id',  // Prepared by must be an existing user
                'approved_by' => 'nullable|exists:users,id',  // Approved by is optional but must be a valid user if provided
                'review_by' => 'nullable|exists:users,id',  // Review by is optional but must be a valid user if provided
                'approve_date' => 'nullable|date',  // Approval date is optional but must be a valid date if provided
                'status' => 'required|string',
                'department_id' => 'required|exists:departments,id',  // Status is required and must be a string
            ]);

            $transactionHeader = TransactionHeader::create($validated);

            // Load related data for the response
            $transactionHeader->load('supplier', 'rebateType', 'preparedBy');

            return response()->json([
                'id' => $transactionHeader->id,
                'supplier_name' => $transactionHeader->supplier->supplier_name,  // Add supplier name
                'rebate_type_name' => $transactionHeader->rebateType->name,  // Add rebate type name
                'particulars' => $transactionHeader->particulars,
                'incentive' => $transactionHeader->incentive,
                'conversion' => $transactionHeader->conversion,
                'prepared_by' => $transactionHeader->preparedBy->full_name,  // Add prepared_by user's full name
                'approved_by' => $transactionHeader->approved_by ? $transactionHeader->approvedBy->full_name : null,  // Add approved_by user's full name
                'review_by' => $transactionHeader->review_by ? $transactionHeader->reviewBy->full_name : null,  // Add review_by user's full name
                'approve_date' => $transactionHeader->approve_date,
                'status' => $transactionHeader->status,
                'created_at' => $transactionHeader->created_at,
                'updated_at' => $transactionHeader->updated_at,
                'department_name' => $header->department->name ?? null,
            ], 201); // 201 Created response
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the transaction header',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Method to show a specific transaction header by ID
    public function show($id)
    {
        $transactionHeader = TransactionHeader::with(['supplier', 'rebateType', 'preparedBy'])->findOrFail($id);

        // Transform the data to include names instead of IDs
        return response()->json([
            'id' => $transactionHeader->id,
            'supplier_name' => $transactionHeader->supplier->supplier_name,
            'rebate_type_name' => $transactionHeader->rebateType->rebate_name,
            'particulars' => $transactionHeader->particulars,
            'incentive' => $transactionHeader->incentive,
            'conversion' => $transactionHeader->conversion,
            'prepared_by' => $transactionHeader->preparedBy->full_name,
            'approved_by' => $transactionHeader->approved_by ? $transactionHeader->approvedBy->full_name : null,
            'review_by' => $transactionHeader->review_by ? $transactionHeader->reviewBy->full_name : null,
            'approve_date' => $transactionHeader->approve_date,
            'status' => $transactionHeader->status,
            'created_at' => $transactionHeader->created_at,
            'updated_at' => $transactionHeader->updated_at,
            'department_name' => $header->department->name ?? null,
        ]);
    }

    // Method to update a transaction header
    public function update(Request $request, $id)
    {
        $transactionHeader = TransactionHeader::findOrFail($id);

        $validatedData = $request->validate([
            'supplier_code' => 'required|exists:suppliers,id',
            'rebate_type' => 'required|exists:rebate_types,id',
            'particulars' => 'required|string|max:255',
            'incentive' => 'nullable|numeric',
            'conversion' => 'nullable|string',
            'review_by' => 'nullable|exists:users,id',
            'approve_date' => 'nullable|date',
            'status' => 'required|string|max:50',
        ]);

        $transactionHeader->update($validatedData);

        // Load related data for the response
        $transactionHeader->load('supplier', 'rebateType', 'preparedBy');

        return response()->json([
            'id' => $transactionHeader->id,
            'supplier_name' => $transactionHeader->supplier->supplier_name,
            'rebate_type_name' => $transactionHeader->rebateType->name,
            'particulars' => $transactionHeader->particulars,
            'incentive' => $transactionHeader->incentive,
            'conversion' => $transactionHeader->conversion,
            'prepared_by' => $transactionHeader->preparedBy->full_name,
            'approved_by' => $transactionHeader->approved_by ? $transactionHeader->approvedBy->full_name : null,
            'review_by' => $transactionHeader->review_by ? $transactionHeader->reviewBy->full_name : null,
            'approve_date' => $transactionHeader->approve_date,
            'status' => $transactionHeader->status,
            'created_at' => $transactionHeader->created_at,
            'updated_at' => $transactionHeader->updated_at,
            'department_name' => $header->department->name ?? null,
        ]);
    }

    // Method to delete a transaction header
    public function destroy($id)
    {
        $transactionHeader = TransactionHeader::findOrFail($id);
        $transactionHeader->delete();

        return response()->json(['message' => 'Transaction Header deleted successfully']);
    }
    public function updateStatusAndApprover(Request $request, $id)
    {
        $transactionHeader = TransactionHeader::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:For Approval,For Review,Approved,Rejected,Cancelled',
            'approved_by' => 'nullable|exists:users,id',
            'review_by' => 'nullable|exists:users,id',
        ]);

        // Manual validation based on status
        if ($validated['status'] === 'For Approval' && empty($validated['approved_by'])) {
            return response()->json(['message' => 'approved_by is required for For Approval status.'], 422);
        }

        if ($validated['status'] === 'For Review' && empty($validated['review_by'])) {
            return response()->json(['message' => 'review_by is required for For Review status.'], 422);
        }

        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
            'approve_date' => now(),
        ];

        // Conditionally assign approvers
        if ($validated['status'] === 'For Approval') {
            $updateData['approved_by'] = $validated['approved_by'];
        } elseif ($validated['status'] === 'For Review') {
            $updateData['review_by'] = $validated['review_by'];
        }

        $transactionHeader->update($updateData);

        return response()->json([
            'message' => 'Status and approver updated successfully.',
            'data' => $transactionHeader,
        ]);
    }



}
