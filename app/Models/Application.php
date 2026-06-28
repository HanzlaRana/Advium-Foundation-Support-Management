<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'application_code',
        'applicant_id',
        'program_id',
        'status',
        'priority',
        'assigned_to',
        'notes',
        'rejection_reason',
        'documents',
        'submitted_at',
        'approved_at',
        'distributed_at',
    ];

    protected $casts = [
        'documents'      => 'array',
        'submitted_at'   => 'datetime',
        'approved_at'    => 'datetime',
        'distributed_at' => 'datetime',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }
}