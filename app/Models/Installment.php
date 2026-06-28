<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'paid_date',
        'amount',
        'paid_amount',
        'fine_amount',
        'status',
        'payment_method',
        'receipt_number',
        'collected_by',
        'notes',
    ];

    protected $casts = [
        'due_date'    => 'date',
        'paid_date'   => 'date',
        'amount'      => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}