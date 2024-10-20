<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Imports\BarcodeImport;
use App\Models\Money\IqcTransaction;
use App\Models\Store\DigitalStore;
use App\Models\Store\KorzinkaBarcode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class KarzinkaBarcodeController extends Controller
{
    public function karzinkaNotUsedBarcode(){
        $iqcTransaction = IqcTransaction::select('identityText')->where('serviceName','storeProductDigital')->distinct()->get()->toArray();
        // return response()->json($iqcTransaction);
        $empty = KorzinkaBarcode::whereNotIn('id', $iqcTransaction)->count();
        $arr['notUsedBarCode'] = $empty;
        return response()->json($arr);
    }
    public function karzinkaBarcodeList()
    {
        $category_id = request()->category_id;
        $pagination = request()->paginate;
        if($category_id){
            $category = KorzinkaBarcode::find($category_id);
            if($category){
                return response()->json($category, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Category with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(KorzinkaBarcode::latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function karzinkaBarcodeSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'store_id'=>'required',
            'type'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($data['type']==1){
            $validator2 = Validator::make($request->all(), [
                'excel'=>'required|max:50000|mimes:xlsx,ods,odt,odp',
            ]);
            if ($validator2->fails()) {
                return ErrorHelperResponse::returnError($validator2->errors(),Response::HTTP_BAD_REQUEST);
            }
        }else{
            $validator3 = Validator::make($request->all(), [
                'barcode'=>'required',
                'facevalue'=>'required',
            ]);
            if ($validator3->fails()) {
                return ErrorHelperResponse::returnError($validator3->errors(),Response::HTTP_BAD_REQUEST);
            }
            $korzinkabarcode = KorzinkaBarcode::where('barcode',$data['barcode'])->first();
            if($korzinkabarcode){
                return ErrorHelperResponse::returnError('Barcode  with given name exist',Response::HTTP_NOT_FOUND);
            }
        }
        $store = DigitalStore::find($data['store_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('Digital Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data,  $store, $request){
                if($data['type']==1){
                    Excel::import(new BarcodeImport($data,$store), $request->file('excel'));
                }else{
                    $korzinkabarcode = new KorzinkaBarcode();
                    $korzinkabarcode->saveModel($data,$store);
                    $responseArr['barCode'] =$korzinkabarcode;
                }
                
                $responseArr['message'] = 'Barcode has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function karzinkaBarcodeEditSubmit(Request $request, $karzinka_id)
    {
        $validator = Validator::make($request->all(), [
            
            'store_id'=>'required',
            'barcode'=>'required',
            'facevalue'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $korzinkabarcode = KorzinkaBarcode::find($karzinka_id);
        if(!$korzinkabarcode){
            return ErrorHelperResponse::returnError('Barcode  with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $korzinkabarcodeNameCheck = KorzinkaBarcode::where('id','!=', $data['barcode'])->where('barcode',$data['barcode'])->first();
        if($korzinkabarcodeNameCheck){
            return ErrorHelperResponse::returnError('Barcode  with given name exist',Response::HTTP_NOT_FOUND);
        }
        $store = DigitalStore::find($data['store_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('Digital Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($korzinkabarcode,$store, $data){
               
                $korzinkabarcode->saveModel($data,$store);
                $responseArr['barCode'] =$korzinkabarcode;
                $responseArr['message'] = 'BarCode has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function karzinkaBarcodeDelete($karzinka_id)
    {
        $korzinkaBarcode = KorzinkaBarcode::find($karzinka_id);
        if(!$korzinkaBarcode){
            return ErrorHelperResponse::returnError('Korzinka Barcode with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        if($korzinkaBarcode){
            try {
                $res = DB::transaction(function () use ($korzinkaBarcode){
                    $korzinkaBarcode->delete();
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
