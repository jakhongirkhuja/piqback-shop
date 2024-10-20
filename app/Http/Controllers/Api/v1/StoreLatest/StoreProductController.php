<?php

namespace App\Http\Controllers\Api\v1\StoreLatest;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\Phonebook;
use App\Models\Store\KorzinkaBarcode;
use App\Models\Store\KorzinkaBarcodeOrder;
use App\Models\StoreLatest\Category;
use App\Models\StoreLatest\Store;
use App\Models\StoreLatest\StoreProduct;
use App\Models\StoreLatest\StoreProductCode;
use App\Models\StoreLatest\StoreSeller;
use App\Models\StoreLatest\StoreSellerReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreProductController extends Controller
{
    public function sendNotifcationToBot($user_id,$itemName,$itemPrice, $orderType){
        $phoneBook = Phonebook::where('user_id', $user_id)->first();
        $phoneNumber = '';
        if($phoneBook){
            $phoneNumber =$phoneBook->phoneNumber;
        }
        $apiToken = "6926842942:AAE8V-0UUq0GBc1WcKX4QgyTtf0v4g-KpLM";

        $data = [
            'chat_id' => '-1001835764381',
            'text' => 'Store - был приобретен продукт🔄'.PHP_EOL.''.PHP_EOL.'Номер: '.$phoneNumber.''.PHP_EOL.'Название: '.$itemName.''.PHP_EOL.'Цена: '.$itemPrice.''.PHP_EOL.'Время: '.Carbon::now()->addHours(5)->format('Y-m-d H:i:s')
        ];

        if($orderType==4){
            $iqcTransaction = IqcTransaction::select('identityText')->where('serviceName','storeProductDigital')->distinct()->get()->toArray();
            $empty = KorzinkaBarcode::whereNotIn('id', $iqcTransaction)->count();
            $data = [
                'chat_id' => '-1001835764381',
                'text' =>'Store - был приобретен продукт🔄'.PHP_EOL.''.PHP_EOL.'Номер: '.$phoneNumber.''.PHP_EOL.'Название: '.$itemName.''.PHP_EOL.'Цена: '.$itemPrice.''.PHP_EOL.'Время: '.Carbon::now()->addHours(5)->format('Y-m-d H:i:s')
            ];
            $response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
            if($empty<=15 && $empty>=0){
                $data = [
                    'chat_id' => '-1001835764381',
                    'text' =>'⚠️ Предупреждение ⚠️'.PHP_EOL.''.PHP_EOL.'Осталось '.$empty.' ваучеров Корзинки'
                ];
                sleep(1);
                $response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
            }
        }else{
            $response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

        }
    }
    public function storeProductList()
    {
        
        $store_id = request()->product_id;
        $pagination = request()->paginate;
        
        if($store_id){
            $store = StoreProduct::find($store_id);
            if($store){
                return response()->json($store, Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('StoreProduct with given id not found',Response::HTTP_NOT_FOUND);
            }
        }else{
            return response()->json(StoreProduct::with('store.category')->latest()->paginate($pagination? $pagination : 100), Response::HTTP_OK);
        }
    }
    public function StoreProductSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|numeric',
            'store_id'=>'required|numeric',
            'productName_ru'=>'required',
            'productName_uz'=>'required',
            'productDescription_ru'=>'required',
            'productDescription_uz'=>'required',
            'productIMG'=>'required',
            'productIMG.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'productCost'=>'required|numeric',
            'productAmount'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Store::find($data['store_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Store not found',Response::HTTP_NOT_FOUND);
        }
        // $storeCategory = Category::find($data['category_id']);
        // if(!$storeCategory){
        //     return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        // }
        $store = StoreProduct::where('productName->ru', strtoupper($data['productName_ru']))->where('productName->uz', strtoupper($data['productName_uz']))->first();
        if($store){
            return ErrorHelperResponse::returnError('Store Product with given title name found',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $store = new StoreProduct();
                $store->saveModel($data, 'Created');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been created';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeProductEditSubmit(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required|numeric',
            'store_id'=>'required|numeric',
            'productName_ru'=>'required',
            'productName_uz'=>'required',
            'productDescription_ru'=>'required',
            'productDescription_uz'=>'required',
            'productIMG.*'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'productCost'=>'required|numeric',
            'productAmount'=>'required|numeric',
            'iqc'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $storeMagazin = Store::find($data['store_id']);
        if(!$storeMagazin){
            return ErrorHelperResponse::returnError('Store not found',Response::HTTP_NOT_FOUND);
        }
        // $storeCategory = Category::find($data['category_id']);
        // if(!$storeCategory){
        //     return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        // }
        $store = StoreProduct::find($product_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $storeCheckTitle = StoreProduct::where('id','!=',$product_id)->where(function ($query) use ($data) {
            $query->where('productName->ru', strtoupper($data['productName_ru']))
                  ->orwhere('productName->uz', strtoupper($data['productName_uz']));
        })->first();
        if($storeCheckTitle){
            return ErrorHelperResponse::returnError('Store with given title name is  exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($store,$data){
               
                $store->updateModel($data, 'updated');
                $responseArr['store'] =$store;
                $responseArr['message'] = 'Store has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeProductImageDelete(Request $request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'imageName'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $product = StoreProduct::find($product_id);
        if(!$product){
            return ErrorHelperResponse::returnError('Product not found',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($product,$data){
               
                $product->deleteImage($data);
                $responseArr['product'] =$product;
                $responseArr['message'] = 'Image has been removed';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function storeProductDelete(Request $request, $product_id)
    {
        $store = StoreProduct::find($product_id);
        if(!$store){
            return ErrorHelperResponse::returnError('Store with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        if($store){
            try {
                $res = DB::transaction(function () use ($store, $data){
                    $store->deleteModel($data,'deactivated');
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function buyProduct(Request $request)
    {
        $lang['ru']= 'Номер товара не найдено';
        $lang['uz']= 'Tovar id topilmadi';
        $validate['product_id']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'product_id'=>'required',
        ],
        [
            'product_id.required' => json_encode($validate['product_id']['required']),
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreProduct::find($data['product_id']);
        if(!$store){
            $lang['ru']= 'Товар не существует';
            $lang['uz']= "Tovar topilmadi";
            return response()->json($lang, Response::HTTP_NOT_FOUND);
        }
        $iqc =  Iqc::where('user_id', auth()->user()->id)->first();
        $productIQC =(int) $store->iqc;
        
        if($iqc){
            
            if($iqc->amountofIQC< $productIQC){
                $lang['ru']= 'У вас недостаточно IQC';
                $lang['uz']= "Sizda yetarlicha  IQC yo'q";
                
                return response()->json($lang, Response::HTTP_NOT_FOUND);
            }
        }else{
            
            $lang['ru']= 'У вас недостаточно IQC';
            $lang['uz']= "Sizda yetarlicha  IQC yo'q";
            return response()->json($lang, Response::HTTP_NOT_FOUND);
            
            // return ErrorHelperResponse::returnError('You have not IQC  or equel to 0 ',Response::HTTP_NOT_FOUND);
        }
        if($data['product_id']==4){
            $karzinkaBarCodeOrders = KorzinkaBarcodeOrder::select('barcode')->pluck('barcode');
            $korzinkaBarCode = KorzinkaBarcode::whereNotIn('barcode',$karzinkaBarCodeOrders)->first();
            if($korzinkaBarCode){
                try {
                    $res =DB::transaction(function () use ($iqc, $korzinkaBarCode, $data,$productIQC){
                        $korzinkaBarcodeOrderNew = new KorzinkaBarcodeOrder();
                        $korzinkaBarcodeOrderNew->saveModel($iqc, $korzinkaBarCode, $data, $productIQC);
                        $responseArr['productCode'] =$korzinkaBarCode;
                        $responseArr['message']['ru'] = 'Продукт обновлен';
                        $responseArr['message']['uz'] = 'Mahsulot yangilandi';
                        $this->sendNotifcationToBot(auth()->user()->id,$korzinkaBarCode->barcode,$productIQC, 4);
                        return response()->json($responseArr, Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                
            }else{
                $responseArr['ru'] = 'Количество ограниченно';
                $responseArr['uz'] = 'Mahsulot сheklangan';
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }

        }else{
            $countOrder = 0;
            $storeObejctCode= StoreProductCode::where('product_id', $data['product_id'])->orderby('productOrderCount','desc')->first();
            if($storeObejctCode){
                $countOrder = (int) $storeObejctCode->productOrderCount;
                $countOrder++;
            }else{
                $countOrder++;
            }
            if($store->productAmount==0){
                $lang['ru']= 'Продукт нет в магазине';
                $lang['uz']= "Tovar do`konda mavjud emas";            
                return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
            }
            try {
                $res =DB::transaction(function () use ($data, $countOrder,$iqc, $store){
                    $storeProductCode = new StoreProductCode();
                    $storeProductCode->saveModel($data,$countOrder,$iqc,$store);
                    $amount = $store->productAmount;
                    if($amount!=0){
                        $amount--;
                        $store->productAmount = $amount;
                        $store->save();
                    }
                    $responseArr['productCode'] =$storeProductCode;
                    $responseArr['store']=Store::with('owner.seller')->find($store->store_id);
                    $responseArr['message']['ru'] = 'Продукт обновлен';
                    $responseArr['message']['uz'] = 'Mahsulot yangilandi';
                    $this->sendNotifcationToBot(auth()->user()->id,json_decode($store->productName, true)['ru'] ,$amount,1);
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        // $iqc = new Iqc();
        // $iqc->saveModel($data, auth()->user()->id,$changedPrice,1,'StoreProduct', $store->id);
    }
    public function buyProductShow()
    {
        $orders = StoreProductCode::where('product_id',request()->product_id)->latest()->get();
        
        return response()->json($orders, Response::HTTP_OK);
    }
    public function storeSellerReports(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id'=>'required',
            'seller_id'=>'required',
            'action'=>'required|boolean',
            'reportIMG'=>'nullable|image|mimes:jpg,png,jpeg,svg|max:2048',
            'shortDescription'=>'nullable',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $store = StoreProduct::find($data['product_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('StoreProduct with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        $store = StoreSeller::find($data['seller_id']);
        if(!$store){
            return ErrorHelperResponse::returnError('StoreSeller with given id is not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($data){
                $storeSellerReport = new StoreSellerReport();
                $storeSellerReport->saveModel($data, false);
                $responseArr['store'] =$storeSellerReport;
                $responseArr['message'] = 'Store Seller Report has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function storeProductsCode($id)
    {
        $codeProduct = StoreProductCode::with('product.store')->where('id',$id)->where('user_id', auth()->user()->id)->first();
        $user = null;
        
        if($codeProduct && $codeProduct->product && $codeProduct->product->store){
           
            $user = User::with('phonebook')->find($codeProduct->product->store->storeOwner);
        }
        $code['code']= $codeProduct;
        $code['owner'] = $user; 
        return response()->json($code, Response::HTTP_OK);
    }
    public function storeProductsCodeKorzinka($id){
        
        $code['code']= KorzinkaBarcodeOrder::with('store')->find($id);
        return response()->json($code, Response::HTTP_OK);
    }
}
