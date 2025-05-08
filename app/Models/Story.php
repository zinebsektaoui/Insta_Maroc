<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'caption',
        'viewed',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'viewed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function markAsViewed()
    {
        $this->update(['viewed' => true]);
    }
}
