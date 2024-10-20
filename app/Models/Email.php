<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    public function saveModel($user_id, $data)
    {
        $this->user_id = $user_id;
        $this->email = $data['email'];
        $this->save();
        $emailHistories = new EmailHistories();
        $emailHistories->saveModel($this, $data);
    }
    public function updateModel($data)
    {
        $this->email = $data['email'];
        $this->save();
        $emailHistories = new EmailHistories();
        $emailHistories->updateModel($this, $data);
    }
    public function deleteModel($data){
        $emailHistories = new EmailHistories();
        $emailHistories->updateModel($this, $data);
        $this->delete();
    }
}
