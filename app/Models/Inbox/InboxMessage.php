<?php

namespace App\Models\Inbox;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Promocode\Promocode;
class InboxMessage extends Model
{
    use HasFactory;
    protected $connection = 'pgsql7';
    public function promocode()
    {
        return $this->hasOne(Promocode::class, 'id', 'promocode_id');
    }
    public function inboxLog()
    {
        return $this->hasOne(InboxMessageLog::class, 'inbox_message_id')->where('user_id',auth()->user()->id);
    }
   
    public function saveModel($data, $status)
    {
        $this->newsPage = $data['newsPage'];
        if(isset($data['messageType']) && $data['messageType']=='Other' ){
            if(isset($data['messageIcon']) && $data['messageIcon']){
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['messageIcon']->getClientOriginalExtension();
                $data['messageIcon']->move(public_path('/files/inboxMessage'),$bannerName);
                $this->messageIcon = $bannerName;
            }
        }else{
            $this->messageIcon = 'sms';
        }
        
        $titlename['ru'] = $data['titleName_ru'];
        $titlename['uz'] = $data['titleName_uz'];
        $this->titleName = json_encode($titlename);
        $desc['ru'] = $data['descriptionText_ru'];
        $desc['uz'] = $data['descriptionText_uz'];
        $this->descriptionText = json_encode($desc);
        if(isset($data['promocode_id']) && $data['promocode_id']){
            $this->promocode_id = $data['promocode_id'];
        }
        if(isset($data['messageType']) && $data['messageType']){
            $this->messageType = $data['messageType'];
        }
        $this->sentBy = $data['sentBy'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->phonebook_id = $data['phonebook_id'];
        $this->save();
        $iqchistory = new InboxMessageHistory();
        $iqchistory->saveModel($this,$data,$status);
    }
    public function updateModel($data, $status)
    {
        $this->newsPage = $data['newsPage'];
        if(isset($data['messageType']) && $data['messageType']=='Other' ){
            if(isset($data['messageIcon']) && $data['messageIcon']){
                if(file_exists(public_path('/files/inboxMessage/'.$this->messageIcon))){
                    unlink(public_path('/files/inboxMessage/'.$this->messageIcon));
                }
                $bannerName = (string) Str::uuid().'-'.Str::random(15).'.'.$data['messageIcon']->getClientOriginalExtension();
                $data['messageIcon']->move(public_path('/files/inboxMessage'),$bannerName);
                $this->messageIcon = $bannerName;
            }
        }else{
            $this->messageIcon = 'sms';
        }
        
        $titlename['ru'] = $data['titleName_ru'];
        $titlename['uz'] = $data['titleName_uz'];
        $this->titleName = json_encode($titlename);
        $desc['ru'] = $data['descriptionText_ru'];
        $desc['uz'] = $data['descriptionText_uz'];
        $this->descriptionText = json_encode($desc);
        if(isset($data['promocode_id']) && $data['promocode_id']){
            $this->promocode_id = $data['promocode_id'];
        }
        if(isset($data['messageType']) && $data['messageType']){
            $this->messageType = $data['messageType'];
        }
        $this->sentBy = $data['sentBy'];
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
        $this->phonebook_id = $data['phonebook_id'];
        $this->save();
        $iqchistory = new InboxMessageHistory();
        $iqchistory->saveModel($this,$data,$status);
    }
    public function deleteModel($data,$status)
    {
        if(file_exists(public_path('/files/inboxMessage/'.$this->messageIcon))){
            unlink(public_path('/files/inboxMessage/'.$this->messageIcon));
        }
        $inboxmessage = new InboxMessageHistory();
        $inboxmessage->saveModel($this,$data,$status);
        $this->delete();
    }
}
