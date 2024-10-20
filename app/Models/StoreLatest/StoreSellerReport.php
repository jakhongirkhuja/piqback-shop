<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreSellerReport extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    
    public function seller()
    {
        return $this->belongsTo(StoreSeller::class, 'seller_id');
    }
    public function storeproductcode()
    {
        return $this->belongsTo(StoreProductCode::class, 'product_code_id');
    }
    public function saveModel($data,$user)
    {
        $this->product_code_id = $data['product_code_id'];
        $this->seller_id= $user? $user->id : $data['seller_id'];
        $this->action= $data['action'];
        $this->shortDescription= $data['shortDescription'];
        if(isset($data['reportIMG']) && $data['reportIMG']){
            $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['reportIMG']->getClientOriginalExtension();
            $data['reportIMG']->move(public_path('/files/storeSeller'),$bannerName);
            $this->reportIMG = $bannerName;
        }
        $this->save();
    }
}
