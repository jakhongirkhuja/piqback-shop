<?php

namespace App\Models\StoreLatest;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StoreProductCode extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
   
    public function product()
    {
        return $this->belongsTo(StoreProduct::class, 'product_id');
    }
    public function saveModel($data,$countOrder,$iqc,$store)
    {
        $this->product_id = $data['product_id'];
        $this->user_id = auth()->user()->id;
        $this->productOrderTime = Carbon::now();
        $this->productOrderCount = $countOrder;
        $generateUrl = (string) Str::uuid();
        $this->productCode = $generateUrl;
        $this->save();
        $iqc->updateStoreModel($data, (int)$store->iqc,0,'storeProduct',  $this->product_id);
    }
}
