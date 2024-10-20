<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class DigitalStoreProduct extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($data, $status)
    {
        $this->category_id = $data['category_id'];
        $this->store_id = $data['store_id'];
        $this->price = $data['price'];
        $this->amount = $data['amount'];
        $this->ordered = $data['ordered'];
        $test['uz'] = $data['name_uz'];
        $test['ru'] = $data['name_ru'];
        $this->name = json_encode($test);

        if(isset($data['img'])){
            $docoded = json_decode($this->img, true);
            foreach ($data['img'] as $key => $productImage) {
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$productImage->getClientOriginalExtension();
                $productImage->move(public_path('/files/digitalStoresProduct'),$bannerName);
                $docoded[] = $bannerName;
            }
            
            $this->img =  json_encode($docoded);
        }

        $this->save();
        $digitalStoreProductHistory = new DigitalStoreProductHistory();
        $digitalStoreProductHistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $this->category_id = $data['category_id'];
        $this->store_id = $data['store_id'];
        $this->price = $data['price'];
        $this->amount = $data['amount'];
        $this->ordered = $data['ordered'];
        $test['uz'] = $data['name_uz'];
        $test['ru'] = $data['name_ru'];
        $this->name = json_encode($test);
        
        if(isset($data['img'])){
            $docoded = json_decode($this->img, true);
            foreach ($data['img'] as $key => $productImage) {
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$productImage->getClientOriginalExtension();
                $productImage->move(public_path('/files/digitalStoresProduct'),$bannerName);
                $docoded[] = $bannerName;
            }
            
            $this->img =  json_encode($docoded);
        }
        $this->save();
        $digitalStoreProductHistory = new DigitalStoreProductHistory();
        $digitalStoreProductHistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $digitalStoreProductHistory = new DigitalStoreProductHistory();
        $digitalStoreProductHistory->saveModel($this,$data,$status);
        $docoded = json_decode($this->img, true);
        foreach ($docoded as $key => $image) {
           
            if(file_exists(public_path('/files/digitalStoresProduct/'.$image))){
                unlink(public_path('/files/digitalStoresProduct/'.$image));
            }
        }
        $this->delete();
    }
    public function deleteImage($data)
    {
        $docoded = json_decode($this->img, true);
        $inside = false;
        foreach ($docoded as $key => $image) {
            if($image==$data['imageName']){
                if(file_exists(public_path('/files/digitalStoresProduct/'.$image))){
                    unlink(public_path('/files/digitalStoresProduct/'.$image));
                }
            }else{
                $inside = true;
                $bannerName[] = $image;
            }
            
        }
        if($inside){
            $this->img = json_encode($bannerName);
        }
        $this->save();
    }
}
