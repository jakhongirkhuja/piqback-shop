<?php

namespace App\Http\Controllers\Api\v1\Promocode;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Inbox\InboxMessage;
use App\Models\Promocode\Promocode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromocodeController extends Controller
{
    public function promocodeList()
    {
        $promo_id = request()->promo_id;
        $pagination = request()->paginate;
        if($promo_id){
            $promo = Promocode::find($promo_id);
            if($promo){
                return response()->json($promo, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('PromoCode with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(Promocode::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function promocodeSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promocode'=>'required',
            'prizeType'=>'required|bool',
            'prizeAmount'=>'required',
            'startDate'=>'required|date_format:Y-m-d H:i:s',
            'endDate'=>'required|date_format:Y-m-d H:i:s',
            'amountOfWinners'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $promocode = Promocode::where('promocode',$data['promocode'])->first();
        if($promocode){
            return ErrorHelperResponse::returnError('PromoCode must be unique',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $promoCode = new Promocode();
                $promoCode->saveModel($data, 'Created');
                $responseArr['promoCode'] =$promoCode;
                $responseArr['message'] = 'PromoCode has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function promocodeEditSubmit(Request $request, $promocode_id)
    {
        $validator = Validator::make($request->all(), [
            'promocode'=>'required',
            'prizeType'=>'required',
            'prizeAmount'=>'required',
            'startDate'=>'required|date_format:Y-m-d H:i:s',
            'endDate'=>'required|date_format:Y-m-d H:i:s',
            'amountOfWinners'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
       
        $data = $request->all();
        $promocode = Promocode::find($promocode_id);
        if(!$promocode){
            return ErrorHelperResponse::returnError('PromoCode with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($promocode, $data){
                $promocode->saveModel($data, 'Updated');
                $responseArr['promoCode'] =$promocode;
                $responseArr['message'] = 'PromoCode has been updated';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function promocodeDelete(Request $request, $promocode_id)
    {
        $promocode = Promocode::find($promocode_id);
        if(!$promocode){
            return ErrorHelperResponse::returnError('PromoCode with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $inboxList = InboxMessage::where('promocode_id', $promocode_id)->first();
        if($inboxList){
            return ErrorHelperResponse::returnError('PromoCode is used inside InboxMessage, update InboxMessage first',Response::HTTP_FOUND);
        }
        $data = $request->all();
        if($promocode){
            try {
                $res = DB::transaction(function () use ($promocode, $data){
                    $promocode->deleteModel($data);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
