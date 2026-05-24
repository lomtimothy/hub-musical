<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'studio_session_id',
        'uploaded_by',
        'title',
        'original_name',
        'path',
        'mime_type',
        'size',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function studioSession(): BelongsTo
    {
        return $this->belongsTo(StudioSession::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
