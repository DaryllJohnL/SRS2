<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_header_id',
        'transaction_amount',
        'bank_code_id',
        'transaction_reference_no',
        'supplier_ref_no',
        'supplier_reference_date',
        'transaction_reference_date',
        'reference_type_id',
    ];

    // Relationships
    public function transactionHeader()
    {
        return $this->belongsTo(TransactionHeader::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_code_id');
    }

    public function referenceType()
    {
        return $this->belongsTo(ReferenceType::class, 'reference_type_id');
    }
}
