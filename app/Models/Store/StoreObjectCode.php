<?php

namespace App\Models\Store;

use App\Models\Money\Iqc;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreObjectCode extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($data,$countOrder,$iqc,$store)
    {
        $this->object_id = $data['object_id'];
        $this->user_id = auth()->user()->id;
        $this->objectOrderTime = Carbon::now();
        $this->objectOrderCount = $countOrder;
        $generateUrl = 'url';
        $this->objectCode = $generateUrl;
        $this->save();
        $iqc->updateStoreModel($data, $store->objectCost,0,'storeObject',  auth()->user()->id);
    }
}
