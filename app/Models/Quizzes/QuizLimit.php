<?php

namespace App\Models\Quizzes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizLimit extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
}
