<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreSeller extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function sellerStoreList()
    {
        return $this->belongsTo(StoreSellerList::class,'id','seller_id');
    }
    public function telegram()
    {
        return $this->belongsTo(SellerTelegram::class,'id','seller_id');
    }
    public function saveModel($data, $status)
    {
       
        $lastId = StoreSeller::latest()->first();
        if($lastId){
            $lastId = $lastId->id + 13;
            
            $this->id = $lastId;
            
        }
        $this->sellerName = $data['sellerName'];
        $this->sellerPhone = $data['sellerPhone'];
        $this->role = $data['role'];
        $this->save();
        $storehistory = new StoreSellerHistory();
        $storehistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
       
        
        $this->sellerName = $data['sellerName'];
        $this->sellerPhone = $data['sellerPhone'];
        $this->role = $data['role'];
        $this->save();
        $storehistory = new StoreSellerHistory();
        $storehistory->saveModel($this,$data,$status);
    }
    
    public function deleteModel($data, $status)
    {
        $storehistory = new StoreSellerHistory();
        $storehistory->saveModel($this,$data,$status);
        $this->delete();
    }

    public function saveModelBot($data, $status,$userId)
    {
       
        $lastId = StoreSeller::latest()->first();
        if($lastId){
            $lastId = $lastId->id + 13;
            
            $this->id = $lastId;
            
        }
        $this->sellerName = $data['sellerName'];
        $this->sellerPhone = $data['sellerPhone'];
        $this->role =0;
        $this->save();
        $storehistory = new StoreSellerHistory();
        $storehistory->saveModelBot($this,$data,$status,$userId);
    }
}
