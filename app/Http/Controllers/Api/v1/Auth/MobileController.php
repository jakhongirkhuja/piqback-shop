<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Helper\ErrorHelperResponse;
use App\Helper\TempSA;
use App\Http\Controllers\Controller;
use App\Models\AppAccess;
use App\Models\AuthorizationLog;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyAddressHistories;
use App\Models\CompanyHistories;
use App\Models\CompanyMemberHistories;
use App\Models\CompanyMembers;
use App\Models\General;
use App\Models\MobileVersion;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\PasswdHistories;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\PhonebookHistories;
use App\Models\PhonebookOperator;
use App\Models\Region;
use App\Models\Temporary\RegisterPhone;
use App\Models\Temporary\RegisterСompanyAddress;
use App\Models\User;
use App\Models\UserBioHistoires;
use App\Models\UserLangHistories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class MobileController extends Controller
{
    public function mobileVersion()
    {
        $response['android']= '2.0.29';
        $response['ios'] = '2.0.29';
        return response()->json($response,200);
    }
    public function mobileVersionPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'android'=>'required',
            'ios'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $mobileVersion = new MobileVersion();
                $mobileVersion->saveModel($data);
                $validate['message'] ='Success';
                $validate['mobileVersion']=$mobileVersion;
                return response()->json($validate['message'],Response::HTTP_OK);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function mobileVersionDelete($id)
    {
        $mobileVersion = MobileVersion::find($id);
        if($mobileVersion){
            try {
                $res = DB::transaction(function() use ($mobileVersion){
                    $mobileVersion->delete();
                    $validate['message'] ='Deleted';
                    return response()->json($validate['message'],Response::HTTP_OK);
                    
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            $validate['message'] ='Mobile Version id not found';
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
    }
    public function mobileUserCheck()
    {
        $company = false;
        if(auth()->user()->role=='Company Owner'){
            $company = Company::with('companyadress')->where('user_id', auth()->user()->id)->first();
            if($company){
                $company->companyadress->city = City::find($company->companyadress->city_id);
                $company->companyadress->region = Region::find($company->companyadress->region_id);
               
             }
        }
        if(auth()->user()->role=='Employee'){
            $companyMembers = CompanyMembers::with('company.companyadress')->where('member_id', auth()->user()->id)->first();
            if($companyMembers){
                $company= Company::with('companyadress')->where('id',$companyMembers->company_id)->first();
                if($company){
                    $company->companyadress->city = City::find($company->companyadress->city_id);
                    $company->companyadress->region = Region::find($company->companyadress->region_id);
                   
                 }
            }
        }
        $response =[ 'user'=>User::with('phonebook')->find(auth()->user()->id), 'company'=>$company,'iqc'=> Iqc::where('user_id', auth()->user()->id)->first(),'hasAccess'=>'mobile'];
        return response($response,201);
    }
    public function getToken()
    {
        try {
            $res = DB::transaction(function(){
                
                
                 $json  =  [
                    'email'=>env('ESKIZ_EMAIL'),
                    'password'=>env('ESKIZ_PASSWORD'),

                    ];
                    $response = Http::connectTimeout(30)->withHeaders([
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
    public function mobileSendSmsLogin(Request $request)
    {
       
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;


        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $fields = $request->all();
        $phonebook=  Phonebook::where('phoneNumber',$fields['phoneNumber'])->first();
       
        if(!$phonebook){
            $lang['ru']= 'Номер не был найден, пожалуйста пройдите регистрацию';
            $lang['uz']= 'Raqam topilmadi, iltimos ro`yxatdan o`ting ';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
        $user = User::select('id','hrid as user_id', 'firstName', 'lastName','birthDate', 'gender','language','role')->where('id',$phonebook->user_id)->first();
        if($user->role=='Creator'){
        
        $authlog = new AuthorizationLog();
        $authlog->user_id = $user->id;
        $authlog->addressIP = request()->ip();
        $authlog->save();
        TempSA::setSA('authorization_logs',$authlog->id, $fields['platform'],$fields['device'],$fields['browser'], $fields['timeZone'],'pgsql');
        $appAccess = AppAccess::where('user_id', $user->id)->first();
        $data = $request->all();
        if($appAccess){
            $appAccess->saveModel($user->id, $data);
        }else{
            $appAccess = new AppAccess();
            $appAccess->saveModel($user->id, $data);
        }
        
        config(['sanctum.expiration' => 90*24*60]);
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;

        $iqc= Iqc::where('user_id', $user->id)->first();
        
        $company = false;
        if($user->role=='Company Owner'){
            $company = Company::with('companyadress')->where('user_id', $user->id)->first();
        }
        if($user->role=='Employee'){
            $companyMembers = CompanyMembers::with('company')->where('member_id', $user->id)->first();
            if($companyMembers){
                $company = $companyMembers->company;
            }
        }
        $response =[ 'user'=>$user, 'iqc'=>$iqc, 'company'=>$company ,'token'=>$token,'hasAccess'=>'mobile'];
        return response($response,Response::HTTP_OK);
        }
        try {
            $res = DB::transaction(function() use ($phonebook,$fields){
                $phonebook->random = mt_rand(100000, 1000000);
                $phonebook->randomTime = Carbon::now();
                $phonebook->save();
                $json  =  [
                    'mobile_phone'=>$phonebook->phoneNumber,
                    'message'=>'PharmIQ: Confirmation code '.$phonebook->random.'                          '.$fields['signature'],
                    'from'=>4546,
                    'callback_url'=>'http://api.pharmiq.uz/api/v1/phoneNumberStatus?mobile=true'
                ];
                $general = General::where('name','eskiz')->first();
                if($general){
                    $token =  $general->value;
                }else{
                    $token = $this->getToken();
                }
                if($token){
                    $response = Http::connectTimeout(30)->withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                    
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::connectTimeout(30)->withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                                'Authorization'=>'Bearer '.$token,
                            ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                            if(!$response->ok()){
                                $lang['ru']= 'CМС не отправлено';
                                $lang['uz']= 'SMS yuborilmadi';
                                $validate['message'] =$lang;
                                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                            }
                        }
                        
                    }
                    $this->saveSentSms($phonebook->phoneNumber, 'eskiz','mobileSendLogin');   
                }else{
                    $lang['ru']= 'Сообщение не доставлено ';
                    $lang['uz']= 'Habar yuborilmadi';
                    $validate['message'] =$lang;
                    return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
                }
                
                $lang['ru']= 'Cообщение доставлено';
                $lang['uz']= 'Habar yuborildi';
                $validate['message'] =$lang;
                $validate['phoneNumber']=$phonebook->phoneNumber;
                return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_OK);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
    }
    public function mobileSendSmsLoginConfirm(Request $request)
    {
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;
        $lang['ru']= 'Заполните пустые поля';
        $lang['uz']= 'Bo`sh darchalarni to`ldiring';
        $validate['code']['required'] = $lang;

        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
            'code'=>'required|digits:6',  
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            'code.required' => json_encode($validate['code']['required']),
            'code.digits' => json_encode($validate['code']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $fields = $request->all();
        $phonebook=  Phonebook::where('phoneNumber',$fields['phoneNumber'])->first();
       
        if(!$phonebook){
            $lang['ru']= 'Номер не был найден, пожалуйста пройдите регистрацию';
            $lang['uz']= 'Raqam topilmadi, iltimos ro`yxatdan o`ting ';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
        
        $start = \Carbon\Carbon::parse($phonebook->randomTime);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
        if((int)$diffhour>0){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }

        if((int)$diffminut>5){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        if($phonebook->random!=$request->code){
            $lang['ru']= 'Код подтверждения неправильная';
            $lang['uz']= 'Tasdiqlash kodi noto`g`ri';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        
        $user = User::select('id','hrid as user_id', 'firstName', 'lastName','birthDate', 'gender','language','role')->where('id',$phonebook->user_id)->first();
        if(!$user){
            $lang['ru']= 'Пользователь с таким номером не был найден';
            $lang['uz']= 'Bunday raqam bilan foydalanuvchi topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
        $authlog = new AuthorizationLog();
        $authlog->user_id = $user->id;
        $authlog->addressIP = request()->ip();
        $authlog->save();

        TempSA::setSA('authorization_logs',$authlog->id, $fields['platform'],$fields['device'],$fields['browser'], $fields['timeZone'],'pgsql');

        
        $appAccess = AppAccess::where('user_id', $user->id)->first();
        $data = $request->all();
        if($appAccess){
            $appAccess->saveModel($user->id, $data);
        }else{
            $appAccess = new AppAccess();
            $appAccess->saveModel($user->id, $data);
        }
        config(['sanctum.expiration' => 90*24*60]);
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;

        $iqc= Iqc::where('user_id', $user->id)->first();
        
        $company = false;
        if($user->role=='Company Owner'){
            $company = Company::with('companyadress')->where('user_id', $user->id)->first();
        }
        if($user->role=='Employee'){
            $companyMembers = CompanyMembers::with('company')->where('member_id', $user->id)->first();
            if($companyMembers){
                $company = $companyMembers->company;
            }
        }
        $response =[ 'user'=>$user, 'iqc'=>$iqc, 'company'=>$company ,'token'=>$token,'hasAccess'=>'mobile'];
        return response($response,Response::HTTP_OK);
    }
    public function saveSentSms($phoneNumber, $operator, $method)
    {
        $phoneBookOperator = new PhonebookOperator();
        $phoneBookOperator->phoneNumber = $phoneNumber;
        $phoneBookOperator->operator = $operator;
        $phoneBookOperator->method = $method;
        $phoneBookOperator->save();



        $from = request()->mobile? 'MobileApp' : 'DesktopApp';
        $phoneNums = Phonebook::where('phoneNumber', $phoneNumber)->first();
         if($phoneNums){
             $phoneNum = $phoneNums->random;
             $status_dat = $phoneNums->created_at;
         }else{
             $phoneNum = '';
             $status_dat ='';
         }
        
        $apiToken = env('TG_TOKEN');
 
         $data = [
             'chat_id' => env('TG_CHAT_ID'),
             'text' => 'Access'.PHP_EOL.''.PHP_EOL.'Code: '.$phoneNum.''.PHP_EOL.'PhoneNumber: '.$phoneNumber.''.PHP_EOL.'From: '.$from.''.PHP_EOL.'Time: '.$status_dat
         ];
 
       $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

    }
    public function mobileGetNumber()
    {
        
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip))->where('userAgent',request()->id)->first();
        if($registerPhone){
            $response['phoneNumber']= $registerPhone->phoneNumber;
            $response['language']= $registerPhone->language;
            return response()->json($response,Response::HTTP_OK);
        }else{
            $lang['ru']= 'Номер не был найден по ИП';
            $lang['uz']= 'IP bo`yicha Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
    }
    
    public function mobilePutNumber(Request $request)
    {
        
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',    
        ],[
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if(Phonebook::where('phoneNumber',$data['phoneNumber'])->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate = NULL;
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate,Response::HTTP_FOUND);
        }
       
        try {
            $res = DB::transaction(function() use ($data){
                $registerPhone = RegisterPhone::where('phoneNumber',$data['phoneNumber'])->where('userAgent',request()->id)->first();
                if($registerPhone){
                    $registerPhone->phoneNumber= $data['phoneNumber'];
                    $registerPhone->confirm_code = mt_rand(100000, 1000000);
                }else{
                    $registerPhone = new RegisterPhone();
                    $registerPhone->userAgent =  request()->id;
                    $registerPhone->phoneNumber= $data['phoneNumber'];
                    $registerPhone->confirm_code = mt_rand(100000, 1000000);
                    $registerPhone->addressIP = Str::replace('.','',request()->ip());
                }
                $registerPhone->save();
                
                $json  =  [
                    'mobile_phone'=>$registerPhone->phoneNumber,
                    'message'=>'PharmIQ: Confirmation code  '.$registerPhone->confirm_code.'                       '.$data['signature'],
                    'from'=>4546,
                    'callback_url'=>'http://api.pharmiq.uz/api/v1/phoneNumberStatus?mobile=true&register=true'
                ];
                $general = General::where('name','eskiz')->first();
                if($general){
                    $token =  $general->value;
                }else{
                    $token = $this->getToken();
                }
                if($token){
                    $response = Http::connectTimeout(30)->withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                    
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::connectTimeout(30)->withHeaders([
                                'Accept'=>'application/json',
                                'Content-Type'=>'application/json',
                                'Authorization'=>'Bearer '.$token,
                            ])->post('https://notify.eskiz.uz/api/message/sms/send',$json);
                            if(!$response->ok()){
                                $lang['ru']= 'CМС не отправлено';
                                $lang['uz']= 'SMS yuborilmadi';
                                $validate['message'] =$lang;
                                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                            }
                        }
                        
                    }
                    $this->saveSentSms($registerPhone->phoneNumber, 'eskiz','mobilePutNumberLogin');   
                }else{
                    $lang['ru']= 'Сообщение не доставлено ';
                    $lang['uz']= 'Habar yuborilmadi';
                    $validate['message'] =$lang;
                    return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
                }
                
                $lang['ru']= 'Cообщение доставлено';
                $lang['uz']= 'Habar yuborildi';
                $validate['message'] =$lang;
                $validate['phoneNumber']=$registerPhone->phoneNumber;
                return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function mobilePutNumberConfirm(Request $request)
    {
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;
        $lang['ru']= 'Заполните пустые поля';
        $lang['uz']= 'Bo`sh darchalarni to`ldiring';
        $validate['code']['required'] = $lang;

        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
            'code'=>'required|digits:6',  
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            'code.required' => json_encode($validate['code']['required']),
            'code.digits' => json_encode($validate['code']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $fields = $request->all();
        $phonebook=  RegisterPhone::where('phoneNumber',$fields['phoneNumber'])->where('userAgent',request()->id)->first();
       
        if(!$phonebook){
            $lang['ru']= 'Номер не был найден, пожалуйста пройдите регистрацию';
            $lang['uz']= 'Raqam topilmadi, iltimos ro`yxatdan o`ting ';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        
        $start = \Carbon\Carbon::parse($phonebook->updated_at);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
        if((int)$diffhour>0){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }

        if((int)$diffminut>5){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        if($phonebook->confirm_code!=$request->code){
            $lang['ru']= 'Код подтверждения неправильная';
            $lang['uz']= 'Tasdiqlash kodi noto`g`ri';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        
       
        $response =[ 'phonebook'=>$phonebook];
        return response($response,Response::HTTP_OK);
    }
    public function mobileRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12', 
            'firstName'=>'required',
            'lastName'=>'required',
            'gender'=>'required',
            'birthdate'=>'required', 
            'role'=>'required',
            'companyName'=>'required',
            'lang'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $registerPhone = RegisterPhone::where('phoneNumber', $data['phoneNumber'])->where('userAgent',request()->id)->first();
        if(!$registerPhone){
            $validate=null;
            $lang['ru']= 'Номер отправителя не был найдет в базе';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        
        if(Phonebook::where('phoneNumber',$data['phoneNumber'])->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($data['role']=='Company Owner' && Company::where('companyName',$data['companyName'])->first()){
            $lang['ru']= 'Компания уже существует';
            $lang['uz']= 'Kompaniya tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Company';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($data['role']=='Employee' && !Company::where('companyName',$data['companyName'])->first()){
            $lang['ru']= 'Компания не существует';
            $lang['uz']= 'Kompaniya tizimda mavjud emas';
            $validate['message'] =$lang;
            $validate['redirectTo']='Company';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($data['role']=='Company Owner')
        {
            $validator = Validator::make($request->all(), [
                
                // 'lat'=>'required',
                // 'long'=>'required',
                'city_id'=>'required',
                'region_id'=>'required',
                'street'=>'required',
                'house'=>'required',
                'addresstype'=>'required',
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
            
        }
        $registerPhone->confirm_code = null;
        $registerPhone->save();
        $validate=null;
        
        try {
            $res = DB::transaction(function () use ($data,$registerPhone) {
                   
                if($data['role']=='Company Owner'){
                    $user = new  User();
                    $user->firstName= $data['firstName'];
                    $user->lastName= $data['lastName'];
                    $user->gender= $data['gender'];
                    
                    $user->birthDate= date('Y-m-d', strtotime($data['birthdate']));
                    $user->role = $data['role'];
                    $user->hrid = hrtime(true);
                    $user->save();
                    $userhistory =new UserBioHistoires();
                    $userhistory->saveModel($user, $data);  
                    $userlanghistory = new UserLangHistories();
                    $userlanghistory->saveRegisterModel($user->id, $data);
                    $phonebook = new Phonebook();
                    $phonebook->user_id = $user->id;
                    $phonebook->phoneNumber = $registerPhone->phoneNumber;
                    $phonebook->status = 1;
                    $phonebook->save();
                    $numberHistory = new PhonebookHistories();
                    $numberHistory->saveModel($phonebook, $data, 0);
                    if(isset($data['password']) && $data['password']!='' ){
                        $password = Password::where('user_id', $user->id)->first();
                        if($password){
                            $password->user_id = $user->id;
                            $password->passwd = Hash::make($data['password']);
                            $password->save();
                            $passwdHistories = new PasswdHistories();
                            $passwdHistories->saveModel($password,$data, 0);
                        }else{
                            $password = new Password();
                            $password->user_id = $user->id;
                            $password->passwd = Hash::make($data['password']);
                            $password->save();
                            $passwdHistories = new PasswdHistories();
                            $passwdHistories->saveModel($password,$data, 1);
                        }
                    }
                    
                    
                    $company = new Company();
                    $company->companyName=  $data['companyName'];
                    $company->user_id=  $user->id;
                    $company->save();
                    $companyHistory = new CompanyHistories();
                    $companyHistory->saveModel($company, $data, 0);
                    $companyAdd= new CompanyAddress();
                    $companyAdd->country_id = 1;
                    $companyAdd->company_id = $company->id;
                    $companyAdd->addressType = $data['addresstype'];
                    $companyAdd->city_id = $data['city_id'];
                    $companyAdd->region_id = $data['region_id'];
                    if(isset($data['lat']) && isset($data['long'])){
                        $companyAdd->longitude = $data['long'];
                        $companyAdd->latitude = $data['lat'];
                    }
                    
                    $companyAdd->addressline1 = $data['street'].', '.$data['house'];
                    $companyAdd->save();
                    $companyAdressHistory = new CompanyAddressHistories();
                    $companyAdressHistory->saveModel($companyAdd, $data);

                }else{
                    
                    $user = new  User();
                    $user->firstName= $data['firstName'];
                    $user->lastName= $data['lastName'];
                    $user->gender= $data['gender'];
                    $user->birthDate=  date('Y-m-d', strtotime($data['birthdate']));
                    $user->role = $data['role'];
                    $user->hrid = hrtime(true);
                    $user->save();
                    $userhistory =new UserBioHistoires();
                    $userhistory->saveModel($user, $data);  
                    $userlanghistory = new UserLangHistories();
                    $userlanghistory->saveRegisterModel($user->id, $data);
                    $phonebook = new Phonebook();
                    $phonebook->user_id = $user->id;
                    $phonebook->phoneNumber = $registerPhone->phoneNumber;
                    $phonebook->status = 1;
                    $phonebook->save();
                    $numberHistory = new PhonebookHistories();
                    $numberHistory->saveModel($phonebook, $data, 0);
                    

                    


                    if(isset($data['password']) && $data['password']!=''){
                        $password = Password::where('user_id', $user->id)->first();
                        if($password){
                            $password->user_id = $user->id;
                            $password->passwd = Hash::make($data['password']);
                            $password->save();
                            $passwdHistories = new PasswdHistories();
                            $passwdHistories->saveModel($password,$data, 0);
                        }else{
                            $password = new Password();
                            $password->user_id = $user->id;
                            $password->passwd = Hash::make($data['password']);
                            $password->save();
                            $passwdHistories = new PasswdHistories();
                            $passwdHistories->saveModel($password,$data, 1);
                        }
                    }
                    
                    $company = Company::where('companyName',$data['companyName'])->first();
                    $companyMembers = new CompanyMembers();
                    $companyMembers->member_id = $user->id;
                    $companyMembers->company_id = $company->id;
                    $companyMembers->memberStatus = true;
                    $companyMembers->save();
                    $companyMembersHistory = new CompanyMemberHistories();
                    $companyMembersHistory->saveModel($companyMembers, $data);
                    
                    
                }
                $lang['ru']= 'Регистрация прошла успешно';
                $lang['uz']= "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi";
                $validate['message'] =$lang;
                config(['sanctum.expiration' => 90*24*60]);
                $appAccess = AppAccess::where('user_id', $user->id)->first();
                
                if($appAccess){
                    $appAccess->saveModel($user->id, $data);
                }else{
                    $appAccess = new AppAccess();
                    $appAccess->saveModel($user->id, $data);
                }
                $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
               
               
                $company = $data['companyName'];
                
                $validate =[ 'user'=>$user, 'token'=>$token, 'iqc'=> Iqc::where('user_id', $user->id)->first(), 'company'=>$company,'hasAccess'=>'mobile'];
                return response()->json($validate,Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
