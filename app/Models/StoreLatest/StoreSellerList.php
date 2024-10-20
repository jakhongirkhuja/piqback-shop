<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSellerList extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    /**
     * Get the user that owns the StoreSellerList
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(StoreSeller::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function saveModel($data, $status)
    {
        $this->store_id = $data['store_id'];
        $this->seller_id = $data['seller_id'];
        $this->save();
        $storehistory = new StoreSellerListHistory();
        $storehistory->saveModel($this,$data,$status);
    }
   
    public function deleteModel($data, $status)
    {
        $storehistory = new StoreSellerListHistory();
        $storehistory->saveModel($this,$data,$status);
        $this->delete();
    }
    public function saveModelBot($data, $status,$userId, $store_id, $seller_id)
    {
        $this->store_id = $store_id;
        $this->seller_id = $seller_id;
        $this->save();
        $storehistory = new StoreSellerListHistory();
        $storehistory->saveModelBot($this,$data,$status,$userId);
    }
    public function deleteModelBot($data, $status, $userId)
    {
        $storehistory = new StoreSellerListHistory();
        $storehistory->saveModelBot($this,$data,$status,$userId);
        $this->delete();
    }
    
}
