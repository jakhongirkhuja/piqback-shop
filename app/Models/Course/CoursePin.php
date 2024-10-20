<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePin extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
   
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function saveModel($category_id, $course_id , $accassPrivate, $order, $status, $data){
        $this->access = $accassPrivate;
        $this->category_id = $category_id;
        $this->course_id = $course_id;
        $this->pinOrder = $order; 
        $this->save();
        $pinorderHistory = new CoursePinHistory();
        $pinorderHistory->saveModel($this, $data, $status);
    }
    public function deleteModel($status,$data){
        $pinorderHistory = new CoursePinHistory();
        $pinorderHistory->saveModel($this, $data, $status);
        $this->delete();
    }
}
