<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class GameHistory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'game_history';
    protected $fillable = [
        'user_id',
            'game_type' ,
            'score' ,
    ];

    use HasFactory;
    
}
