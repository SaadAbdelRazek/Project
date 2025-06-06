<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;


    protected $fillable = ['question'];

    // A question can have many answers
    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }
}
