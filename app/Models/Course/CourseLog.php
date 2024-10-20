<?php

namespace App\Models\Course;

use App\Helper\StandardAttributes;
use App\Models\Money\IqcTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        
        'created_at'
    ];
    protected $appends = ['timedone'];
    public function getTimedoneAttribute()
    {
        // $iqcTransaction = IqcTransaction::select('created_at')->where('user_id',$this->user_id)->where('serviceName','quiz')->where('identityText', $this->course_id)->latest()->first();
        // if($iqcTransaction){

        // }
        return null;
    }
    public function saveOrUpdate($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->course_id = $data['course_id'];
        $this->status = $data['status'];
        $this->save();
        StandardAttributes::setSA('course_logs',$this->id,$this->status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql3' );
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
