<?php

namespace App\Models\Inbox;

use App\Helper\StandardAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboxMessageHistory extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function saveModel($model, $data, $status)
    {
        $this->inbox_message_id = $model->id;
        $this->newsPage = $model->newsPage; 
        $this->messageIcon =$model->messageIcon;
        $this->titleName = $model->titleName;
        $this->descriptionText = $model->descriptionText;
        $this->promocode_id = $model->promocode_id;
        $this->sentBy = $model->sentBy;
        $this->startDate = $model->startDate;
        $this->endDate = $model->endDate;
        $this->phonebook_id = $model->phonebook_id;
        $this->moderated = auth()->user()? auth()->user()->id : 1;
        $this->save();
        StandardAttributes::setSA('inbox_message_histories',$this->id,$status,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql7');
    }
}
