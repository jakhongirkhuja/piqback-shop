<?php

namespace App\Models\Lessons;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function saveOrUpdate($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->lesson_id = $data['lesson_id'];
        $this->typeContent = $data['typeContent'];
        $this->addressIP  = request()->ip();
        $this->platform = $data['platform'];
        $this->device = $data['device'];
        $this->browser = $data['browser'];
        $this->timeZone = $data['timeZone'];
        $this->save();
    }
}
