<?php

namespace App\Models\Promocode;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromocodeHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($model, $data, $status)
    {
        $this->promocode= $model->promocode;
        $this->prizeType= $model->prizeType;
        $this->prizeAmount= $model->prizeAmount;
        $this->startDate= $model->startDate;
        $this->endDate= $model->endDate;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('promocode_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql7');
    }
}
