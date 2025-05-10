<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Department name, e.g., 'Sales', 'HR'
    ];

    // Define the inverse relationship with User
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user_role')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    // Define relationship with department_approvers (Pivot Table)
    public function approvers()
    {
        return $this->belongsToMany(User::class, 'department_approvers')
            ->withPivot('role') // The role in the department (e.g., 'Encoder', 'Approver')
            ->withTimestamps();
    }
}
