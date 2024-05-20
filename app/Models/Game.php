<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'description', 'thumbnail', 'uploadTimestamp', 'author', 'scoreCount', 'created_by'
    ];

    protected $dates = ['uploadTimestamp'];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return url($this->thumbnail);
        }
        return null;
    }

    public function versions()
    {
        return $this->hasMany(GameVersion::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scores()
    {
        return $this->hasManyThrough(Score::class, GameVersion::class);
    }
}