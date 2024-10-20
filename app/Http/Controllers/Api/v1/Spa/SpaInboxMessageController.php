<?php

namespace App\Http\Controllers\Api\v1\Spa;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Groups\GroupMemberLists;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Groups\TargetFilter;
use App\Models\Inbox\InboxMessage;
use App\Models\Inbox\InboxMessageLog;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\Promocode\PromocodeLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpaInboxMessageController extends Controller
{
    public function getInboxMessage()
    {
        $inboxMessage = InboxMessage::with('promocode','inboxLog')
        ->where('sentBy','inbox message to LMS')
        ->where('endDate', '>=', \Carbon\Carbon::now('Asia/Tashkent')->subMinutes(10))
        ->where('startDate', '<=', \Carbon\Carbon::now('Asia/Tashkent'))
        ->orderby('endDate','asc')->take(220)->get()->map(function($query){
            $filters = TargetFilter::where('group_id',$query->phonebook_id)->first();
            $userinfo = auth()->user();
            $year = \Carbon\Carbon::parse($userinfo->birthDate)->age;
            $notSuit = false; 
            if($filters){
            
                if($filters->ageRange!='all'){
                    if(str_contains($filters->ageRange, ',')){
                        $minandmax = explode(",",$filters->ageRange);
                        if($minandmax[0]>=$year && $year>=$minandmax[1] ){
                            $notSuit = true;
                        }
                    }
                    if($filters->ageRange!=$year){
                    
                        $notSuit = true;
                    }
                }
                $roles = json_decode($filters->roleList);
                
                if(!in_array("all", $roles)){
                    $exist = false;
                    foreach ($roles as $key => $role) {
                        if($userinfo->role == $role ){
                            $exist = true;
                        }
                    }
                    if(!$exist){
                        $notSuit = true;
                    }
                }
                $gn = $filters->gender? 1 : 0;
                        $un = $userinfo->gender? 1 : 0;
                    
                 // if($filters->gender || !$filters->gender ){
                //         dd($gn, $un );
                //         if($gn != $un){
                //         $notSuit = true;
                //     }
                // }
            }  
            $restrictedUsers = MemberRestrictionList::where('group_id', $query->phonebook_id)->where('memberID',auth()->user()->id)->first();
            if($restrictedUsers){
                $notSuit = true;
            }
            $memberListExist = GroupMemberLists::where('group_id',$query->phonebook_id)->first();
            if($memberListExist){
                $groups = GroupMemberLists::where('group_id',$query->phonebook_id)->where('memberID', auth()->user()->id)->first();   
                if(!$groups){
                    $notSuit = true;
                }     
            } 
            if(!$notSuit){
                return $query;
            }
            
        });
        return response()->json($inboxMessage,200);
    }
    function postPromocodeInbox(Request $request){
        
        $validator = Validator::make($request->all(), [
            'promocode'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $promocode = \App\Models\Promocode\Promocode::where('id',$data['promocode'])
        ->where('endDate', '>=', \Carbon\Carbon::now('Asia/Tashkent'))
         ->where('startDate', '<=', \Carbon\Carbon::now('Asia/Tashkent'))->first();
        if(!$promocode){
            return ErrorHelperResponse::returnError('Promocode истёк',Response::HTTP_NOT_FOUND);
        }
        if($promocode->amountOfWinners===0){
            return ErrorHelperResponse::returnError('Promocode Исчерпан',Response::HTTP_NOT_FOUND);
        }
        
        if($promocode->amountOfWinners!==0 && $promocode->amountOfWinners!==null){
            $promoCodeLog = PromocodeLog::where('promocode_id', $promocode->id)->get();
            if($promocode->amountOfWinners<count($promoCodeLog)){
                return ErrorHelperResponse::returnError('Promocode Исчерпан',Response::HTTP_NOT_FOUND);
            }
        }
        $iqcTransaction = IqcTransaction::where('user_id', auth()->user()->id)->where('serviceName','promoCode')->where('identityText', $promocode->id)->first();
        if($iqcTransaction){
            return ErrorHelperResponse::returnError('Promocode был использован',Response::HTTP_NOT_FOUND);
        }
        $iqc = Iqc::where('user_id', auth()->user()->id)->first();
        if(!$iqc){
            $iqc = new Iqc();
        }
        try {
            $res =DB::transaction(function () use ($promocode,$iqc, $data){
            
                $iqc->updateStoreModel($data, $promocode->prizeAmount,1,'promoCode',  $promocode->id);
                $newPromoLog = new PromocodeLog();
                $newPromoLog->saveModel($promocode->id);
                $responseArr['message'] = 'Promocode успешно был использован';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function postInboxMessageLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $inboxMessage = InboxMessageLog::where('user_id', auth()->user()->id)->where('inbox_message_id',$data['id'])->first();
        if($inboxMessage){
            return ErrorHelperResponse::returnError('Exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $inboxMessageLog = new InboxMessageLog();
                $inboxMessageLog->saveModel($data);
                $responseArr['message'] = 'InboxMessage Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
