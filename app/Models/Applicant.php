<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'full_name',
        'cnic',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'province',
        'family_members',
        'monthly_income',
        'occupation',
        'photo',
        'cnic_front',
        'cnic_back',
        'is_blacklisted',
        'blacklist_reason',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'is_blacklisted' => 'boolean',
        'monthly_income' => 'decimal:2',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}