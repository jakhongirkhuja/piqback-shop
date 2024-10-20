<?php

namespace App\Models\Certificate;

use App\Models\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
   
    public function courses()
    {
        return $this->belongsTo(Course::class, 'course_id','id');
    }
}
