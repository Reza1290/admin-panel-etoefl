<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class TenseClaim extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tense_claims';
    protected $fillable = [
        'user_id', 'tense_id','is_true'
    ];

    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class,'user_id','_id');
    }

    public function tense(){
        return $this->belongsTo(Tense::class,'tense_id','_id');
    }

    
}
