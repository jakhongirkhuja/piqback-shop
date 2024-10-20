<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phonebook extends Model
{
    use HasFactory;
    public function saveModel($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->phoneNumber = $data['number'];
        $this->save();
        $numberHistory = new PhonebookHistories();
        $numberHistory->saveModel($this, $data, 0);
    }
    public function updateModel($data)
    {
        
        $this->phoneNumber = $data['number'];
        if(isset($data['numberconfirm']) && $data['numberconfirm']){
            $this->status = 1;
        }else{
            $this->status = 0;
        }
        $this->save();
        $numberHistory = new PhonebookHistories();
        $numberHistory->saveModel($this, $data,1);
    }
    public function deleteModel($data)
    {
        $numberHistory = new PhonebookHistories();
        $numberHistory->saveModel($this, $data,1);
        $this->delete();
    }
}
