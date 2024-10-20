<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Password extends Model
{
    use HasFactory;
    public function saveModel($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->passwd = Hash::make($data['password']);
        $this->save();
        $passwdHistories = new PasswdHistories();
        $passwdHistories->saveModel($this,$data, 0);
    }
    public function updateModel($data)
    {
        $this->passwd = Hash::make($data['password']);
        $this->save();
        $passwdHistories = new PasswdHistories();
        $passwdHistories->saveModel($this,$data, 1);
    }
    public function deleteModel($data){
        $passwdHistories = new PasswdHistories();
        $passwdHistories->saveModel($this,$data, 1);
        $this->delete();
    }
    
}
