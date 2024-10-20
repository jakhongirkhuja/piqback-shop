<?php

namespace App\Models\Money;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iqc extends Model
{
    use HasFactory;
    protected $connection = 'pgsql4';
    public function updateModel($data,$value, $valueType, $serviceName, $service_id=null, $notify=null)
    {
        if($valueType==0){
            $this->amountofIQC = $this->amountofIQC - $value;
        }else{
            $this->amountofIQC = $this->amountofIQC + $value;
        }
        $this->save();
        $iqchistory = new IqcTransaction();
        $iqchistory->saveModel($this,$data,$value,$valueType, $serviceName, $service_id, $notify);
    }
    public function saveModel($data, $user_id, $value, $valueType, $serviceName, $service_id=null)
    {
        $this->user_id =$user_id;
        $this->amountofIQC = $value;
        $this->save();
        $iqchistory = new IqcTransaction();
        $iqchistory->saveModel($this,$data,$value,$valueType, $serviceName, $service_id);
    }
    public function updateStoreModel($data,$value, $valueType, $serviceName, $service_id)
    {
        if($valueType==0){
            $this->amountofIQC = $this->amountofIQC - $value;
        }else{
            $this->amountofIQC = $this->amountofIQC + $value;
        }
        $this->save();
        $iqchistory = new IqcTransaction();
        $iqchistory->saveModel($this,$data,$value,$valueType, $serviceName, $service_id);
    }
}
