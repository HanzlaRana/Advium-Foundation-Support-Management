<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'application_id',
        'applicant_id',
        'loan_number',
        'asset_type',
        'total_amount',
        'down_payment',
        'loan_amount',
        'monthly_installment',
        'total_installments',
        'paid_installments',
        'total_paid',
        'remaining_balance',
        'start_date',
        'end_date',
        'status',
        'guarantor_name',
        'guarantor_cnic',
        'guarantor_phone',
        'notes',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'total_amount'        => 'decimal:2',
        'down_payment'        => 'decimal:2',
        'loan_amount'         => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_paid'          => 'decimal:2',
        'remaining_balance'   => 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}