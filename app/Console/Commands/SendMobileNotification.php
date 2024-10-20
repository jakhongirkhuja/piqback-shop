<?php

namespace App\Console\Commands;

use App\Models\Groups\TargetFilter;
use App\Models\Inbox\InboxMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMobileNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled notification send';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tables = InboxMessage::
         where('used',0)
         ->where('sentBy','Mobile notification')
         ->where('endDate', '>=', \Carbon\Carbon::now('Asia/Tashkent')->subMinutes(10))
         ->where('startDate', '<=', \Carbon\Carbon::now('Asia/Tashkent'))
         ->get();
         if(count($tables)>0){
            foreach($tables as $table ){
                $getusers = \App\Models\Groups\GroupMemberLists::where('group_id',$table->phonebook_id)->get()->pluck('memberID');
                $users = '';
                $type ='all';
                $age = '90';
                if(count($getusers)>0){
                    foreach ($getusers as $key => $getuser) {
                        $user = User::find($getuser);
                        if($user){
                            $users .=','.$user->hrid;
                        }
                        $type = 'custom';
                        $gender = 'all';
                        $roles = "all";
                    }
                }else{
                    $filters = TargetFilter::where('group_id',$table->phonebook_id)->first();
                    $roles = implode(',',json_decode($filters->roleList));
                    if($filters->gender==null && in_array('all', json_decode($filters->roleList, true), true)){
                        $gender = 'all';
                        $roles = 'all';
                        $type = "all";
                    }else{
                        if($filters->gender){
                            $gender = "1";
                        }else{
                            $gender = "0";
                        }
                    }
                }
                $data = [
                    "to" =>"/topics/all",
                    "data"=>[
                        "type"=>$type,
                        "role"=>$roles,
                        "gender"=>$gender,
                        "age"=>$age,
                        "title_ru"=>json_decode($table->titleName)->ru,
                        "title_uz"=>json_decode($table->titleName)->uz,
                        "body_ru"=>json_decode($table->descriptionText)->ru,
                        "body_uz"=>json_decode($table->descriptionText)->uz,
                        "users"=>$users,
                        "course"=>"1234",
                    ]
                ];

                $response = Http::withToken('AAAAdIwC2Bk:APA91bE0CwdTZ5QI85HHRGEhFuKjIUYMhJfLTgbv1dXuF-VkSyYDNKRE0Fif7rlaoinSfaiiani322stENPQsSuSEeJ7s_8qYLLgCsiDHQTqvXFmcLmg5wlYrW4xp6O131iuIJ1t4Oz1')
                ->post('https://fcm.googleapis.com/fcm/send', $data);
                if($response->successful()){
                    $table->used = 1;
                    $table->save();
                   
                    Log::info(json_encode($response->json()));
                }else{
                   
                    Log::info(json_encode($response->body()));
                }
                sleep(2);
            }
         }else{
            Log::info('Empty');
         }
    }
}
