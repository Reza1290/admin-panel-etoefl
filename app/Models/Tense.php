<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;


class Tense extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tenses';
    protected $fillable = [
        'sentence', 'tense'
    ];

    use HasFactory;
    
}
