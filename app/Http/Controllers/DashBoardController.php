<?php

namespace App\Http\Controllers;

use App\Models\ReferenceType;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\Role;
use App\Models\RebateType;
use App\Models\Reference;
use App\Models\Bank;

class DashboardController extends Controller
{
    public function getDashboardStats()
    {
        return response()->json([
            'status' => 'success',
            'total_users' => User::count(),
            'total_suppliers' => Supplier::count(),
            'total_departments' => Department::count(),
            'total_roles' => Role::count(),
            'total_rebates' => RebateType::count(),
            'total_references' => ReferenceType::count(),
            'total_banks' => Bank::count(),
        ]);
    }
}
