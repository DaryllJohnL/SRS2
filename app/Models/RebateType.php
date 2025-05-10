<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RebateType extends Model
{
    use HasFactory;

    protected $fillable = ['rebate_code', 'rebate_name']; // Add more columns if needed
}
