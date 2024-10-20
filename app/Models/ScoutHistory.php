<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model, $data)
    {
        $this->group_id = $model->group_id;
        $this->scout_id  = $model->scout_id;
       
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        
        StandardAttributes::setSA('scout_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2'); 
    }
}
