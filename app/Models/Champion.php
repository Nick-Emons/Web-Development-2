<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Champion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',           
        'name',          
        'title',        
        'blurb',        
        'image',        
        'lore',        
        'tags',          
        'info',       
        'stats',        
        'spells',        
        'passive',      
        'skins'         
    ];

    protected $casts = [
        'tags' => 'array',
        'info' => 'array',
        'stats' => 'array',
        'spells' => 'array',
        'passive' => 'array',
        'skins' => 'array', 
    ];
}
