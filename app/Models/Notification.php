<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'reference_type',
        'reference_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static helper to send notification
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'general',
        string $referenceType = null,
        int $referenceId = null
    ): void {
        static::create([
            'user_id'        => $userId,
            'title'          => $title,
            'message'        => $message,
            'type'           => $type,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
        ]);
    }
}