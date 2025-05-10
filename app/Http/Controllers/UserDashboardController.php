<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function getUserDashboardStats(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get transactions where the header's department_id matches the user's department
        $transactions = TransactionHeader::with('supplier')
            ->where('department_id', $departmentId)
            ->get();

        // Count transactions by status
        $statusCounts = $transactions->groupBy('status')->map->count();

        // Ensure all possible statuses are included
        $statusLabels = [
            'Rejected',
            'Approved',
            'Cancelled',
            'For Review',
            'For Approval',
            'Interfaced',
            'New',
        ];

        $statusCounts = collect($statusLabels)->mapWithKeys(function ($status) use ($statusCounts) {
            return [$status => $statusCounts->get($status, 0)];
        });

        return response()->json([
            'status' => 'success',
            'total_transactions' => $transactions->count(),
            'transactions_by_status' => $statusCounts,
        ]);
    }
}
