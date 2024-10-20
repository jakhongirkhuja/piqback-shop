<?php

namespace App\Models\StoreLatest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerTelegram extends Model
{
    use HasFactory;
    protected $connection = 'pgsql8';
    public function saveModel($data, $status)
    {
        $this->telegram_id = $data['telegram_id'];
        $this->seller_id = $data['seller_id'];
        $this->save();
        $storehistory = new SellerTelegramHistory();
        $storehistory->saveModel($this,$data,$status);
    }
    
    public function deleteModel($data, $status)
    {
        $storehistory = new SellerTelegramHistory();
        $storehistory->saveModel($this,$data,$status);
        $this->delete();
    }


    public function saveModelBot($data, $status, $seller_id)
    {
        $this->telegram_id = $data['telegram_id'];
        $this->seller_id = $seller_id;
        $this->save();
        $storehistory = new SellerTelegramHistory();
        $storehistory->saveModelBot($this,$data,$status,$data['telegram_id']);
    }
}
