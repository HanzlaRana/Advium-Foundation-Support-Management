<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_name', 'donor_phone', 'donor_email',
        'type', 'amount', 'date', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}