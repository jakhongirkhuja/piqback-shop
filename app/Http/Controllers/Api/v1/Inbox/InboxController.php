<?php

namespace App\Http\Controllers\Api\v1\Inbox;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Groups\Group;
use App\Models\Inbox\InboxMessage;
use App\Models\Phonebook\Phonebook;
use App\Models\Promocode\Promocode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InboxController extends Controller
{
    public function inboxList()
    {
        $inbox_id = request()->inbox_id;
        $pagination = request()->paginate;
        if($inbox_id){
            $inbox = InboxMessage::find($inbox_id);
            if($inbox){
                return response()->json($inbox, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Inbox with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            if(request()->sms) return response()->json(tap(InboxMessage::where('sentBy','SMS to phone Number')->latest()->paginate($pagination? $pagination : 100))->map(
                function($inbox){
                    $group = Group::find($inbox->phonebook_id);
                    $groupName = '';
                    if($group){
                        $groupName= $group->groupName;
                    }
                    $inbox->targetName = $groupName;
                    return $inbox; 
              }
            ), Response::HTTP_OK);

            return response()->json(tap(InboxMessage::where('sentBy','!=','SMS to phone Number')->latest()->paginate($pagination? $pagination : 100))->map(
                function($inbox){
                    $group = Group::find($inbox->phonebook_id);
                    $groupName = '';
                    if($group){
                        $groupName= $group->groupName;
                    }
                    $inbox->targetName = $groupName;
                    return $inbox; 
              }
            ), Response::HTTP_OK);
        }
    }
    public function inboxSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'newsPage'=>'nullable',
            'titleName_ru'=>'required',
            'descriptionText_ru'=>'required',
            'titleName_uz'=>'required',
            'descriptionText_uz'=>'required',
            'promocode_id'=>'nullable|integer',
            'sentBy'=>'required|max:190',
           
            'startDate'=>'required|date_format:Y-m-d H:i:s',
            'endDate'=>'required|date_format:Y-m-d H:i:s',
            'phonebook_id'=>'required|integer'
        ]);

        
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        if($request->messageType=='Other'){
            $validator = Validator::make($request->all(), [
                'messageIcon'=>'required|mimes:png,jpg,jpeg,svg|max:2048',
            ]);
        }
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($data['promocode_id']){
            $promeCode = Promocode::find($data['promocode_id']);
            if(!$promeCode){
                return ErrorHelperResponse::returnError('PromoCode with given id is not exist',Response::HTTP_NOT_FOUND);
            }
        }
        
        $phoneBookList = Group::find($data['phonebook_id']);
        if(!$phoneBookList){
            return ErrorHelperResponse::returnError('Group with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $inboxlist = new InboxMessage();
                $inboxlist->saveModel($data,0);
                $responseArr['inboxMessage'] =$inboxlist;
                $responseArr['message'] = 'Inbox Message has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function inboxEditSubmit(Request $request, $inbox_id)
    {
        $validator = Validator::make($request->all(), [
            'newsPage'=>'nullable',
            'titleName_ru'=>'required',
            'messageIcon'=>'nullable|mimes:png,jpg,jpeg,svg|max:2048',
            'descriptionText_ru'=>'required',
            'titleName_uz'=>'required',
            
            'descriptionText_uz'=>'required',
            'promocode_id'=>'nullable|integer',
            'sentBy'=>'required|max:190',
            'startDate'=>'required|date_format:Y-m-d H:i:s',
            'endDate'=>'required|date_format:Y-m-d H:i:s',
            'phonebook_id'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data = $request->all();
        if($data['promocode_id']){
            $promeCode = Promocode::find($data['promocode_id']);
            if(!$promeCode){
                return ErrorHelperResponse::returnError('PromoCode with given id is not exist',Response::HTTP_NOT_FOUND);
            }
        }
        $phoneBookList = Group::find($data['phonebook_id']);
        if(!$phoneBookList){
            return ErrorHelperResponse::returnError('Group with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $inboxList = InboxMessage::find($inbox_id);
        if(!$inboxList){
            return ErrorHelperResponse::returnError('InboxMessage with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($inboxList,$data){
              
                $inboxList->updateModel($data,1);
                $responseArr['inboxMessage'] =$inboxList;
                $responseArr['message'] = 'Inbox Message has been updated';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function inboxDelete(Request $request, $inbox_id)
    {
        $inboxMessage = InboxMessage::find($inbox_id);
        $data = $request->all();
        if($inboxMessage){
            try {
                $res = DB::transaction(function () use ($inboxMessage, $data){
                    $inboxMessage->deleteModel($data,1);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return ErrorHelperResponse::returnError('Inbox Message  with given Id not found',Response::HTTP_NOT_FOUND);
    }
}
