<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'state',
        'hourly_rate',
        'capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function studioSessions(): HasMany
    {
        return $this->hasMany(StudioSession::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
