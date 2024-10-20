<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorzinkaBarcode extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    protected $fillable = ['category_id','store_id','barcode','facevalue','uploader'];
    public function saveModel($data, $store)
    {
        $this->category_id = $store->category_id;
        $this->store_id = $data['store_id'];
        $this->barcode = $data['barcode'];
        $this->facevalue = str_replace(' ', '', $data['facevalue']);
        $this->uploader = auth()->user()? auth()->user()->id : 1;
        $this->save();
    }
}
