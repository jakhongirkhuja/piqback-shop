<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorzinkaBarcodeOrder extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($iqc, $korzinkaBarCode, $data, $productIQC)
    {
        $this->category_id = $korzinkaBarCode->category_id;
        $this->store_id = $korzinkaBarCode->store_id;
        $this->barcode = $korzinkaBarCode->barcode;
        $this->facevalue = $korzinkaBarCode->facevalue;
        $this->toUser = auth()->user()? auth()->user()->id : 1;
        $this->save();
        $iqc->updateStoreModel($data, $productIQC,0,'storeProductDigital',  $korzinkaBarCode->id);
    }
    
    /**
     * Get the user that owns the KorzinkaBarcodeOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(DigitalStore::class, 'store_id');
    }
}
