<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyAddressHistories;
use App\Models\CompanyHistories;
use App\Models\CompanyMemberHistories;
use App\Models\CompanyMembers;
use App\Models\Email;
use App\Models\EmailHistories;
use App\Models\General;
use App\Models\Groups\GroupMemberListHistories;
use App\Models\Groups\GroupMemberLists;
use App\Models\PasswdHistories;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\Quarter;
use App\Models\Region;
use App\Models\Scout;
use App\Models\ScoutedList;
use App\Models\User;
use App\Models\UserBioHistoires;
use App\Models\UserLangHistories;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function getAccess($user)
    {
        $hasAccess = '';
        switch ($user->role) {
            case 'SuperAdmin':
                $hasAccess = 'board,academy';
                break;
            case 'Company Owner':
                $hasAccess = 'academy';
                break;
            case 'Employee':
                $hasAccess = 'academy';
                break;
            case 'Scout':
                $hasAccess = 'academy';
                break;
            case 'Creator':
                $hasAccess = 'board';
                break;
            case 'Owner':
                $hasAccess = 'board';
                break;
            case 'Analytic':
                $hasAccess = 'board';
                break;
            case 'Zero':
                $hasAccess = 'board';
                break;
            default:
                $hasAccess = 'academy';
                break;
        }
        return $hasAccess;
    }
    public function logout(Request $request)
    {
       
        auth()->user()->tokens()->delete();
        $responseArr['message'] = 'Success';
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function login(Request $request)
    {
        $fields = $request->validate([
            'phoneNumber'=>'required|numeric',
            'password'=>'required|string',
        ]);
        $phonebook=  Phonebook::where('phoneNumber',$fields['phoneNumber'])->first();
       
        if(!$phonebook){
            return ErrorHelperResponse::returnError('phoneNumber not found',Response::HTTP_NOT_FOUND);
        }
        $password=  Password::where('user_id',$phonebook->user_id)->first();
        if(!$password || !Hash::check($fields['password'],$password->passwd)){
            return ErrorHelperResponse::returnError('Password not same',Response::HTTP_NOT_FOUND);
        }
       
        $user = User::select('id','hrid as user_id', 'firstName', 'lastName','birthDate', 'gender','language','role')->where('id',$phonebook->user_id)->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given user id not found',Response::HTTP_NOT_FOUND);
        }
        
        $token = $user->createToken('myapptoken')->plainTextToken;
        
        
        $response =[ 'user'=>$user, 'token'=>$token,'hasAccess'=>$this->getAccess($user) ];
        return response($response,201);
    }
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $email = Email::where('email',$request['email'])->first();
        if(!$email){
            return ErrorHelperResponse::returnError('Email not found',Response::HTTP_NOT_FOUND);
        }
        $responseArr['message'] = 'Email exists in database';
        return response()->json($responseArr, Response::HTTP_FOUND);
    }
    public function checkNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number'=>'required|size:12',    
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $number = Phonebook::where('phoneNumber',$request['number'])->where('status',1)->first();
        if(!$number){
            return ErrorHelperResponse::returnError('Number not found',Response::HTTP_BAD_REQUEST);
        }
        $responseArr['message'] = 'Number exists in database, please login';
        return response()->json($responseArr, Response::HTTP_FOUND);
        
    }
    public function registerUser(Request $request)
    {
        $responseArr['message'] = '404 NOT FOUND';
        return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        // $validator = Validator::make($request->all(), [
        //     'number'=>'required|size:12',
        //     'firstName'=>'required|max:190',
        //     'lastName'=>'required|max:190',
        //     'gender'=>'required|min:1|max:2',
        //     'birthDate'=>'required|date_format:Y-m-d',    
        // ]);
        // if ($validator->fails()) {
        //     $responseArr['error']=true;
        //     $responseArr['message'] = $validator->errors();
        //     return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        // }
        // $data = $request->all();
        // if(isset($data['user_id'])){
        //     $user = User::where('hrid',$data['user_id'])->first();
        //     if(!$user){
        //         $responseArr['message'] = 'User with given id not found';
        //         return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        //     }
        // }else{
            
        // }
        // $number = Phonebook::where('phoneNumber',$data['number'])->first();
        // if($number){
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'Number exists in database, please login';
        //     return response()->json($responseArr, Response::HTTP_FOUND);
        // }
        
        // $user = new  User();
        // $user->saveModelRegister($data);
        // if($user){
        //     $userlanghistory = new UserLangHistories();
        //     $userlanghistory->user_id = $user->id;
        //     $userlanghistory->lang= 'ru';
        //     if($userlanghistory->save()){
        //         StandardAttributes::setSA('user_lang_histories',$userlanghistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
        //     }
        //     $number = new Phonebook();
        //     $number->user_id = $user->id;
        //     $number->phoneNumber = $request['number'];
        //     if($number->save()){
        //         $responseArr['user_id'] = $user->hrid;
        //         $responseArr['message'] = 'Success';
        //         return response()->json($responseArr, Response::HTTP_CREATED);
        //     }else{
        //         $responseArr['error']=true;
        //         $responseArr['message'] = 'Number not saved ';
        //         return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        //     }
        // }else{
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'User not saved';
        //     return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
    }
    public function roles(Request $request)
    {
        $response= ['Company Owner','Employee','Project','Creators','SuperAdmin','Tester','Scout'];
        return response()->json($response, Response::HTTP_OK);
    }
    public function generateNameAndCheck($stringchanged)
    {
        
        $string= $stringchanged.' №1';
        $inc = 1; 
        $sorted = [];
        $while = true;
        do {
           
            $company = Company::where('companyName',$string)->first();
            if($company){
                $string = $stringchanged.' №'.$inc;
            }else{
                $string = $stringchanged.' №'.$inc;
                $sorted[]=$string;
                if(count($sorted)>=3){
                    $while = false;
                }
            }
            $inc ++;
        } while ($while);
        return $sorted;
    }
    public function companyName(Request $request)
    {
        $validator = Validator::make($request->all(), [
                'name'=>'required',
        ]);
       if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $company = Company::where('companyName', $request->name)->first();
        if($company){
            $responseArr['message'] = 'Company with given name exist, please choose another one';
            $responseArr['examples'][]= $this->generateNameAndCheck($request->name);
            return response()->json($responseArr, Response::HTTP_FOUND);
        }else{
            $responseArr['message'] = 'Company with given name not found';
            return response()->json($responseArr, Response::HTTP_OK);
        }
        dd($company);
        // $validator = Validator::make($request->all(), [
        //     'name'=>'required|max:190|unique:pgsql2.companies,companyName',
        //     'user_id'=>'required|numeric'
        // ]);
        // if ($validator->fails()) {
        //     $responseArr['error']=true;
        //     $responseArr['message'] = $validator->errors();
        //     return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        // }
        // $data = $request->all();
        // $user= User::where('hrid',$data['user_id'])->first();
        
        // if($user){
        //     $company = new Company();
        //     $company->companyName=  $data['name'];
        //     $company->user_id=  $user->id;
        //     if($company->save()){
        //         $companyHistory = new CompanyHistories();
        //         $companyHistory->user_id = $user->id;
        //         $companyHistory->companyName = $data['name'];
        //         $companyHistory->moderated=  $user->id;
        //         if($companyHistory->save()){
                    
        //             StandardAttributes::setSA('company_histories',$companyHistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
        //         }
        //         $responseArr['company_id']=$company->id;
        //         $responseArr['message'] = 'Success';
        //         return response()->json($responseArr, Response::HTTP_CREATED);
        //     }else{
        //         $responseArr['error']=true;
        //         $responseArr['message'] = 'Company not saved ';
        //         return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        //     }
        // }else{
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'User with user_id not found ';
        //     return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        // }
        
    }
    public function getAdress($lat, $long)
    {
        // dd($lat, $long);
        $getInfoYandex = Http::get('https://geocode-maps.yandex.ru/1.x/?apikey=97cc3b95-6b0c-4891-9481-5b375bbf00fc&geocode='.(float)$long.','.(float)$lat.'&format=json&lang=ru-RU&results=5');
        if($getInfoYandex->ok()){
            $yandexbody = $getInfoYandex->json()['response']['GeoObjectCollection']['featureMember'];
            $dataAdress = [];
            $goinside = true;
            foreach ($yandexbody as  $eachbody) {
                $components = $eachbody['GeoObject']['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
                foreach ($components as $component) {
                    switch ($component['kind']) {
                        case 'province':
                            if($goinside) $dataAdress['city'] = $component['name']; $goinside=false;
                            break;
                        case 'district':
                            $dataAdress['region'] = $component['name'];
                            break;
                        case 'area'://if not tashkent area will exist 
                            $dataAdress['area'] = $component['name'];
                            break;
                        case 'street':
                            $dataAdress['street'] = $component['name'];
                            break;
                        case 'house':
                            $dataAdress['house'] = $component['name'];
                            break;
                        case 'other':
                            $dataAdress['other'] = $component['name'];
                            break;    
                        default:
                            # code...
                            break;
                    }
                }
            }
            
            if(isset($dataAdress['city']) && (isset($dataAdress['region']) || isset($dataAdress['area']) )){
                $city = City::where('name_ru',$dataAdress['city'] )->first();
                if(!$city){
                    $city  = new City();
                    $city->country_id =  1;
                    $city->name_uz =  '';
                    $city->name_ru =  $dataAdress['city'];
                    $city->save(); 
                }
                $datset['id'] = $city->id;
                $datset['name'] = $city->name_ru;
    
                $dataAdress['city']=$datset;
                
                $region = Region::where('name_ru',isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'] )->first();
               
                if(!$region){
                    $regionGetIfnotFound = isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'];
                    $intoArray= explode(" ",$regionGetIfnotFound);
                    $inside = false;
                    foreach ($intoArray as $key => $value) {
                        $regionCheck = Region::where('name_ru','like','%'.$value.'%')->first();
                        if($regionCheck && !$inside){
                            $inside = true;
                            $region = $regionCheck;
                        }
                    }
                    if($city->id==14 && !$inside){
                        switch ($regionGetIfnotFound) {
                            case 'Алмазорский район':
                                $region = Region::find(187);
                                $inside = true;
                                break;
                            case 'Алмазарский район':
                                $region = Region::find(187);
                                $inside = true;
                                break;
                            case 'Олмазорский район':
                                $region = Region::find(187);
                                $inside = true;
                                break;
                            case 'массив Лабзак':
                                $region = Region::find(192);
                                $inside = true;
                                break;    
                            case 'массив Буюк Ипак Йули':
                                $region = Region::find(185);
                                $inside = true;
                                break;
                            case 'массив Чиланзор':
                                $region = Region::find(191);
                                $inside = true;
                                break;
                            case 'массив Йулдош':
                                $region = Region::find(203);
                                $inside = true;
                                break;
                            case 'массив Юнусабад':
                                $region = Region::find(193);
                                $inside = true;
                                break;
                            case 'массив Каракамыш':
                                $region = Region::find(187);
                                $inside = true;
                                break;
                            case 'квартал Г9А':
                                $region = Region::find(191);
                                $inside = true;
                                break;             
                            default:
                                $region = Region::find(191);
                                $inside = true;
                                break;
                        }
                    }
                    if(!$inside){
                        $region = Region::where('city_id', $city->id)->first();
                        if(!$region){
                            $region  = new Region();
                            $region->city_id = $city->id;
                            $region->name_uz =  '';
                            $region->name_ru = isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'];
                            $region->save();
                        }
                        $inside= true;
                    }
                    if(!$inside){
                        
                        $region  = new Region();
                        $region->city_id = $city->id;
                        $region->name_uz =  '';
                        $region->name_ru = isset($dataAdress['region'])?$dataAdress['region'] : $dataAdress['area'];
                        $region->save();
                    }
                    
                }
                $datset['id'] = $region->id;
                $datset['name'] = $region->name_ru;
    
                $dataAdress['region']=$datset;
                
            } 
        }else{
            $dataAdress=false;
        }
        
       return  $dataAdress;
    }
    public function getAdressByCoor()
    {
        $lat = request()->lat;
        $long = request()->long;
        if(!$lat || !$long ){
            $responseArr['error']=true;
            $responseArr['message'] = 'longitude or latitude not given';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $res= $this->getAdress($lat, $long);
        return response()->json($res, Response::HTTP_OK);
    }
    public function companyAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|numeric|unique:pgsql2.company_addresses,company_id',
            'addressType'=>'required|numeric|min:0|max:1'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company= Company::find($data['company_id']);
       
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company with company_id not found ';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($data['addressType']==0){
            $validator = Validator::make($request->all(), [
                'longitude'=>'required',
                'latitude'=>'required'
            ]);
            if ($validator->fails()) {
                $responseArr['error']=true;
                $responseArr['message'] = $validator->errors();
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }
           
            try {
                $res = DB::transaction(function() use ($data){
                    $companyAdd= new CompanyAddress();
                    $companyAdd->saveModelCoor($data);
                    $responseArr['company_id']=$companyAdd->company_id;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            $validator = Validator::make($request->all(), [
                'city_id'=>'required|numeric|min:1',
                'region_id'=>'required|numeric|min:1',
                'street'=>'required',
                'house'=>'required',
            ]);
            if ($validator->fails()) {
                $responseArr['error']=true;
                $responseArr['message'] = $validator->errors();
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }
            try {
                $res = DB::transaction(function() use ($company, $data){
                    $companyAddress = new CompanyAddress();
                    $companyAddress->saveModelByIds($company->id, $data);
                    $responseArr['company_id']=$company->id;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function getToken()
    {
        try {
            $res = DB::transaction(function(){
                
                
                 $json  =  [
                    'email'=>env('ESKIZ_EMAIL'),
                    'password'=>env('ESKIZ_PASSWORD'),
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
    public function setPasswordRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|size:12',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $number = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$number){
            $responseArr['error']=true;
            $responseArr['message'] = 'Number not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $user = User::find($number->user_id);
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User connected to number not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $number, $user){
                
                $number->status = 0;
                $number->random = mt_rand(100000, 1000000);
                $number->randomTime = Carbon::now();
                $number->save();
                $json  =  [
                    'mobile_phone'=>$number->phoneNumber,
                    'message'=>"PharmIQ Confirmation code: ".$number->random,
                    'from'=>4546,
                    'callback_url'=>'http://api.895773-cx81958.tmweb.ru/api/v1/phoneNumberStatus'
                ];
                $general = General::where('name','eskiz')->first();
                if($general){
                    $token =  $general->value;
                }else{
                    $token = $this->getToken();
                }
                if($token){
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                    if($response->ok()){
                        // $lang['ru']= 'СМС отправлено';
                        // $lang['uz']= 'SMS yuborildi';
                        // $validate['message'] =$lang;
                        // return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                    }
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                                'Authorization'=>'Bearer '.$token,
                            ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                            if($response->ok()){
                                // $lang['ru']= 'СМС отправлено';
                                // $lang['uz']= 'SMS yuborildi';
                                // $validate['message'] =$lang;
                                // return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                            }
                        }
                        
                    }
                       
                }else{

                    $json  =  [
                        'messages'=>[
                            [
                                'recipient'=>$number->phoneNumber,
                                'message-id'=>'abc000000001',
                                'sms'=>[
                                    'originator'=>'3700',
                                    'content'=>[
                                        'text'=>"PharmIQ Confirmation code: ".$number->random
                                    ]
                                ]
                            ]
                        ]
                        ];
                        Http::withHeaders([
                            'Accept'=>'application/json',
                            'Content-Type'=>'application/json',
                           
                        ])->post('http://91.204.239.44/broker-api/send',$json);
                }
                // number check
                    $responseArr['user_id']=$user->hrid;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function setPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|size:12',
            'password'=>'required|confirmed|min:6',
        ]);
        // it must return with token
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        $number = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$number){
            $responseArr['error']=true;
            $responseArr['message'] = 'Number not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
       
        if($number->status==0){
            $responseArr['error']=true;
            $responseArr['message'] = 'Number not confirmed';
            return response()->json($responseArr, Response::HTTP_FORBIDDEN);
        }
       
        $user = User::select('id','hrid as user_id', 'firstName', 'lastName','birthDate', 'gender','language','role')->where('id',$number->user_id)->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given user id not found',Response::HTTP_NOT_FOUND);
        }
        try {
            $res  = DB::transaction(function() use ($data, $user){
                
                $password = Password::where('user_id', $user->id)->first();
                if($password){
                    $password->updateModel($data);
                }else{
                    $password = new Password();
                    $password->saveModel($user->id, $data);
                }
                
                $token = $user->createToken('myapptoken')->plainTextToken;
                $response =[ 'user'=>$user, 'token'=>$token, ];
               
                return response()->json($response, Response::HTTP_CREATED);
            });

            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        // $data = $request->all();
        
        // $user = User::where('hrid',$data['user_id'])->first();
        // if(!$user){
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'User with user_id not found ';
        //     return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        // }
        // $password = Password::where('user_id', $user->id)->first();
        // if($password){
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'User has already been used, please change user_id';
        //     return response()->json($responseArr, Response::HTTP_FOUND);
        // }
        // $password = new Password();
        // $password->user_id = $user->id;
        // $password->passwd = Hash::make($data['passwd']);
        
        // if($password->save()){
            
        //     $passwdHistories = new PasswdHistories();
        //     $passwdHistories->user_id = $user->id;
        //     $passwdHistories->passwd = $password->passwd;
        //     if($passwdHistories->save()){
        //         // dd($companyHistory);
        //         StandardAttributes::setSA('passwd_histories',$passwdHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
        //     }
        //     $group_id = Scout::where('scout_id', $user->id)->first();
        //     if(($data['role']=='Company Owner' || $data['role']=='Employee') &&  isset($data['scout_id'])  && $group_id){
        //         // $scoutList = new ScoutedList();
        //         // $scoutList->saveModel($group_id->group_id, $data);
        //         $groupMembers = new GroupMemberLists();
        //         $groupMembers->group_id = $group_id->group_id;
        //         $groupMembers->memberID = $user->id;
        //         if($groupMembers->save()){
        //             $groupMembersHistory = new GroupMemberListHistories();
        //             $groupMembersHistory->group_id = $group_id->group_id;
        //             $groupMembersHistory->memberID = $user->id;
        //             $groupMembersHistory->moderated = auth()->user()? auth()->user()->id : 1;
        //             if($groupMembersHistory->save()){
        //                 StandardAttributes::setSA('group_member_list_histories',$groupMembersHistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');     
                        
        //             }
        //         }
        //     }
        //     $responseArr['user_id']=$data['user_id'];
        //     $responseArr['message'] = 'Success';
        //     return response()->json($responseArr, Response::HTTP_CREATED);
        // }else{
        //     $responseArr['error']=true;
        //     $responseArr['message'] = 'Password  not saved ';
        //     return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
    }
    public function setEmail(Request $request)
    {
        $responseArr['message'] = '404 not found';
        return response()->json($responseArr, Response::HTTP_FOUND);

        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'email'=>'required|email|unique:emails'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['user_id'])->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with user_id not found ';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $email = new Email();
        $email->user_id = $user->id;
        $email->email = $data['email'];
        if($email->save()){
            $emailHistories = new EmailHistories();
            $emailHistories->user_id = $user->id;
            $emailHistories->email = $data['email'];
            if($emailHistories->save()){
                
                StandardAttributes::setSA('email_histories',$emailHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
            }
            $responseArr['user_id']=$data['user_id'];
            $responseArr['message'] = 'Success';
            return response()->json($responseArr, Response::HTTP_CREATED);
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Email  not saved ';
            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function showCities(Request $request)
    {
        // $headers =request()->ip();
        // dd($headers);
        // dd(City::all());
        $ok = City::select('id','name_uz','name_ru')->get();
       return response()->json($ok);
    }
    public function showRegions($city_id)
    {
        $ok = Region::select('id','name_uz','name_ru')->where('city_id', $city_id)->get();
       return response()->json($ok);
    }
    public function showQuarters($region_id)
    {
       return response()->json(Quarter::select('id','name')->where('regions_id', $region_id)->get());
    }
    public function confirmNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('hrid', $request->user_id)->first();
       
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $phonebook = Phonebook::where('user_id', $user->id)->first();
        if(!$phonebook){
            $responseArr['error']=true;
            $responseArr['message'] = 'Phone number with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($phonebook->status==1){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with given user_id is confirmed';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $phonebook->random = mt_rand(100000, 1000000);
        $phonebook->randomTime = Carbon::now();
        if($phonebook->save()){
            try {
                $json  =  [
                    'mobile_phone'=>$phonebook->phoneNumber,
                    'message'=>"PharmIQ Confirmation code: ".$phonebook->random,
                    'from'=>4546,
                    'callback_url'=>'http://api.895773-cx81958.tmweb.ru/api/v1/phoneNumberStatus'
                ];
                $general = General::where('name','eskiz')->first();
                if($general){
                    $token =  $general->value;
                }else{
                    $token = $this->getToken();
                }
                if($token){
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                                'Authorization'=>'Bearer '.$token,
                            ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                            if($response->ok()){
                                $responseArr['user_id']=$request->user_id;
                                $responseArr['message'] = 'Success';
                                return response()->json($responseArr, Response::HTTP_CREATED);
                            }else{
                                $json  =  [
                                    'messages'=>[
                                        [
                                            'recipient'=>$phonebook->phoneNumber,
                                            'message-id'=>'abc000000001',
                                            'sms'=>[
                                                'originator'=>'3700',
                                                'content'=>[
                                                    'text'=>"PharmIQ Confirmation code: ".$phonebook->random
                                                ]
                                            ]
                                        ]
                                    ]
                                    ];
                                $response =   Http::withHeaders([
                                        'Accept'=>'application/json',
                                        'Content-Type'=>'application/json',
                                       
                                    ])->post('http://91.204.239.44/broker-api/send',$json);
                                if($response->ok()){
                                    $responseArr['user_id']=$request->user_id;
                                    $responseArr['message'] = 'Success';
                                    return response()->json($responseArr, Response::HTTP_CREATED);
                                }else{
                                    $responseArr['error']=true;
                                    $responseArr['message'] = 'SMS  not sent'.$response;
                                    return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
                                }
                            }
                        }
                        
                    }
                    if($response->ok()){
                        $responseArr['user_id']=$request->user_id;
                        $responseArr['message'] = 'Success';
                        return response()->json($responseArr, Response::HTTP_CREATED);
                    }else{
                        $json  =  [
                            'messages'=>[
                                [
                                    'recipient'=>$phonebook->phoneNumber,
                                    'message-id'=>'abc000000001',
                                    'sms'=>[
                                        'originator'=>'3700',
                                        'content'=>[
                                            'text'=>"PharmIQ Confirmation code: ".$phonebook->random
                                        ]
                                    ]
                                ]
                            ]
                            ];
                        $response =   Http::withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                               
                            ])->post('http://91.204.239.44/broker-api/send',$json);
                        if($response->ok()){
                            $responseArr['user_id']=$request->user_id;
                            $responseArr['message'] = 'Success';
                            return response()->json($responseArr, Response::HTTP_CREATED);
                        }else{
                            $responseArr['error']=true;
                            $responseArr['message'] = 'SMS  not sent'.$response;
                            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                    
                    $responseArr['error']=true;
                    $responseArr['message'] = 'SMS  not sent'.$response;
                    return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR); 
                }else{

                    $json  =  [
                        'messages'=>[
                            [
                                'recipient'=>$phonebook->phoneNumber,
                                'message-id'=>'abc000000001',
                                'sms'=>[
                                    'originator'=>'3700',
                                    'content'=>[
                                        'text'=>"PharmIQ Confirmation code: ".$phonebook->random
                                    ]
                                ]
                            ]
                        ]
                        ];
                    $response =   Http::withHeaders([
                            'Accept'=>'application/json',
                            'Content-Type'=>'application/json',
                           
                        ])->post('http://91.204.239.44/broker-api/send',$json);
                    if($response->ok()){
                        $responseArr['user_id']=$request->user_id;
                        $responseArr['message'] = 'Success';
                        return response()->json($responseArr, Response::HTTP_CREATED);
                    }else{
                        $responseArr['error']=true;
                        $responseArr['message'] = 'SMS  not sent';
                        return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
                
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Code not created and sent';
            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function confirmNumberByNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|size:12',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        
        
        $phonebook = Phonebook::where('phoneNumber', $request->phoneNumber)->first();
        if(!$phonebook){
            $responseArr['error']=true;
            $responseArr['message'] = 'Phone number with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
       
        $user = User::where('id', $phonebook->user_id)->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($phonebook->status==1){
            $phonebook->status = 0;
            
        }
        $phonebook->random =mt_rand(100000, 1000000);
        $phonebook->randomTime = Carbon::now();
        if($phonebook->save()){
            try {
                $json  =  [
                    'mobile_phone'=>$phonebook->phoneNumber,
                    'message'=>"PharmIQ Confirmation code: ".$phonebook->random,
                    'from'=>4546,
                    'callback_url'=>'http://api.pharmiq.uz/api/v1/phoneNumberStatus'
                ];
                $general = General::where('name','eskiz')->first();
                if($general){
                    $token =  $general->value;
                }else{
                    $token = $this->getToken();
                }
                if($token){
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                    if($response->ok()){
                        $responseArr['user_id']=$user->hrid;
                        $responseArr['message'] = 'Success';
                        return response()->json($responseArr, Response::HTTP_CREATED);
                    }
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                                'Authorization'=>'Bearer '.$token,
                            ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                            if($response->ok()){
                                $responseArr['user_id']=$user->hrid;
                                $responseArr['message'] = 'Success';
                                return response()->json($responseArr, Response::HTTP_CREATED);
                            }
                        }
                        
                    }
                    $responseArr['error']=true;
                    $responseArr['message'] = 'SMS  not sent'.$response;
                    return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
                }else{

                    $json  =  [
                        'messages'=>[
                            [
                                'recipient'=>$phonebook->phoneNumber,
                                'message-id'=>'abc000000001',
                                'sms'=>[
                                    'originator'=>'3700',
                                    'content'=>[
                                        'text'=>"PharmIQ Confirmation code: ".$phonebook->random
                                    ]
                                ]
                            ]
                        ]
                        ];
                    $response =   Http::withHeaders([
                            'Accept'=>'application/json',
                            'Content-Type'=>'application/json',
                           
                        ])->post('http://91.204.239.44/broker-api/send',$json);
                    if($response->ok()){
                        $responseArr['user_id']=$user->hrid;
                        $responseArr['message'] = 'Success';
                        return response()->json($responseArr, Response::HTTP_CREATED);
                    }else{
                        $responseArr['error']=true;
                        $responseArr['message'] = 'SMS  not sent';
                        return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
                
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Code not created and sent';
            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function confirmResponse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'code'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('hrid', $request->user_id)->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
       
        $phonebook = Phonebook::where('user_id', $user->id)->first();
        if(!$phonebook){
            $responseArr['error']=true;
            $responseArr['message'] = 'Phone number with user_id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }


        $start = \Carbon\Carbon::parse($phonebook->randomTime);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
        if((int)$diffhour>0){
            $responseArr['error']=true;
            $responseArr['message'] = 'Confirmation code expired';
            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if((int)$diffminut>5){
            $responseArr['error']=true;
            $responseArr['message'] = 'Confirmation code expired';
            return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if($phonebook->random==$request->code){
            $phonebook->random = Str::random(5);
            $phonebook->status = 1;
            if($phonebook->save()){
                ////
                ////
                ////
                ////
                ///
                ////
                $user = User::select('id','hrid as user_id', 'firstName', 'lastName','birthDate', 'gender','language','role')->where('hrid',$request->user_id)->first();
                $token = $user->createToken('myapptoken')->plainTextToken;
                $response =[
                    'user'=>$user,
                    'token'=>$token,
                    'hasAccess'=>$this->getAccess($user)
                ];
                return response($response,201);
              
            }else{
                $responseArr['error']=true;
                $responseArr['message'] = 'Phone number is not confirmed';
                return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Respond code confirmation not same';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
    }

    
    public function setCompanyMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'user_id'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::find($data['company_id']);
        if($company){
            $user = User::where('hrid',$data['user_id'])->first();
            if($user){
                $checkMembership = CompanyMembers::where('member_id', $user->id)->first();
                if($checkMembership){
                    $responseArr['error']=true;
                    $responseArr['message'] = 'User with given id has been already member of a company';
                    return response()->json($responseArr, Response::HTTP_FOUND);
                }else{
                    $companyMembers = new CompanyMembers();
                    $companyMembers->member_id = $user->id;
                    $companyMembers->company_id = $data['company_id'];
                    
                    if($companyMembers->save()){
                        $companyMembersHistory = new CompanyMemberHistories();
                        $companyMembersHistory->company_id= $data['company_id'];
                        $companyMembersHistory->member_id= $user->id;
                        $companyMembersHistory->moderated= auth()->user()? auth()->user()->id : 1;
                        if($companyMembersHistory->save()){
                            StandardAttributes::setSA('company_member_histories',$companyMembersHistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'], 'pgsql2');
                            $responseArr['message'] = 'Success';
                            return response()->json($responseArr, Response::HTTP_OK);
                        }else{
                            $responseArr['error']=true;
                            $responseArr['message'] = 'Error while saving members history';
                            return response()->json($responseArr, Response::HTTP_BAD_GATEWAY);
                        }
                    }else{
                        $responseArr['error']=true;
                        $responseArr['message'] = 'Error while saving members';
                        return response()->json($responseArr, Response::HTTP_BAD_GATEWAY);
                    }
                    
                }
                
            }
        }
        $responseArr['error']=true;
        $responseArr['message'] = 'Company or User with given id not found';
        return response()->json($responseArr, Response::HTTP_NOT_FOUND);
    }
}
