<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'champion_id'];

    // Relatie naar de champion
    public function champion()
    {
        return $this->belongsTo(Champion::class);
    }
}
