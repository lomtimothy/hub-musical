<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudioSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'booked_by',
        'title',
        'notes',
        'starts_at',
        'ends_at',
        'status',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'total_price' => 'decimal:2',
        ];
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function booker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function musicians(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['instrument', 'payment_split'])
            ->withTimestamps();
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }
}
