<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreProduct extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    /**
     * Get the user that owns the StoreProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function saveModel($data, $status)
    {
        $test['uz'] = strtoupper($data['productName_uz']);
        $testv['uz'] = $data['productDescription_uz'];
        $test['ru'] = strtoupper($data['productName_ru']);
        $testv['ru'] =$data['productDescription_ru'];
        $this->productName = json_encode($test);
        $this->productDescription = json_encode($testv);
        $this->productCost = $data['productCost'];
        $this->productAmount= $data['productAmount'];
        $this->store_id = $data['store_id'];
        $this->category_id = $data['category_id'];
        foreach ($data['productIMG'] as $key => $productImage) {
            
            $bannerSt = (string) Str::uuid().'-'.Str::random(15).'.'.$productImage->getClientOriginalExtension();
            $productImage->move(public_path('/files/store'),$bannerSt);
            $bannerName[] = $bannerSt;
        }
        
        $this->productIMG = json_encode($bannerName);
        $this->save();
        $Storeproducthistory = new StoreProductHistory();
        $Storeproducthistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $test['uz'] = strtoupper($data['productName_uz']);
        $testv['uz'] = $data['productDescription_uz'];
        $test['ru'] = strtoupper($data['productName_ru']);
        $testv['ru'] =$data['productDescription_ru'];
        $this->productName = json_encode($test);
        $this->productDescription = json_encode($testv);
        $this->productCost = $data['productCost'];
        $this->productAmount= $data['productAmount'];
        $this->store_id = $data['store_id'];
        $this->category_id = $data['category_id'];
        $this->iqc= $data['iqc'];
        // if(isset($data['productIMG']) && file_exists(public_path('/files/store/'.$this->productIMG))){
        //     unlink(public_path('/files/store/'.$this->productIMG));
            
        // }
        if(isset($data['productIMG'])){
            $docoded = json_decode($this->productIMG, true);
            foreach ($data['productIMG'] as $key => $productImage) {
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$productImage->getClientOriginalExtension();
                $productImage->move(public_path('/files/store'),$bannerName);
                $docoded[] = $bannerName;
            }
            
            $this->productIMG =  json_encode($docoded);
        }
        $this->save();
        $Storeproducthistory = new StoreProductHistory();
        $Storeproducthistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data, $status)
    {
        $Storeproducthistory = new StoreProductHistory();
        $Storeproducthistory->saveModel($this,$data,$status);
        $docoded = json_decode($this->productIMG, true);
        foreach ($docoded as $key => $image) {
            # code...
            if(file_exists(public_path('/files/store/'.$image))){
                unlink(public_path('/files/store/'.$image));
            }
        }
        $this->delete();
    }
    public function deleteImage($data)
    {
        $docoded = json_decode($this->productIMG, true);
        $inside = false;
        foreach ($docoded as $key => $image) {
            if($image==$data['imageName']){
                if(file_exists(public_path('/files/store/'.$image))){
                    unlink(public_path('/files/store/'.$image));
                }
            }else{
                $inside = true;
                $bannerName[] = $image;
            }
            
        }
        if($inside){
            $this->productIMG = json_encode($bannerName);
        }
        $this->save();
    }
}
