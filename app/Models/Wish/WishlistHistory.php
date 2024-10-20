<?php

namespace App\Models\Wish;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql5';
    public function saveModel($model, $data, $status)
    {
        $this->user_id = $model->user_id;
        $this->course_id = $model->course_id;
        $this->save();
        StandardAttributes::setSA('wishlist_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql5');
    }
}
