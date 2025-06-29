<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'data_type'];

}
