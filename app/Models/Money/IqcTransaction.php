<?php

namespace App\Models\Money;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IqcTransaction extends Model
{
    use HasFactory;
    protected $connection = 'pgsql4';
    public function saveModel($model, $data,$value,$valueType, $serviceName, $service_id=null,$notify=null)
    {
        if(isset($data['ref_id']) && $data['ref_id']){
            $user = User::where('hrid', $data['ref_id'])->first();
            $this->user_id = $user->id;
        }else{
            if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                $this->user_id = $model->user_id;
            }else{
                if(isset($data['force'])){
                    $this->user_id =  $model->user_id;
                }else{
                    $this->user_id = auth()->user()->id;
                }
                
                
            }
        }
        $this->serviceName = $serviceName;
        $this->value = $value;
        $this->valueType = $valueType;
        $this->identityText = $service_id;
        $this->amountofIQC = $model->amountofIQC;
        if($notify){
            $this->notify = (int)$notify;
        }
        $this->save();
    }
}
