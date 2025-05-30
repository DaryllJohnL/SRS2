<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier; // Ensure you have a Supplier model
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search by name
        if ($request->has('supplier_code')) {
            $query->where('supplier_code', 'LIKE', '%' . $request->supplier_code . '%');
        }

        // Search by email
        if ($request->has('supplier_name')) {
            $query->where('supplier_name', 'LIKE', '%' . $request->supplier_name . '%');
        }

        // Fetch results
        $suppliers = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'supplier_code' => 'required|string|unique:suppliers,supplier_code',
            'supplier_name' => 'required|string',
            'supplier_address' => 'nullable|string',
            'supplier_phone_number' => 'nullable|string',
            'supplier_contact_person' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'supplier_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create new supplier
        $supplier = Supplier::create([
            'supplier_code' => $request->supplier_code,
            'supplier_name' => $request->supplier_name,
            'supplier_address' => $request->supplier_address,
            'supplier_phone_number' => $request->supplier_phone_number,
            'supplier_contact_person' => $request->supplier_contact_person,
            'status' => $request->status ?? 'active',
            'supplier_type' => $request->supplier_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier added successfully!',
            'data' => $supplier
        ], 201);
    }
    // ✅ 1. Update Supplier Details
    public function update(Request $request, $id)
    {
        // Find supplier by ID
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier not found'
            ], 404);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'supplier_code' => 'required|string|unique:suppliers,supplier_code,' . $id,
            'supplier_name' => 'required|string',
            'supplier_address' => 'nullable|string',
            'supplier_phone_number' => 'nullable|string',
            'supplier_contact_person' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update supplier
        $supplier->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier updated successfully!',
            'data' => $supplier
        ], 200);
    }

    // ✅ 2. Soft Delete Supplier (Update Status)
    public function updateStatus(Request $request, $id)
    {
        // Validate request status input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive' // Ensure only 'active' or 'inactive' is allowed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find supplier by ID
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier not found'
            ], 404);
        }

        // Update status based on user input
        $supplier->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => "Supplier status updated to {$request->status}!",
            'data' => $supplier
        ], 200);
    }
    // ✅ 3. Delete Supplier Permanently
    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => 'error',
                'message' => 'Supplier not found'
            ], 404);
        }

        try {
            $supplier->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete supplier',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        // Validate if a file is uploaded
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Adjust file type and size limit
        ]);

        $file = $request->file('file');

        // Store the file temporarily
        $path = $file->storeAs('uploads', 'suppliers.csv');

        // Read the CSV file
        try {
            $csv = Reader::createFromPath(Storage::path($path), 'r');
            $csv->setHeaderOffset(0); // Assuming the first row contains headers

            // Strip BOM (Byte Order Mark) from the header if present
            $headers = $csv->getHeader();
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            $csv->setHeaderOffset(0); // Reset header after stripping BOM
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to read the CSV file. Please check the file format.',
            ], 500);
        }

        // Log headers to debug
        \Log::info('CSV Headers: ', $headers);

        // Define a mapping from CSV column names to database field names
        $columnMapping = [
            'supplier_code' => 'supplier_code',
            'supplier_name' => 'supplier_name',
            'supplier_address' => 'supplier_address',
            'supplier_phone_number' => 'supplier_phone_number',
            'supplier_contact_person' => 'supplier_contact_person',
            'status' => 'status',
            'supplier_type' => 'supplier_type',
        ];

        // Get all records in the CSV
        $records = $csv->getRecords();
        $createdSuppliers = [];
        $errors = [];

        foreach ($records as $index => $record) {
            // Log each row being processed
            \Log::info("Row $index", $record);

            // Create a new array to hold the mapped data
            $mappedRecord = [];

            // Loop through each column in the record and map to the correct field
            foreach ($record as $csvColumn => $value) {
                // Check if the CSV column has a mapping
                if (array_key_exists($csvColumn, $columnMapping)) {
                    // Trim spaces from the value
                    $mappedRecord[$columnMapping[$csvColumn]] = trim($value);
                }
            }

            // Validate mapped data
            $validator = Validator::make($mappedRecord, [
                'supplier_code' => 'required|string|unique:suppliers,supplier_code',
                'supplier_name' => 'required|string',
                'supplier_address' => 'nullable|string',
                'supplier_phone_number' => 'nullable|string',
                'supplier_contact_person' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
                'supplier_type' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 1, // Including the row number (human-readable, starts from 1)
                    'errors' => $validator->errors()->all(),
                ];
                continue; // Skip this record and move to the next one
            }

            // Insert supplier data into the database
            try {
                $supplier = Supplier::create($mappedRecord);
                $createdSuppliers[] = $supplier;
            } catch (\Exception $e) {
                // Catch and record any error when inserting into the database
                $errors[] = [
                    'row' => $index + 1,
                    'errors' => ['Database error: ' . $e->getMessage()],
                ];
            }
        }

        // Delete the uploaded file after processing
        Storage::delete($path);

        if (count($errors) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Some records were not processed due to errors.',
                'errors' => $errors
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Suppliers uploaded successfully!',
            'data' => $createdSuppliers
        ], 201);
    }

}
