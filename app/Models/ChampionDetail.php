<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChampionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'image',
        'lore',
        'tags',
        'info',
        'stats',
        'spells',
        'passive',
    ];

    // Definieer de relatie met Champion
    public function champion()
    {
        return $this->belongsTo(Champion::class);
    }
}
