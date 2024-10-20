<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreObject extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($data, $status)
    {
        $test['uz'] = $data['objectName_uz'];
        $testv['uz'] = $data['objectDescription_uz'];
        $test['ru'] = $data['objectName_ru'];
        $testv['ru'] =$data['objectDescription_ru'];
        $this->objectName = json_encode($test);
        $this->objectDescription = json_encode($testv);
        $this->objectCost = $data['objectCost'];
        $this->objectAmount= $data['objectAmount'];
        
        
        $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['objectIMG']->getClientOriginalExtension();
        $data['objectIMG']->move(public_path('/files/store'),$bannerName);
        $this->objectIMG = $bannerName;
        $this->save();
        $StoreObjecthistory = new StoreObjectHistory();
        $StoreObjecthistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $test['uz'] = $data['objectName_uz'];
        $testv['uz'] = $data['objectDescription_uz'];
        $test['ru'] = $data['objectName_ru'];
        $testv['ru'] =$data['objectDescription_ru'];
        $this->objectName = json_encode($test);
        $this->objectDescription = json_encode($testv);
        $this->objectCost = $data['objectCost'];
        $this->objectAmount= $data['objectAmount'];
        
        if(isset($data['objectIMG']) && file_exists(public_path('/files/store/'.$this->objectIMG))){
            unlink(public_path('/files/store/'.$this->objectIMG));
            
        }
        if(isset($data['objectIMG'])){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['objectIMG']->getClientOriginalExtension();
            $data['objectIMG']->move(public_path('/files/store'),$bannerName);
            $this->objectIMG = $bannerName;
        }
        $this->save();
        $StoreObjecthistory = new StoreObjectHistory();
        $StoreObjecthistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $StoreObjecthistory = new StoreObjectHistory();
        $StoreObjecthistory->saveModel($this,$data,$status);
        if(file_exists(public_path('/files/store/'.$this->objectIMG))){
            unlink(public_path('/files/store/'.$this->objectIMG));
        }
        $this->delete();
    }
    
}
