<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class Leaderboard extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'leaderboards';
    protected $fillable = [
        'user_id','total_score',     'game_score','quiz_score','synonym_score','scrambled_score','tense_score','speaking_score','timestamp'
    ];

    use HasFactory;
    
    public function user(){
        return $this->belongsTo(User::class,'user_id','_id');
    }
}
