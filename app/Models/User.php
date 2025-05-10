<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ✅ Must extend this
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Specify custom timestamp column names
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'last_update_date';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'department_id',
        'created_by',
    ];

    protected $hidden = [
        'password', // ✅ Hide password from JSON responses
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'created_date',
        'last_update_date',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // The user belongs to a single department (update this to belongTo relationship)
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // This defines the many-to-many relationship with departments (via a pivot table)
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_user_role')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    // Many-to-many relationship with department_approvers (assuming each department has multiple approvers)
    public function approvers()
    {
        return $this->belongsToMany(Department::class, 'department_approvers')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }
}
