<?php

namespace App\Models\Promocode;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($data, $status)
    {
        $this->promocode= $data['promocode'];
        $this->prizeType= $data['prizeType'];
        $this->prizeAmount= $data['prizeAmount'];
        $this->startDate= $data['startDate'];
        $this->endDate= $data['endDate'];
        $this->amountOfWinners =$data['amountOfWinners']=='null'? null : $data['amountOfWinners'];
        $this->save();
        $promoceCodehistory = new PromocodeHistory();
        $promoceCodehistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data)
    {
        $promoceCodehistory = new PromocodeHistory();
        $promoceCodehistory->saveModel($this,$data,'disabled');
        $this->delete();
    }
}
