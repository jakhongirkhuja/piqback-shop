<?php

namespace App\Models\Store;

use App\Models\StoreLatest\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PhysicalStory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
   
    public function sellers()
    {
        return $this->hasMany(StoreSellerList::class,'store_id','id');
    }
    public function saveModel($data, $status)
    {
        $this->category_id = $data['category_id'];
        $this->owner = $data['owner'];
        $this->longitude = $data['longitude'];
        $this->latitude = $data['latitude'];
        $storeLandmark['ru'] = $data['landmark_ru'];
        $storeLandmark['uz'] = $data['landmark_uz'];  
        $this->landmark = json_encode($storeLandmark);
        $storeDescription['ru'] = $data['description_ru'];
        $storeDescription['uz'] = $data['description_uz'];  
        $this->description = json_encode($storeDescription);

        $this->mainContact = $data['mainContact'];
        $this->extraContact = $data['extraContact'];
        $this->name = $data['name'];
        $this->save();
        $physicalStoreHistory = new PhysicalStoreHistory();
        $physicalStoreHistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $physicalStoreHistory = new PhysicalStoreHistory();
        $physicalStoreHistory->saveModel($this,$data,$status);
        $this->delete();
    }
}
