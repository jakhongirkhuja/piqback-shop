<?php

namespace App\Imports;

use App\Models\Store\KorzinkaBarcode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BarcodeImport implements ToCollection
{
    protected $data;
    protected $store;
    public function __construct($data, $store) {
        $this->data = $data;
        $this->store = $store;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $usr = auth()->user()? auth()->user()->id : 1;
        foreach ($rows as $row) 
        {
            $korzinkabarcode = KorzinkaBarcode::where('barcode',$row[1])->first();
            if(!$korzinkabarcode){
                KorzinkaBarcode::create([
                    'category_id'=>$this->store->category_id,
                    'store_id'=>$this->data['store_id'],
                    'barcode' => $row[1],
                    'facevalue' => (int)$row[2],
                    'uploader'=>$usr,
                ]);
            }
            
        }
    }
}
