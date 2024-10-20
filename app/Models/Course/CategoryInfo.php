<?php

namespace App\Models\Course;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryInfo extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
}
