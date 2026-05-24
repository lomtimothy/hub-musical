<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function studios(): MorphedByMany
    {
        return $this->morphedByMany(Studio::class, 'taggable');
    }

    public function users(): MorphedByMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
}
