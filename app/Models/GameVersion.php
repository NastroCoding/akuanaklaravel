<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameVersion extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'version', 'storage_path', 'upload_timestamp'];

    /**
     * Mendefinisikan hubungan dengan Game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Mendefinisikan hubungan dengan Score.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
