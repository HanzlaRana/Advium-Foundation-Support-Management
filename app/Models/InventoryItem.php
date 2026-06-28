<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'description',
        'unit',
        'quantity_in_stock',
        'quantity_reserved',
        'quantity_distributed',
        'reorder_level',
        'unit_cost',
        'expiry_date',
        'supplier',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_active'   => 'boolean',
        'unit_cost'   => 'decimal:2',
    ];

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->reorder_level;
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->diffInDays(now()) <= 30;
    }
}