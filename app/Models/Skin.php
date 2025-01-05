<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    use HasFactory;

    protected $fillable = [
        'champion_id',
        'name',
        'num'
    ];

    public function champion()
    {
        return $this->belongsTo(Champion::class);
    }
}
