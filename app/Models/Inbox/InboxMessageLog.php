<?php

namespace App\Models\Inbox;

use App\Models\Promocode\Promocode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboxMessageLog extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($data){
        $this->user_id = auth()->user()->id;
        $this->inbox_message_id = $data['id'];
        $this->save();
    }
   
    
}
