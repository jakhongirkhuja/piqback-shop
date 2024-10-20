<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';

    /**
     * Get the user that owns the Store
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
   
    public function owner()
    {
        return $this->hasMany(StoreSellerList::class,'store_id','id');
    }
    public function saveModel($data, $status)
    {
       
        $this->category_id = $data['category_id'];
        $this->storeOwner = $data['storeOwner'];
        $this->storeName = $data['storeName'];
        $this->storeLongitude = $data['storeLongitude'];
        $this->storeLatitude = $data['storeLatitude'];

        $storeLandmark['ru'] = $data['storeLandmark_ru'];
        $storeLandmark['uz'] = $data['storeLandmark_uz'];  
        $this->storeLandmark = json_encode($storeLandmark);
        $storeDescription['ru'] = $data['storeDescription_ru'];
        $storeDescription['uz'] = $data['storeDescription_uz'];  
        $this->storeDescription = json_encode($storeDescription);
        
        $this->save();
        $storehistory = new StoreHistory();
        $storehistory->saveModel($this,$data,$status);
    }
    
    public function deleteModel($data, $status)
    {
        $storehistory = new StoreHistory();
        $storehistory->saveModel($this,$data,$status);
        $this->delete();
    }
}
