<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TransactionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function getUserDashboardStats(Request $request)
    {
        $user = Auth::user();

        $statusLabels = [
            'Rejected',
            'Approved',
            'Cancelled',
            'For Review',
            'For Approval',
            'Interfaced',
            'New',
        ];

        $isAdmin = $user->role->role_name === 'Admin';
        $isAccounting = $user->role->role_name === 'Accounting';

        $transactionQuery = TransactionHeader::query();

        if ($isAdmin) {
            // Admin sees all
        } elseif ($isAccounting) {
            // Accounting sees transactions from their department
            $transactionQuery->where('department_id', $user->department_id);
        } else {
            // Other users see transactions where they are involved
            $transactionQuery->where(function ($query) use ($user) {
                $query->where('prepared_by', $user->id)
                    ->orWhere('review_by', $user->id)
                    ->orWhere('approved_by', $user->id);
            });
        }

        $transactions = $transactionQuery->get();

        // Group by status
        $statusCounts = $transactions->groupBy('status')->map->count();
        $statusCounts = collect($statusLabels)->mapWithKeys(fn($status) => [
            $status => $statusCounts->get($status, 0)
        ]);

        // Monthly stats for last 6 months
        $monthlyStatsQuery = TransactionHeader::select(
            DB::raw("FORMAT(created_at, 'MMM yyyy') as month"),
            'status',
            DB::raw('count(*) as count')
        )
            ->when(!$isAdmin, function ($query) use ($user, $isAccounting) {
                if ($isAccounting) {
                    return $query->where('department_id', $user->department_id);
                } else {
                    return $query->where(function ($q) use ($user) {
                        $q->where('prepared_by', $user->id)
                            ->orWhere('review_by', $user->id)
                            ->orWhere('approved_by', $user->id);
                    });
                }
            })
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("FORMAT(created_at, 'MMM yyyy')"), 'status')
            ->orderBy(DB::raw("FORMAT(created_at, 'MMM yyyy')"))
        ;

        $monthlyStats = $monthlyStatsQuery->get()
            ->groupBy('status')
            ->map(function ($group) {
                return $group->pluck('count', 'month')->toArray();
            });

        return response()->json([
            'status' => 'success',
            'total_transactions' => $transactions->count(),
            'transactions_by_status' => $statusCounts,
            'monthly_stats' => $monthlyStats,
        ]);
    }
}
