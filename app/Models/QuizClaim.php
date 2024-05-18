<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class QuizClaim extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'quiz_claims';

    protected $fillable = [
        'user_id',
        'quiz_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','_id');
    }

    public function quiz(){
        return $this->belongsTo(Quiz::class,'quiz_id','_id');
    }
}
