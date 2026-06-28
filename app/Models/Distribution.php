<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'application_id',
        'distributed_by',
        'delivery_method',
        'scheduled_date',
        'actual_date',
        'location',
        'recipient_name',
        'recipient_cnic',
        'recipient_phone',
        'proof_photo',
        'notes',
        'status',
        'qr_code',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_date'    => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function distributedBy()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }
}