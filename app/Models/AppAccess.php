<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppAccess extends Model
{
    use HasFactory;
    public function saveModel($user_id,$data)
    {
        $this->user_id= $user_id;
        $os='android';
        if(str_contains($data['device'], 'iPhone')){
            $os='iOS';
        }
        $this->os= $os;
        $this->save();
       
    }
}
