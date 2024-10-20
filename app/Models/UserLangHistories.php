<?php

namespace App\Models;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLangHistories extends Model
{
    use HasFactory;
    public function saveRegisterModel($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->lang= 'ru';
        $this->save();
        StandardAttributes::setSA('user_lang_histories',$this->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
    }
}
