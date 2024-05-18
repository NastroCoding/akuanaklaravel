<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function versions()
    {
        return $this->hasMany(GameVersion::class);
    }
}
