<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'beneficiary_code',
        'full_name',
        'cnic',
        'phone',
        'address',
        'status',
        'photo',
    ];
}