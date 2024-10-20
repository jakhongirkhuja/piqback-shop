<?php

namespace App\Http\Controllers\Api\v1\TargetFilter;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Groups\Group;
use App\Models\Groups\TargetFilter;
use App\Models\Inbox\InboxMessage;
use App\Models\Phonebook\Phonebook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TargetFilterController extends Controller
{
    public function targetList()
    {
        $targetfilter_id = request()->targetfilter_id;
        $pagination = request()->paginate;
        if($targetfilter_id){
            $phonebook = TargetFilter::find($targetfilter_id);
            if($phonebook){
                return response()->json($phonebook, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('TargetFilter with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(TargetFilter::with('group')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function targetSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'=>'required|numeric',
            'ageRange'=>'required',
            'roleList'=>'required',
            'gender'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $groupCheck = Group::find($data['group_id']);
        if(!$groupCheck){
            return ErrorHelperResponse::returnError('Group with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $targetFilter = new TargetFilter();
                $targetFilter->saveModel($data, 'created');
                $responseArr['TargetFilter'] =$targetFilter;
                $responseArr['message'] = 'Target Filter has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function targetEditSubmit(Request $request, $targetfilter_id)
    {
        $validator = Validator::make($request->all(), [
            'group_id'=>'required|numeric',
            'ageRange'=>'required',
            'roleList'=>'required',
            'gender'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $groupCheck = Group::find($data['group_id']);
        if(!$groupCheck){
            return ErrorHelperResponse::returnError('Group with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $targetFilter = TargetFilter::find($targetfilter_id);
        if(!$targetFilter){
            return ErrorHelperResponse::returnError('Phonebook with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($targetFilter,$data){
               
                $targetFilter->saveModel($data, 'updated');
                $responseArr['targetFilter'] =$targetFilter;
                $responseArr['message'] = 'Phonebook has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function targetDelete(Request $request, $targetfilter_id)
    {
        $targetfilter = TargetFilter::find($targetfilter_id);
        if(!$targetfilter){
            return ErrorHelperResponse::returnError('TargetFilter with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        
        try {
            $res = DB::transaction(function () use ($targetfilter, $data){
                $targetfilter->deleteModel($data);
                $responseArr['message'] = 'deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
