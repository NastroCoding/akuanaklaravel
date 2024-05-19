<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'games';

    public function versions()
    {
        return $this->hasMany(GameVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(GameVersion::class)->latest();
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function scores()
    {
        return $this->hasManyThrough(Score::class, GameVersion::class);
    }
}
