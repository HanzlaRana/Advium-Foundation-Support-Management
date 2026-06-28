<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'type',
        'icon',
        'total_helped',
        'is_active',
        'eligibility_criteria',
        'required_documents',
        'loan_amount',
        'loan_duration_months',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'is_active'          => 'boolean',
        'loan_amount'        => 'decimal:2',
    ];
}