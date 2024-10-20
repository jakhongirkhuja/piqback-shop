<?php

namespace App\Console\Commands;

use App\Models\General;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Inbox\InboxMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled sms send';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function getToken()
    {
        try {
            $res = DB::transaction(function(){
                
                
                 $json  =  [
                    'email'=>'Maylantim@gmail.com',
                    'password'=>'5Juzs7eDSoHl876ScDbe42kafZhie36ej6WjvVV3',

                    ];
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        // 'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
                    ])->post('https://notify.eskiz.uz/api/auth/login',$json);
                    if($response->ok()){
                        $general = General::where('name','eskiz')->first();
                        if($general){
                            $general->value = $response['data']['token'];
                            $general->save();
                            return $general->value;
                        }else{
                            $general = new General();
                            $general->name='eskiz';
                            $general->value = $response['data']['token'];
                            $general->save();
                            return $general->value;
                        }
                    }
                    return false;
            });
            return $res;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function handle()
    {
        $tables = InboxMessage::
         where('used',0)
         ->where('sentBy','SMS to phone Number')
         ->where('endDate', '>=', \Carbon\Carbon::now('Asia/Tashkent')->subMinutes(10))
         ->where('startDate', '<=', \Carbon\Carbon::now('Asia/Tashkent'))
         ->get();
         
         $users = [];
         $allusers = false;
         if(count($tables)>0){
             foreach($tables as $table ){
                 $text= isset(json_decode($table->descriptionText,true)['uz'])? json_decode($table->descriptionText,true)['uz'] : '';
                 $users = [] ;
                 $restrictedUsers = MemberRestrictionList::where('group_id', $table->phonebook_id)->get()->pluck('memberID')->toArray();
                 $groups = \App\Models\Groups\GroupMemberLists::where('group_id',$table->phonebook_id)->whereNotIn('memberID', $restrictedUsers)->get();
                 $smssendss['groups'] = $groups;
                 Log::info(json_encode($smssendss));
                 if(count($groups)>0){
                     foreach($groups as $group){
                        $phoneNumber = \App\Models\Phonebook::where('user_id',$group->memberID)->first();
                        if($phoneNumber){
                            $arr['to']=$phoneNumber->phoneNumber;
                            $arr['user_sms_id']= $phoneNumber->user_id;
                            $arr['text']= $text;    
                            $users[]=$arr;
                        }
                    }
                 }else{
                     $allusers = true;
                     
                     // send all users
                 }
                 $smssendss44['allusers63'] = $allusers;
                 Log::info(json_encode($smssendss44));
                 if($allusers){
                    if($table->phonebook_id==31){
                        // $users= \App\Models\Phonebook::select('phoneNumber as to','id as user_sms_id', DB::raw(`$text as text`))->where('status', 1)->where(function($query){
                        //     $query->where('phoneNumber', 'LIKE', '99895%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99899%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99877%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99890%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99891%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99893%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99894%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99833%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99897%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99888%')
                        //           ->orWhere('phoneNumber', 'LIKE', '99898%');
                        // })->get()->toArray();
                        $restrictedUsersAll = MemberRestrictionList::where('group_id', 31)->get()->pluck('memberID')->toArray();
                        $usersalls =\App\Models\Phonebook::select('phoneNumber','user_id')->whereNotIn('user_id', $restrictedUsersAll)->where('status', 1)->where(function($query){
                            $query->where('phoneNumber', 'LIKE', '99895%')
                                  ->orWhere('phoneNumber', 'LIKE', '99899%')
                                  ->orWhere('phoneNumber', 'LIKE', '99877%')
                                  ->orWhere('phoneNumber', 'LIKE', '99890%')
                                  ->orWhere('phoneNumber', 'LIKE', '99891%')
                                  ->orWhere('phoneNumber', 'LIKE', '99893%')
                                  ->orWhere('phoneNumber', 'LIKE', '99894%')
                                  ->orWhere('phoneNumber', 'LIKE', '99833%')
                                  ->orWhere('phoneNumber', 'LIKE', '99897%')
                                  ->orWhere('phoneNumber', 'LIKE', '99888%')
                                  ->orWhere('phoneNumber', 'LIKE', '99898%');
                        })->get();
                        foreach ($usersalls as $key => $usersall) {
                            $arr['to']=$usersall->phoneNumber;
                            $arr['user_sms_id']= $usersall->user_id;
                            $arr['text']= $text;    
                            $users[]=$arr;
                        }
                    }
                    
                            $smssendss44['all'] = 'all users';
                            Log::info(json_encode($smssendss44));
                 }
                 $smssendss44['allusers82'] = $users;
                 Log::info(json_encode($smssendss44));
                 if(count($users)>0){
                    $chunked = array_chunk($users,180);
                    foreach($chunked as $k=>$chunk){
              
            
                        $smssend['messages'] = $chunk;
                        $smssend['from'] = '4546';
                        $smssend['dispatch_id'] = '120';
                       
            
                        Log::info(json_encode($smssend));
                        $general = \App\Models\General::where('name','eskiz')->first();
                        if($general){
                            $token =  $general->value;
                        }else{
                            
                            $token = $this->getToken();
                        }
                        $response = Http::withHeaders([
                                    'Accept'=>'application/json',
                            'Content-Type'=>'application/json',
                            'Authorization'=>'Bearer '.$token,
                        ])->post('https://notify.eskiz.uz/api/message/sms/send-batch',$smssend);
                        if(!$response->ok()){
                            $logresponse = [
                                'response' => $response->body()
                            ];
                            Log::info(json_encode($logresponse));
                        }
                    }
                    $table->used = 1;
                    $table->save();
                 }else{
                    $log = [
                        'table' => 'empty'
                    ];
                    Log::info(json_encode($log));
                 }
                 
                 
             }
             

             
         }else{
            $log = [
                'table' => $tables.'114'
            ];

            Log::info(json_encode($log));
         }
    }
}
