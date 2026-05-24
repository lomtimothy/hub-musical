<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function studios(): MorphToMany
    {
        return $this->morphedByMany(Studio::class, 'taggable');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
}
