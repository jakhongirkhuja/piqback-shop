<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutedListHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql2';
    public function saveModel($model ,$data)
    {
        $this->group_id =$model->group_id;
        $this->scout_id = $model->scout_id;
        $this->company_id =$model->company_id;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('scouted_list_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');
    }
}
