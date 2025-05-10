<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role_name'];  // Allow mass assignment for role_name
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user_role')
            ->withPivot('department_id')
            ->withTimestamps();
    }
}
