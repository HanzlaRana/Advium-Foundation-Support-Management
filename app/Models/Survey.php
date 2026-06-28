<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'application_id',
        'volunteer_id',
        'house_type',
        'house_condition',
        'rooms',
        'has_electricity',
        'has_gas',
        'has_water',
        'has_internet',
        'total_members',
        'earning_members',
        'school_going_children',
        'total_monthly_income',
        'total_monthly_expenses',
        'employment_status',
        'eligibility_result',
        'notes',
        'photos',
        'visited_at',
    ];

    protected $casts = [
        'has_electricity'       => 'boolean',
        'has_gas'               => 'boolean',
        'has_water'             => 'boolean',
        'has_internet'          => 'boolean',
        'photos'                => 'array',
        'visited_at'            => 'datetime',
        'total_monthly_income'  => 'decimal:2',
        'total_monthly_expenses'=> 'decimal:2',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}