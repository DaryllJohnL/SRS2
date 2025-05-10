<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHeader extends Model
{
    use HasFactory;

    // Define the table name if it differs from the plural of the model name
    protected $table = 'transaction_headers';

    // Define the fillable attributes (fields you want to allow mass assignment)
    protected $fillable = [
        'supplier_code',
        'rebate_type',
        'particulars',
        'incentive',
        'conversion',
        'prepared_by',
        'approved_by',
        'review_by',
        'approve_date',
        'status',
        'department_id' // Add this
    ];

    // Relationship
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Define relationships

    // A transaction header belongs to a supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_code');
    }

    // A transaction header belongs to a rebate type
    public function rebateType()
    {
        return $this->belongsTo(RebateType::class, 'rebate_type');
    }

    // A transaction header is prepared by a user
    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    // A transaction header is approved by a user
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // A transaction header is reviewed by a user
    public function reviewBy()
    {
        return $this->belongsTo(User::class, 'review_by');
    }
}
