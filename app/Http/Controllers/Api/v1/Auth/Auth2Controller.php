<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Helper\TempSA;
use App\Http\Controllers\Controller;
use App\Models\AuthorizationLog;
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
use App\Models\Money\Iqc;
use App\Models\PasswdHistories;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\PhonebookHistories;
use App\Models\PhonebookOperator;
use App\Models\Region;
use App\Models\Temporary\RegisterBio;
use App\Models\Temporary\RegisterEmail;
use App\Models\Temporary\RegisterPassword;
use App\Models\Temporary\RegisterPhone;
use App\Models\Temporary\RegisterRole;
use App\Models\Temporary\RegisterСompany;
use App\Models\Temporary\RegisterСompanyAddress;
use App\Models\Temporary\RegisterСompanyMember;
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

class Auth2Controller extends Controller
{
    public function getAccess($user)
    {
        $hasAccess = '';
        switch ($user->role) {
            case 'SuperAdmin':
                $hasAccess = 'board.pharmiq.uz/auth-user?token=,academy.pharmiq.uz?token=';
                break;
            case 'Company Owner':
                $hasAccess = 'academy.pharmiq.uz?token=';
                break;
            case 'Employee':
                $hasAccess = 'academy.pharmiq.uz?token=';
                break;
            case 'Scout':
                $hasAccess = 'academy.pharmiq.uz?token=';
                break;
            case 'Creator':
                $hasAccess = 'board.pharmiq.uz/auth-user?token=, academy.pharmiq.uz?token=';
                break;
            case 'Owner':
                $hasAccess = 'board.pharmiq.uz/auth-user?token=, academy.pharmiq.uz?token=';
                break;
            case 'Analytic':
                $hasAccess = 'board.pharmiq.uz/auth-user?token=';
                break;
            case 'Zero':
                $hasAccess = 'board.pharmiq.uz/auth-user?token=';
                break;
            default:
                $hasAccess = 'academy.pharmiq.uz?token=';
                break;
        }
        return $hasAccess;
    }
    public function logout(Request $request)
    {
        $user = auth()->user();
        $fields = $request->all();
        auth()->user()->tokens()->delete();
        $authlog = new AuthorizationLog();
        $authlog->user_id = $user->id;
        $authlog->actionType = 'logout';
        $authlog->addressIP = request()->ip();
        $authlog->save();
        TempSA::setSA('authorization_logs',$authlog->id, $fields['platform'],$fields['device'],$fields['browser'], $fields['timeZone'],'pgsql');
        $responseArr['message'] = 'Success';
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function login(Request $request)
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

        $lang['ru']= 'Заполните пароль';
        $lang['uz']= 'Mahfiy kalitni kiriting';
        $validate['password']['required'] =$lang;
        $lang['ru']= 'Заполните пароль';
        $lang['uz']= 'Mahfiy kalitni kiriting';
        $validate['password']['required'] =$lang;
        $lang['ru']= 'Пароль должен быть не менее 8 символов';
        $lang['uz']= 'Mahfiy kalitni kiriting, kamida 8 ta simbol';
        $validate['password']['string'] =$lang;

        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
            'password'=>'required|string|min:8',
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            'password.required' => json_encode($validate['password']['required']),
            'password.string' => json_encode($validate['password']['string']),
            'password.min' => json_encode($validate['password']['string']),
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
        $password=  Password::where('user_id',$phonebook->user_id)->first();
        
        if(!$password || !Hash::check($fields['password'],$password->passwd)){
            $lang['ru']= 'Пароль неправильный, проверьте и введите его заново или воспользуйтесь функцией «Забыл пароль»';
            $lang['uz']= "Parol noto‘g‘ri, uni tekshiring va qaytadan kiriting yoki “Parolni unutdim” funksiyasidan foydalaning";
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
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
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
       
        
        $response =[ 'user'=>$user, 'token'=>$token,'hasAccess'=>$this->getAccess($user) ];
        return response($response,201);
    }
    public function checkNumber(Request $request)
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
        $number = Phonebook::where('phoneNumber',$request['phoneNumber'])->first();
        if(!$number){
            $lang['ru']= 'Номер не был найден';
            $lang['uz']= 'Raqam tizimda mavjud emas';
            $validate['message'] =$lang;
            return response()->json(json_encode($validate['message']),Response::HTTP_OK);
        }else{
            if($number->status==0){
                $lang['ru']= 'Номер не был активен, пожалуйста пройдите по восстановление пароля';
                $lang['uz']= "Raqam faolashtirilmagan  edi, mahfiy so'zni tiklashga o'ting";
            }else{
                $lang['ru']= 'Номер был найден, пожалуйста пройдите авторизацию';
                $lang['uz']= 'Raqam mavjud, tizimga kiring';
            }
        }
        $validate['message'] =$lang;
        return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_FOUND);
        
    }
    
    public function resetPassword(Request $request)
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
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $number = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$number){
            $lang['ru']= 'Номер не был найден';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
            
        }
        $user = User::find($number->user_id);
        if(!$user){
            $lang['ru']= 'Пользоватль не был найден';
            $lang['uz']= 'Foydalanuvchi tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $number){
                
                $number->status = 0;
                $number->random = mt_rand(100000, 1000000);
                $number->randomTime = Carbon::now();
                $number->save();
                $json  =  [
                    'mobile_phone'=>$number->phoneNumber,
                    'message'=>'PharmIQ Confirmation code: '.$number->random,
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
                    
                    if($response->status()==401){
                        $token = $this->getToken();
                        if($token){
                            $response = Http::withHeaders([
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
                    $this->saveSentSms($number->phoneNumber, 'eskiz','webPasswordReset');   
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
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                      
                    ])->post('http://91.204.239.44/broker-api/send',$json);
                    if( $response->status()!=200){
                        $lang['ru']= 'Сообщение не доставлено ';
                        $lang['uz']= 'Habar yuborilmadi';
                        $validate['message'] =$lang;
                        return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
                    }
                    $this->saveSentSms($number->phoneNumber, 'playmobile','webPasswordReset');
                }
                
                
                
                // number check
                
                $lang['ru']= 'Cообщение доставлено';
                $lang['uz']= 'Habar yuborildi';
                $validate['message'] =$lang;
                $validate['phoneNumber']=$number->phoneNumber;
                return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_OK);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function codeConfirm(Request $request)
    {
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Заполните секретнет номер';
        $lang['uz']= 'Mahfiy raqamni kiriting';
        $validate['code']['required'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
            'code'=>'required',
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            'code.required' => json_encode($validate['code']['required']),
            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $number = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$number){
            $lang['ru']= 'Номер не был найден';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
            
        }
        
        $start = \Carbon\Carbon::parse($number->randomTime);
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
        if($number->random==$request->code){
            // $number->random = Str::random(5);
            $number->status = 1;
            if($number->save()){
                $lang['ru']= 'Номер активирован';
                $lang['uz']= 'Raqam aktiv holata';
                $validate['message'] =$lang;
                $validate['code']=$number->random;
                $validate['phoneNumber']=$number->phoneNumber;
                return ErrorHelperResponse::returnError($validate,Response::HTTP_OK);
            }else{
                return ErrorHelperResponse::returnError('Something wrong please connect with admin',Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            $lang['ru']= 'Код подтверждения неправильная';
            $lang['uz']= 'Tasdiqlash kodi noto`g`ri';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
    }

    public function newPasswordConfirm(Request $request)
    {
        $lang['ru']= 'Заполните номер';
        $lang['uz']= 'Telefon raqamingizni kiriting';
        $validate['phoneNumber']['required'] = $lang;
        $lang['ru']= 'Заполните секретнет номер';
        $lang['uz']= 'Mahfiy raqamni kiriting';
        $validate['code']['required'] = $lang;
        $lang['ru']= 'Заполните пароль';
        $lang['uz']= 'Mahfiy so`zni kiriting';
        $validate['password']['required'] = $lang;
        $lang['ru']= 'Пароль должен содержать не менее 8 символов';
        $lang['uz']= 'Mahfiy so`z kamida 8 ta dan ko`p bo`lishi kerak';
        $validate['password']['min'] = $lang;
        $lang['ru']= 'Номер не правильно прописан';
        $lang['uz']= 'Telefon notogri kiritilgan';
        $validate['phoneNumber']['size'] = $lang;
        $lang['ru']= 'Номер должен состоять из цифр';
        $lang['uz']= 'Telefon raqamingizni raqamlardan iborat bo`lishi kerak';
        $validate['phoneNumber']['numeric'] =$lang;
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|numeric|digits:12',
            'code'=>'required',
            'password'=>'required|min:8'
        ],
        [
            'phoneNumber.required' => json_encode($validate['phoneNumber']['required']),
            'phoneNumber.digits' => json_encode($validate['phoneNumber']['size']),
            'phoneNumber.numeric' => json_encode($validate['phoneNumber']['numeric']),
            'code.required' => json_encode($validate['code']['required']),
            'password.required'=>json_encode($validate['password']['required']),
            'password.min'=>json_encode($validate['password']['min']),
            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $number = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$number){
            $lang['ru']= 'Номер не был найден';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        if($number->random!=$data['code']){
            return ErrorHelperResponse::returnError('Something wrong please connect with admin',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        try {
            $res = DB::transaction(function() use ($data,$number){
                $password=  Password::where('user_id',$number->user_id)->first();
                if($password){
                    $password->passwd = Hash::make($data['password']);
                }else{
                    $password = new Password();
                    $password->user_id = $number->user_id;
                    $password->passwd = Hash::make($data['password']);
                }
                $password->save();
                $lang['ru']= 'Пароль обновлен';
                $lang['uz']= 'Mahfiy so`z yangilandi';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getcompanyAddress()
    {
        $registerCompanyAddress = RegisterСompanyAddress::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerCompanyAddress){
            $response['companyAddress']= $registerCompanyAddress;
            $response['language']= $registerCompanyAddress->language;
            return response()->json($response,Response::HTTP_OK);
        }else{
            $lang['ru']= 'Адрес  не был найден по ИП';
            $lang['uz']= 'IP bo`yicha Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
    }
    public function putcompanyAddress(Request $request)
    {
        $lang['ru']= 'Город не был выбран';
        $lang['uz']= 'Shahar tanlanmagan';
        $validate['city_id']['required'] = $lang;
        $lang['ru']= 'Тип не был выбран';
        $lang['uz']= 'Тип не был выбран';
        $validate['addressType']['required'] = $lang;
        $lang['ru']= 'Регион не был выбран';
        $lang['uz']= 'Tuman tanlanmagan';
        $validate['region_id']['required'] = $lang;
        $lang['ru']= 'Улица или дом не заполнено';
        $lang['uz']= 'Ko`cha yoki xonadon kiritilmagan';
        $validate['addressline']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'addressType'=>'required|numeric',    
            'city_id'=>'required|numeric',    
            'region_id'=>'required|numeric',    
            'addressline'=>'required',    
            'language'=>'required',    
        ],[
            'addressType.required' => json_encode($validate['addressType']['required']),
            'addressType.numeric' => json_encode($validate['addressType']['required']),
            'city_id.required' => json_encode($validate['city_id']['required']),
            'city_id.numeric' => json_encode($validate['city_id']['required']),
            'region_id.required' => json_encode($validate['region_id']['required']),
            'region_id.numeric' => json_encode($validate['region_id']['required']),
            'addressline.required' => json_encode($validate['addressline']['required']),
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if(!City::find($data['city_id'])){
            $lang['ru']= 'Город в системе не существует обновите';
            $lang['uz']= 'Shahar tizimda mavjud emas';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate,Response::HTTP_NOT_FOUND);
        }
        if(!Region::find($data['region_id'])){
            $lang['ru']= 'Регион в системе не существует обновите';
            $lang['uz']= 'Tuman tizimda mavjud emas';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate,Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data){
                $registerCompanyAddress = RegisterСompanyAddress::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                
                if($registerCompanyAddress){
                    $registerCompanyAddress->city_id= $data['city_id'];
                    $registerCompanyAddress->region_id= $data['region_id'];
                    $registerCompanyAddress->addressline= $data['addressline'];
                    $registerCompanyAddress->language= $data['language'];
                    $registerCompanyAddress->addressType= $data['addressType'];
                    $registerCompanyAddress->longitude= $data['longitude'];
                    $registerCompanyAddress->latitude= $data['latitude'];
                }else{
                    $registerCompanyAddress = new RegisterСompanyAddress();
                    $registerCompanyAddress->country_id= 1;
                    $registerCompanyAddress->city_id= $data['city_id'];
                    $registerCompanyAddress->region_id= $data['region_id'];
                    $registerCompanyAddress->addressline= $data['addressline'];
                    $registerCompanyAddress->language= $data['language'];
                    $registerCompanyAddress->addressType= $data['addressType'];
                    $registerCompanyAddress->longitude= $data['longitude'];
                    $registerCompanyAddress->userAgent =  request()->header('user-agent');
                    $registerCompanyAddress->latitude= $data['latitude'];
                    $registerCompanyAddress->addressIP = Str::replace('.','',request()->ip());
                }
                $registerCompanyAddress->save();
                TempSA::setSA('registerсompany_addresses',$registerCompanyAddress->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getNumber()
    {
        
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
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
    
    public function putNumber(Request $request)
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
                $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip())  )->where('userAgent',request()->header('user-agent'))->first();
                if($registerPhone){
                    $registerPhone->phoneNumber= $data['phoneNumber'];
                }else{
                    $registerPhone = new RegisterPhone();
                    $registerPhone->userAgent =  request()->header('user-agent');
                    $registerPhone->phoneNumber= $data['phoneNumber'];
                    $registerPhone->addressIP = Str::replace('.','',request()->ip());
                }
                $registerPhone->save();
                TempSA::setSA('register_phones',$registerPhone->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getInfo()
    {
        
        $registerBio = RegisterBio::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerBio){
            $response['bio']= $registerBio;
            $response['language']= $registerBio->language;
            return response()->json($response,Response::HTTP_OK);
        }else{
            $lang['ru']= 'Информация  не была найдена по ИП';
            $lang['uz']= 'IP bo`yicha ma`lumot tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }               
    }
    public function putInfo(Request $request)
    {
        $lang['ru']= 'Заполните Имя';
        $lang['uz']= 'Ismingizni kiriting';
        $validate['firstName']['required'] = $lang;
        $lang['ru']= 'Заполните Фамилию';
        $lang['uz']= 'Familiyangizni kiriting';
        $validate['lastName']['required'] = $lang;
        $lang['ru']= 'Выберите пол';
        $lang['uz']= 'Jinsingiznig tanlang';
        $validate['gender']['required'] =$lang;
        $lang['ru']= 'Выберите дату рождения';
        $lang['uz']= 'Tug`ilgan sanangizni tanlang';
        $validate['birthDate']['required'] =$lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'firstName'=>'required',
            'lastName'=>'required',
            'gender'=>'required',
            'birthDate'=>'required',
            'language'=>'required',    
        ],[
            'firstName.required' => json_encode($validate['firstName']['required']),
            'lastName.required' => json_encode($validate['lastName']['required']),
            'gender.required' => json_encode($validate['gender']['required']),
            'birthDate.required' => json_encode($validate['birthDate']['required']),
            'language.required' => json_encode($validate['language']['required']),
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $registerBio = RegisterBio::where('addressIP', Str::replace('.','',request()->ip())  )->where('userAgent',request()->header('user-agent'))->first();
                if($registerBio){
                    $registerBio->firstName= $data['firstName'];
                    $registerBio->lastName= $data['lastName'];
                    $registerBio->gender= $data['gender'];
                    $registerBio->birthDate= $data['birthDate'];
                }else{
                    $registerBio = new RegisterBio();
                    $registerBio->firstName= $data['firstName'];
                    $registerBio->lastName= $data['lastName'];
                    $registerBio->gender= $data['gender'];
                    $registerBio->birthDate= $data['birthDate'];
                    $registerBio->userAgent =  request()->header('user-agent');
                    $registerBio->addressIP = Str::replace('.','',request()->ip());
                }
                $registerBio->save();
                TempSA::setSA('register_bios',$registerBio->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getRole()
    {
        
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerRole){
            $response['role']= $registerRole->role;
            $response['language']= $registerRole->language;
            return response()->json($response,Response::HTTP_OK);
        }else{
            $lang['ru']= 'Информация  не была найдена по ИП';
            $lang['uz']= 'IP bo`yicha ma`lumot tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
    }
    public function putRole(Request $request)
    {
        $lang['ru']= 'Выберите роль';
        $lang['uz']= 'Rol tanlang';
        $validate['role']['required'] = $lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'role'=>'required',  
            'language'=>'required',    
        ],[
            'role.required' => json_encode($validate['role']['required']),
            'language.required' => json_encode($validate['language']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip())  )->where('userAgent',request()->header('user-agent'))->first();
                if($registerRole){
                    $registerRole->role= $data['role'];
                    $registerRole->addressIP = Str::replace('.','',request()->ip());
                }else{
                    $registerRole = new RegisterRole();
                    $registerRole->role= $data['role'];
                    $registerRole->userAgent =  request()->header('user-agent');
                    $registerRole->addressIP = Str::replace('.','',request()->ip());
                }
                $registerRole->save();
                TempSA::setSA('register_roles',$registerRole->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getcompanyName()
    {
        
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerRole){
            $redirect['redirectTo']='Role';
            return ErrorHelperResponse::returnError($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($registerRole->role=='Employee'){
            $company = RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if($company){
                $response['companyId']=$company->company_id;
                $response['companyName']=$company->companyName;
                $response['language']=$company->language;
                return response()->json($response,Response::HTTP_OK);
            }
        }
        if($registerRole->role=='Company Owner'){
            $company = RegisterСompany::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if($company){
                $response['language']=$company->language;
                $response['companyName']=$company->companyName;
                return response()->json($response,Response::HTTP_OK);
            }
        }
        $response['companyName']='';
        return response()->json($response,Response::HTTP_NOT_FOUND);
    }
    public function putcompanyName(Request $request)
    {
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerRole){
            $redirect['redirectTo']='Role';
            return ErrorHelperResponse::returnError($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($registerRole->role=='Employee'){
            $lang['ru']= 'Компания не выбрана';
            $lang['uz']= 'Kompaniya tanlanmagan';
            $validate['companyId']['required'] = $lang;

            

            $lang['ru']= 'Компания не заполнена';
            $lang['uz']= 'Kompaniya darchasi to`ldirilmagan';
            $validate['companyName']['required'] = $lang;
            $lang['ru']= 'Выберите язык';
            $lang['uz']= 'Tilni tanlang';
            $validate['language']['required'] =$lang;
            $validator = Validator::make($request->all(), [
                
                'companyId'=>'required|numeric',
                'companyName'=>'required',  
                'language'=>'required',    
            ],[
                'companyName.required' => json_encode($validate['companyName']['required']),
                'companyId.required' => json_encode($validate['companyId']['required']),
                'companyId.numeric' => json_encode($validate['companyId']['required']),
                'language.required' => json_encode($validate['language']['required']),
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
            $data = $request->all();
            if(!Company::find($data['companyId'])){
                $validate=null;
                $lang['ru']= 'Аптека  не существует';
                $lang['uz']= 'Dorixona tizimda mavjud emas';
                $validate['message'] =$lang;
                return ErrorHelperResponse::returnError($validate,Response::HTTP_NOT_FOUND);
            }
            try {
                $res = DB::transaction(function() use ($data){
                    $company = RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                    if($company){
                        $company->company_id= $data['companyId'];
                        $company->companyName= $data['companyName'];
                        $company->addressIP = Str::replace('.','',request()->ip());
                    }else{
                        $company = new RegisterСompanyMember();
                        $company->company_id= $data['companyId'];
                        $company->companyName= $data['companyName'];
                        $company->userAgent =  request()->header('user-agent');
                        $company->addressIP = Str::replace('.','',request()->ip());
                    }
                    $company->save();
                    TempSA::setSA('registerсompany_members',$company->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                    $lang['ru']= 'Обновлено';
                    $lang['uz']= 'Yangilangan';
                    $validate['message'] =$lang;
                    return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        if($registerRole->role=='Company Owner'){
            $validate=null;
            $lang['ru']= 'Компания не заполнена';
            $lang['uz']= 'Kompaniya darchasi to`ldirilmagan';
            $validate['companyName']['required'] = $lang;
            $lang['ru']= 'Название компании должна быть не менее 6 значный';
            $lang['uz']= 'Kompaniya nomi 6 ta harfdan ko`p bo`lishi kerak';
            $validate['companyName']['min'] = $lang;
            $lang['ru']= 'Выберите язык';
            $lang['uz']= 'Tilni tanlang';
            $validate['language']['required'] =$lang;
            $validator = Validator::make($request->all(), [
                'companyName'=>'required|min:6',  
                'language'=>'required',    
            ],[
                'companyName.required' => json_encode($validate['companyName']['required']),
                'companyName.min' => json_encode($validate['companyName']['min']),
                'language.required' => json_encode($validate['language']['required']),
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
            
            $data = $request->all();
            if(Company::where('companyName',$data['companyName'])->first()){
                $validate=null;
                $lang['ru']= 'Аптека уже существует';
                $lang['uz']= 'Dorixona tizimda mavjud';
                $validate['message'] =$lang;
                return ErrorHelperResponse::returnError($validate,Response::HTTP_FOUND);
            }
            try {
                $res = DB::transaction(function() use ($data){
                    $company = RegisterСompany::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                    if($company){
                        $company->companyName= $data['companyName'];
                        $company->addressIP = Str::replace('.','',request()->ip());
                    }else{
                        $company = new RegisterСompany();
                        $company->companyName= $data['companyName'];
                        $company->userAgent =  request()->header('user-agent');
                        $company->addressIP = Str::replace('.','',request()->ip());
                    }
                    $company->save();
                    TempSA::setSA('registerсompanies',$company->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                    $lang['ru']= 'Обновлено';
                    $lang['uz']= 'Yangilangan';
                    $validate['message'] =$lang;
                    return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        
        
        
        
    }
    public function putPassword(Request $request)
    {
        $lang['ru']= 'Заполните пароль';
        $lang['uz']= 'Mahfiy so`zni to`ldiring';
        $validate['password']['required'] = $lang;
        $lang['ru']= 'Пароль не должен быть менее 8 символов';
        $lang['uz']= 'Mahfiy so`z kamida 8 ta simboldan iborat bo`lishi lozim';
        $validate['password']['min'] = $lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'password'=>'required|min:8',  
            'language'=>'required',    
        ],[
            'password.required' => json_encode($validate['password']['required']),
            'password.min' => json_encode($validate['password']['min']),
            'language.required' => json_encode($validate['language']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $registerPassword = RegisterPassword::where('addressIP', Str::replace('.','',request()->ip())  )->where('userAgent',request()->header('user-agent'))->first();
                if($registerPassword){
                    $registerPassword->password= Hash::make($data['password']);
                    $registerPassword->addressIP = Str::replace('.','',request()->ip());
                }else{
                    $registerPassword = new RegisterPassword();
                    $registerPassword->password= Hash::make($data['password']);
                    $registerPassword->userAgent =  request()->header('user-agent');
                    $registerPassword->addressIP = Str::replace('.','',request()->ip());
                }
                $registerPassword->save();
                TempSA::setSA('register_roles',$registerPassword->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getEmail()
    {
        
        $registerRole = RegisterEmail::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerRole){
            $response['email']= $registerRole->email;
            $response['language']= $registerRole->language;
            return response()->json($response,Response::HTTP_OK);
        }else{
            $lang['ru']= 'Информация  не была найдена по ИП';
            $lang['uz']= 'IP bo`yicha ma`lumot tizimda topilmadi';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
        }
    }
    public function putEmail(Request $request)
    {
        $lang['ru']= 'Заполните email';
        $lang['uz']= 'Email darchasini to`ldiring';
        $validate['email']['required'] = $lang;
        $lang['ru']= 'Формат email не правилный';
        $lang['uz']= "Elektron pochta formati noto'g'ri";
        $validate['email']['email'] = $lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'email'=>'nullable|email',  
            'language'=>'required',    
        ],[
            // 'email.required' => json_encode($validate['email']['required']),
            'email.email' => json_encode($validate['email']['email']),

            'language.required' => json_encode($validate['language']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if(Email::where('email',$data['email'])->first()){
            $validate = null;
            $lang['ru']= 'Email уже существует';
            $lang['uz']= 'Email tizimda mavjud';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate,Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data){
                
                $registerEmail = RegisterEmail::where('addressIP', Str::replace('.','',request()->ip())  )->where('userAgent',request()->header('user-agent'))->first();
                if($registerEmail){
                    $registerEmail->email= $data['email'];
                    $registerEmail->addressIP = Str::replace('.','',request()->ip());
                }else{
                    $registerEmail = new RegisterEmail();
                    $registerEmail->email= $data['email'];
                    $registerEmail->userAgent =  request()->header('user-agent');
                    $registerEmail->addressIP = Str::replace('.','',request()->ip());
                }
                $registerEmail->save();
                TempSA::setSA('register_emails',$registerEmail->id, $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql6');
                $lang['ru']= 'Обновлено';
                $lang['uz']= 'Yangilangan';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
             'chat_id' =>  env('TG_CHAT_ID'),
             'text' => 'Access'.PHP_EOL.''.PHP_EOL.'Code: '.$phoneNum.''.PHP_EOL.'PhoneNumber: '.$phoneNumber.''.PHP_EOL.'From: '.$from.''.PHP_EOL.'Time: '.$status_dat
         ];
 
       $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );


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
    
    public function sendSms(Request $request)
    {
        $data = $request->all();
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerPhone){
            $redirect['redirectTo']='Register';
            return response()->json($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }
        if(Phonebook::where('phoneNumber',$registerPhone->phoneNumber)->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerRole){
            $redirect['redirectTo']='Role';
            return response()->json($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }

        if($registerRole->role=='Company Owner')
        {
            $registerСompany = RegisterСompany::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if(!$registerСompany){
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            if(Company::where('companyName',$registerСompany->companyName)->first()){
                $lang['ru']= 'Компания уже существует';
                $lang['uz']= 'Kompaniya tizimda mavjud';
                $validate['message'] =$lang;
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            if(!RegisterСompanyAddress::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first()){
                $validate['message']='Company address not saved';
                $validate['redirectTo']='CompanyAddress';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
        }
        if($registerRole->role=='Employee'){
            if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                if(!is_int((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш.';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
                if(!Company::find((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }else{
                $registerСompany = RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                if(!$registerСompany){
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
    
                if(!Company::find($registerСompany->company_id)){
                    $lang['ru']= 'Компания не существует';
                    $lang['uz']= 'Kompaniya tizimda mavjud emas';
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }
        }
        $registerEmail = RegisterEmail::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerEmail){
            if(Email::where('email',$registerEmail->email)->first()){
                $lang['ru']= 'Почта существует';
                $lang['uz']= 'Email tizimda mavjud';
                $validate['message'] =$lang;
                $validate['redirectTo']='EmailSetView';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        $bio = RegisterBio::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$bio){
            $validate['redirectTo']='UserInfo';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        
        try {
            $res = DB::transaction(function() use ($registerPhone){
                $random = mt_rand(100000, 1000000);
                $registerPhone->confirm_code = $random;
                $registerPhone->save();
                $json  =  [
                    'mobile_phone'=>$registerPhone->phoneNumber,
                    'message'=>'PharmIQ Confirmation code: '.$registerPhone->confirm_code,
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
                        $this->saveSentSms($registerPhone->phoneNumber, 'eskiz','webRegister');
                        $lang['ru']= 'СМС отправлено';
                        $lang['uz']= 'SMS yuborildi';
                        $validate['message'] =$lang;
                        return response()->json(json_encode($validate['message']),Response::HTTP_OK);
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
                                $this->saveSentSms($registerPhone->phoneNumber, 'eskiz','webRegister');
                                $lang['ru']= 'СМС отправлено';
                                $lang['uz']= 'SMS yuborildi';
                                $validate['message'] =$lang;
                                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                            }
                        }
                        
                    }
                       
                }
                $lang['ru']= 'CМС не отправлено';
                $lang['uz']= 'SMS yuborilmadi';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $lang['ru']= 'Cмс отправлено false';
        $lang['uz']= 'SMS yuborildi false';
        $validate['message'] =$lang;
        return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
    }
    public function loginOnly(Request $request)
    {
        $lang['ru']= 'Заполните пустые поля';
        $lang['uz']= 'Bo`sh darchalarni to`ldiring';
        $validate['code']['required'] = $lang;
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
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
        }
        if($phonebook->random !=$fields['code'] ){
            $validate=null;
            $lang['ru']= 'Код не правильно прописан';
            $lang['uz']= 'Notog`ri kiritilgan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
            return response()->json($validate,Response::HTTP_NOT_FOUND);
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
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
       
        
        $response =[ 'user'=>$user, 'token'=>$token,'hasAccess'=>$this->getAccess($user) ];
        return response($response,201);
    }
    public function sendSmsOnlyLogin(Request $request)
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
        $data = $request->all();
        
        $phoneNumber = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if(!$phoneNumber){
            $lang['ru']= 'Номер  не существует';
            $lang['uz']= 'Raqam tizimda mavjud emas';
            $validate['message'] =$lang;
            $validate['redirectTo']='Login';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($phoneNumber->randomTime!=null){
            $start = \Carbon\Carbon::parse($phoneNumber->randomTime);
            $end = \Carbon\Carbon::now();
            $diffminut = $start->diff($end)->format('%I');
            if((int)$diffminut<=3){
                $lang['ru']= 'Переотправить пароль можно только через 3х минут';
                $lang['uz']= "Tasdiqlash kodini faqat 3 daqiqadan so'ng qayta yuborishingiz mumkin";
                $validate['message'] =$lang;
                return ErrorHelperResponse::returnError($validate['message'],Response::HTTP_NOT_FOUND);
            }
        }
        try {
            $res = DB::transaction(function() use ($phoneNumber){
                $random = mt_rand(100000, 1000000);
                $phoneNumber->random = $random;
                $phoneNumber->randomTime = Carbon::now();
                $phoneNumber->save();
                $json  =  [
                    'mobile_phone'=>$phoneNumber->phoneNumber,
                    'message'=>'PharmIQ Confirmation code: '.$phoneNumber->random,
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
                        $this->saveSentSms($phoneNumber->phoneNumber, 'eskiz','webRegister');
                        $lang['ru']= 'СМС отправлено';
                        $lang['uz']= 'SMS yuborildi';
                        $validate['message'] =$lang;
                        return response()->json(json_encode($validate['message']),Response::HTTP_OK);
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
                                $this->saveSentSms($phoneNumber->phoneNumber, 'eskiz','webRegister');
                                $lang['ru']= 'СМС отправлено';
                                $lang['uz']= 'SMS yuborildi';
                                $validate['message'] =$lang;
                                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                            }
                        }
                        
                    }
                       
                }
                $lang['ru']= 'CМС не отправлено';
                $lang['uz']= 'SMS yuborilmadi';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $lang['ru']= 'Cмс отправлено false';
        $lang['uz']= 'SMS yuborildi false';
        $validate['message'] =$lang;
        return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
    }
    public function sendSmsOnly(Request $request)
    {
        $data = $request->all();
        
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerPhone){
            $redirect['redirectTo']='Register';
            return response()->json($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }
        if(Phonebook::where('phoneNumber',$registerPhone->phoneNumber)->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        try {
            $res = DB::transaction(function() use ($registerPhone){
                $random = mt_rand(100000, 1000000);
                $registerPhone->confirm_code = $random;
                $registerPhone->save();
                $json  =  [
                    'mobile_phone'=>$registerPhone->phoneNumber,
                    'message'=>'PharmIQ Confirmation code: '.$registerPhone->confirm_code,
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
                        $this->saveSentSms($registerPhone->phoneNumber, 'eskiz','webRegister');
                        $lang['ru']= 'СМС отправлено';
                        $lang['uz']= 'SMS yuborildi';
                        $validate['message'] =$lang;
                        return response()->json(json_encode($validate['message']),Response::HTTP_OK);
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
                                $this->saveSentSms($registerPhone->phoneNumber, 'eskiz','webRegister');
                                $lang['ru']= 'СМС отправлено';
                                $lang['uz']= 'SMS yuborildi';
                                $validate['message'] =$lang;
                                return response()->json(json_encode($validate['message']),Response::HTTP_OK);
                            }
                        }
                        
                    }
                       
                }
                $lang['ru']= 'CМС не отправлено';
                $lang['uz']= 'SMS yuborilmadi';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $lang['ru']= 'Cмс отправлено false';
        $lang['uz']= 'SMS yuborildi false';
        $validate['message'] =$lang;
        return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
    }
    public function confirmCodeOnly(Request $request)
    {
        $lang['ru']= 'Заполните пустые поля';
        $lang['uz']= 'Bo`sh darchalarni to`ldiring';
        $validate['code']['required'] = $lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'code'=>'required|digits:6', 
        ],[
            'code.required' => json_encode($validate['code']['required']),
            'code.digits' => json_encode($validate['code']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerPhone){
            $validate=null;
            $lang['ru']= 'Номер отправителя не был найдет в базе';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($registerPhone->confirm_code !=$data['code'] ){
            $validate=null;
            $lang['ru']= 'Код не правильно прописан';
            $lang['uz']= 'Notog`ri kiritilgan';
            $validate['message'] =$lang;
            return response()->json($validate,Response::HTTP_NOT_FOUND);
        }
        $start = \Carbon\Carbon::parse($registerPhone->updated_at);
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
        $registerPhone->confirm_code = 000000;
        $registerPhone->save();
        $lang['ru']= 'Номер подтверждён';
        $lang['uz']= 'Raqam tasdiqlandi';
        $validate['message'] =$lang;
        return response()->json($validate['message'],Response::HTTP_OK);
        
    }
    public function newRegisterOnly(Request $request)
    {
        $data = $request->all();
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerPhone){
            $validate=null;
            $lang['ru']= 'Номер отправителя не был найдет в базе';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        if($registerPhone->confirm_code!=00000){
            
            $lang['ru']= 'Номер не подтверждён';
            $lang['uz']= 'Raqam tasdiqlanmagan';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        
        if(Phonebook::where('phoneNumber',$registerPhone->phoneNumber)->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerRole){
            $redirect['redirectTo']='Role';
            return response()->json($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }

        if($registerRole->role=='Company Owner')
        {
            $registerСompany = RegisterСompany::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if(!$registerСompany){
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            if(Company::where('companyName',$registerСompany->companyName)->first()){
                $lang['ru']= 'Компания уже существует';
                $lang['uz']= 'Kompaniya tizimda mavjud';
                $validate['message'] =$lang;
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            $registerCompanyAddress =RegisterСompanyAddress::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if(!$registerCompanyAddress){
                $validate['message']='Company address not saved';
                $validate['redirectTo']='CompanyAddress';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
        }
        if($registerRole->role=='Employee'){
            $registerCompanyAddress = null;
            $registerСompany = null;
            if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                if(!is_int((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш.';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
                if(!Company::find((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }else{
                $registerСompany = RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                if(!$registerСompany){
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
    
                if(!Company::find($registerСompany->company_id)){
                    $lang['ru']= 'Компания не существует';
                    $lang['uz']= 'Kompaniya tizimda mavjud emas';
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }
        }
        

        $bio = RegisterBio::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$bio){
            $validate['redirectTo']='UserInfo';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
      
        $checkfirstPhone = Phonebook::where('phoneNumber', $registerPhone->phoneNumber)->first();
        if($checkfirstPhone){
            $lang['ru']= 'Такой номер уже существует';
            $lang['uz']= 'Bunday raqam sistemada mavjud';
            $responseArr['message']=$lang;
            $validate['redirectTo']='Register';
            return response()->json($responseArr, Response::HTTP_MOVED_PERMANENTLY);
        }
        
        
        
        $validate=null;
        
        try {
            $res = DB::transaction(function () use ($data, $registerRole,$bio,$registerPhone,$registerСompany, $registerCompanyAddress) {
                        
                if($registerRole->role=='Company Owner'){
                    if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null'){
                        // $usersc = User::where('hrid',$data['scout_id'] )->where('role','Scout')->first();
                        // if(!$usersc){
                        //     $responseArr['error']=true;
                        //     $responseArr['message'] ='Scout with given id not found';
                        //     return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        // }
                        // $this->userScout = $usersc;
                    }
                    $user = new  User();
                    $user->firstName= $bio->firstName;
                    $user->lastName= $bio->lastName;
                    $user->gender= $bio->gender;
                    $user->birthDate= $bio->birthDate;
                    $user->role = $registerRole->role;
                    $user->hrid = hrtime(true);
                    $user->save();
                    $userhistory =new UserBioHistoires();
                    $userhistory->saveModel($user, $data);
                    



                    $userlanghistory = new UserLangHistories();
                    $userlanghistory->saveRegisterModel($user->id, $data);
                    
                    $phonebook = new Phonebook();
                    $phonebook->user_id = $user->id;
                    $phonebook->phoneNumber = $registerPhone->phoneNumber;
                    $phonebook->random = mt_rand(100000, 1000000);
                    $phonebook->randomTime = null;
                    $phonebook->status = 1;
                    $phonebook->save();
                    $numberHistory = new PhonebookHistories();
                    $numberHistory->saveModel($phonebook, $data, 0);
                    

                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!=''){
                        $userref = User::where('hrid', $data['ref_id'])->first();
                        
                        if($userref){
                            $iqc =  Iqc::where('user_id', $userref->id)->first();
                            if($iqc){
                                $iqc->updateModel($data, 5,1,'ref link', $user->id);
                            }else{
                                $iqc = new Iqc();
                                $iqc->saveModel($data, $userref->id, 5,1,'ref link',  $user->id);
                            }
                        }
                        
                    }



                    
                    $company = new Company();
                    $company->companyName=  $registerСompany->companyName;
                    $company->user_id=  $user->id;
                    $company->save();
                    $companyHistory = new CompanyHistories();
                    $companyHistory->saveModel($company, $data, 0);
                    $companyAdd= new CompanyAddress();
                    $companyAdd->country_id = 1;
                    $companyAdd->company_id = $company->id;
                    $companyAdd->addressType = $registerCompanyAddress->addressType;
                    $companyAdd->city_id = $registerCompanyAddress->city_id;
                    $companyAdd->region_id = $registerCompanyAddress->region_id;
                    $companyAdd->longitude = $registerCompanyAddress->longitude;
                    $companyAdd->latitude = $registerCompanyAddress->latitude;
                    $companyAdd->addressline1 = $registerCompanyAddress->addressline;
                    $companyAdd->save();
                    $companyAdressHistory = new CompanyAddressHistories();
                    $companyAdressHistory->saveModel($companyAdd, $data);

                }else{
                    if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                        $companyRef = Company::find($data['ref_company']);
                        if(!$companyRef){
                            $lang['ru']= 'Компания  по реф линк не найдена';
                            $lang['uz']= 'Ref link  Kompaniyasi tizimda topilmadi';
                            $responseArr['message']=$lang;
                            $validate['redirectTo']='Login';
                            return response()->json($responseArr, Response::HTTP_MOVED_PERMANENTLY);
                        }
                    }
                    $user = new  User();
                    $user->firstName= $bio->firstName;
                    $user->lastName= $bio->lastName;
                    $user->gender= $bio->gender;
                    $user->birthDate= $bio->birthDate;
                    $user->role = $registerRole->role;
                    $user->hrid = hrtime(true);
                    $user->save();
                    $userhistory =new UserBioHistoires();
                    $userhistory->saveModel($user, $data);
                    



                    $userlanghistory = new UserLangHistories();
                    $userlanghistory->saveRegisterModel($user->id, $data);
                    
                    $phonebook = new Phonebook();
                    $phonebook->user_id = $user->id;
                    $phonebook->phoneNumber = $registerPhone->phoneNumber;
                    $phonebook->random = mt_rand(100000, 1000000);
                    $phonebook->randomTime = null;
                    $phonebook->status = 1;
                    $phonebook->save();
                    $numberHistory = new PhonebookHistories();
                    $numberHistory->saveModel($phonebook, $data, 0);
                    

                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!=''){
                        $userref = User::where('hrid', $data['ref_id'])->first();
                        
                        if($userref){
                            $iqc =  Iqc::where('user_id', $userref->id)->first();
                            if($iqc){
                                $iqc->updateModel($data, 5,1,'ref link', $user->id);
                            }else{
                                $iqc = new Iqc();
                                $iqc->saveModel($data, $userref->id, 5,1,'ref link',  $user->id);
                            }
                        }
                        
                    }


                    
                    
                    
                    if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                        $companyMembers = new CompanyMembers();
                        $companyMembers->member_id = $user->id;
                        $companyMembers->memberStatus = 1;
                        $companyMembers->company_id = $data['ref_company'];
                        $companyMembers->save();
                        $companyMembersHistory = new CompanyMemberHistories();
                        $companyMembersHistory->saveModel($companyMembers, $data);
                        $iqc = new Iqc();
                        $iqc->saveModel($data, $user->id, 0,1,'ref company',  $data['ref_company']);
                        
                    }else{
                        $companyMembers = new CompanyMembers();
                        $companyMembers->member_id = $user->id;
                        $companyMembers->company_id = $registerСompany->company_id;
                        $companyMembers->memberStatus = 1;
                        $companyMembers->save();
                        $companyMembersHistory = new CompanyMemberHistories();
                        $companyMembersHistory->saveModel($companyMembers, $data);
                    }
                    
                }
                $lang['ru']= 'Регистрация прошла успешно';
                $lang['uz']= "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi";
                $validate['message'] =$lang;
                return response()->json($validate['message'],Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function confirmCode(Request $request)
    {
        $lang['ru']= 'Заполните пустые поля';
        $lang['uz']= 'Bo`sh darchalarni to`ldiring';
        $validate['code']['required'] = $lang;
        $lang['ru']= 'Выберите язык';
        $lang['uz']= 'Tilni tanlang';
        $validate['language']['required'] =$lang;
        $validator = Validator::make($request->all(), [
            'code'=>'required|digits:6',  
            'language'=>'required',    
        ],[
            'code.required' => json_encode($validate['code']['required']),
            'code.digits' => json_encode($validate['code']['required']),
            'language.required' => json_encode($validate['language']['required']),
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $registerPhone = RegisterPhone::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerPhone){
            $validate=null;
            $lang['ru']= 'Номер отправителя не был найдет в базе';
            $lang['uz']= 'Raqam tizimda topilmadi';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        
        if(Phonebook::where('phoneNumber',$registerPhone->phoneNumber)->first()){
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            $validate['message'] =$lang;
            $validate['redirectTo']='Register';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        $registerRole = RegisterRole::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$registerRole){
            $redirect['redirectTo']='Role';
            return response()->json($redirect,Response::HTTP_MOVED_PERMANENTLY);
        }

        if($registerRole->role=='Company Owner')
        {
            $registerСompany = RegisterСompany::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if(!$registerСompany){
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            if(Company::where('companyName',$registerСompany->companyName)->first()){
                $lang['ru']= 'Компания уже существует';
                $lang['uz']= 'Kompaniya tizimda mavjud';
                $validate['message'] =$lang;
                $validate['redirectTo']='Company';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
            $registerCompanyAddress =RegisterСompanyAddress::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
            if(!$registerCompanyAddress){
                $validate['message']='Company address not saved';
                $validate['redirectTo']='CompanyAddress';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
        }
        if($registerRole->role=='Employee'){
            $registerCompanyAddress = null;
            $registerСompany = null;
            if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                if(!is_int((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш.';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
                if(!Company::find((int)$data['ref_company'])){
                    $lang['ru']= 'Реферальный линк компании  неправильный, удалите кэш';
                    $lang['uz']= "Kompaniya havolasi noto'g'ri, keshni tozalang";
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Login';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }else{
                $registerСompany = RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
                if(!$registerСompany){
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
    
                if(!Company::find($registerСompany->company_id)){
                    $lang['ru']= 'Компания не существует';
                    $lang['uz']= 'Kompaniya tizimda mavjud emas';
                    $validate['message'] =$lang;
                    $validate['redirectTo']='Company';
                    return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
                }
            }
        }
        $registerEmail = RegisterEmail::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if($registerEmail){
            if(Email::where('email',$registerEmail->email)->first()){
                $lang['ru']= 'Почта существует';
                $lang['uz']= 'Email tizimda mavjud';
                $validate['message'] =$lang;
                $validate['redirectTo']='EmailSetView';
                return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        $bio = RegisterBio::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$bio){
            $validate['redirectTo']='UserInfo';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        $passwordRegister = RegisterPassword::where('addressIP', Str::replace('.','',request()->ip()))->where('userAgent',request()->header('user-agent'))->first();
        if(!$passwordRegister){
            $validate['redirectTo']='PasswordSetView';
            return response()->json($validate,Response::HTTP_MOVED_PERMANENTLY);
        }
        $checkfirstPhone = Phonebook::where('phoneNumber', $registerPhone->phoneNumber)->first();
        if($checkfirstPhone){
            $lang['ru']= 'Такой номер уже существует';
            $lang['uz']= 'Bunday raqam sistemada mavjud';
            $responseArr['message']=$lang;
            $validate['redirectTo']='Register';
            return response()->json($responseArr, Response::HTTP_MOVED_PERMANENTLY);
        }
        
        
        if($registerPhone->confirm_code !=$data['code'] ){
            $validate=null;
            $lang['ru']= 'Код не правильно прописан';
            $lang['uz']= 'Notog`ri kiritilgan';
            $validate['message'] =$lang;
            return response()->json($validate,Response::HTTP_NOT_FOUND);
        }
        $registerPhone->confirm_code = null;
        $registerPhone->save();
        $validate=null;
        
        try {
            $res = DB::transaction(function () use ($data, $registerRole,$bio,$registerPhone,$passwordRegister, $registerEmail,$registerСompany, $registerCompanyAddress) {
                        
                if($registerRole->role=='Company Owner'){
                    if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null'){
                        // $usersc = User::where('hrid',$data['scout_id'] )->where('role','Scout')->first();
                        // if(!$usersc){
                        //     $responseArr['error']=true;
                        //     $responseArr['message'] ='Scout with given id not found';
                        //     return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        // }
                        // $this->userScout = $usersc;
                    }
                    $user = new  User();
                    $user->firstName= $bio->firstName;
                    $user->lastName= $bio->lastName;
                    $user->gender= $bio->gender;
                    $user->birthDate= $bio->birthDate;
                    $user->role = $registerRole->role;
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
                    

                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!=''){
                        $userref = User::where('hrid', $data['ref_id'])->first();
                        
                        if($userref){
                            $iqc =  Iqc::where('user_id', $userref->id)->first();
                            if($iqc){
                                $iqc->updateModel($data, 5,1,'ref link', $user->id);
                            }else{
                                $iqc = new Iqc();
                                $iqc->saveModel($data, $userref->id, 5,1,'ref link',  $user->id);
                            }
                        }
                        
                    }



                    $password = Password::where('user_id', $user->id)->first();
                    if($password){
                        $password->user_id = $user->id;
                        $password->passwd = $passwordRegister->password;
                        $password->save();
                        $passwdHistories = new PasswdHistories();
                        $passwdHistories->saveModel($password,$data, 0);
                    }else{
                        $password = new Password();
                        $password->user_id = $user->id;
                        $password->passwd = $passwordRegister->password;
                        $password->save();
                        $passwdHistories = new PasswdHistories();
                        $passwdHistories->saveModel($password,$data, 1);
                    }
                    if($registerEmail && $registerEmail->email){
                        $email = new Email();
                        $email->user_id = $user->id;
                        $email->email = $registerEmail->email;
                        $email->save();
                        $emailHistories = new EmailHistories();
                        $emailHistories->user_id = $user->id;
                        $emailHistories->email = $registerEmail->email;
                        $emailHistories->save();
                        StandardAttributes::setSA('email_histories',$emailHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                    }
                    
                    $company = new Company();
                    $company->companyName=  $registerСompany->companyName;
                    $company->user_id=  $user->id;
                    $company->save();
                    $companyHistory = new CompanyHistories();
                    $companyHistory->saveModel($company, $data, 0);
                    $companyAdd= new CompanyAddress();
                    $companyAdd->country_id = 1;
                    $companyAdd->company_id = $company->id;
                    $companyAdd->addressType = $registerCompanyAddress->addressType;
                    $companyAdd->city_id = $registerCompanyAddress->city_id;
                    $companyAdd->region_id = $registerCompanyAddress->region_id;
                    $companyAdd->longitude = $registerCompanyAddress->longitude;
                    $companyAdd->latitude = $registerCompanyAddress->latitude;
                    $companyAdd->addressline1 = $registerCompanyAddress->addressline;
                    $companyAdd->save();
                    $companyAdressHistory = new CompanyAddressHistories();
                    $companyAdressHistory->saveModel($companyAdd, $data);

                }else{
                    if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                        $companyRef = Company::find($data['ref_company']);
                        if(!$companyRef){
                            $lang['ru']= 'Компания  по реф линк не найдена';
                            $lang['uz']= 'Ref link  Kompaniyasi tizimda topilmadi';
                            $responseArr['message']=$lang;
                            $validate['redirectTo']='Login';
                            return response()->json($responseArr, Response::HTTP_MOVED_PERMANENTLY);
                        }
                    }
                    $user = new  User();
                    $user->firstName= $bio->firstName;
                    $user->lastName= $bio->lastName;
                    $user->gender= $bio->gender;
                    $user->birthDate= $bio->birthDate;
                    $user->role = $registerRole->role;
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
                    

                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!=''){
                        $userref = User::where('hrid', $data['ref_id'])->first();
                        
                        if($userref){
                            $iqc =  Iqc::where('user_id', $userref->id)->first();
                            if($iqc){
                                $iqc->updateModel($data, 5,1,'ref link', $user->id);
                            }else{
                                $iqc = new Iqc();
                                $iqc->saveModel($data, $userref->id, 5,1,'ref link',  $user->id);
                            }
                        }
                        
                    }


                    $password = Password::where('user_id', $user->id)->first();
                    if($password){
                        $password->user_id = $user->id;
                        $password->passwd = $passwordRegister->password;
                        $password->save();
                        $passwdHistories = new PasswdHistories();
                        $passwdHistories->saveModel($password,$data, 0);
                    }else{
                        $password = new Password();
                        $password->user_id = $user->id;
                        $password->passwd = $passwordRegister->password;
                        $password->save();
                        $passwdHistories = new PasswdHistories();
                        $passwdHistories->saveModel($password,$data, 1);
                    }
                    if($registerEmail && $registerEmail->email){
                        $email = new Email();
                        $email->user_id = $user->id;
                        $email->email = $registerEmail->email;
                        $email->save();
                        $emailHistories = new EmailHistories();
                        $emailHistories->user_id = $user->id;
                        $emailHistories->email = $registerEmail->email;
                        $emailHistories->save();
                        StandardAttributes::setSA('email_histories',$emailHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                    }
                    
                    
                    if(isset($data['ref_company']) && $data['ref_company'] && $data['ref_company']!=''){
                        $companyMembers = new CompanyMembers();
                        $companyMembers->member_id = $user->id;
                        $companyMembers->memberStatus = 1;
                        $companyMembers->company_id = $data['ref_company'];
                        $companyMembers->save();
                        $companyMembersHistory = new CompanyMemberHistories();
                        $companyMembersHistory->saveModel($companyMembers, $data);
                        $iqc = new Iqc();
                        $iqc->saveModel($data, $user->id, 0,1,'ref company',  $data['ref_company']);
                        
                    }else{
                        $companyMembers = new CompanyMembers();
                        $companyMembers->member_id = $user->id;
                        $companyMembers->company_id = $registerСompany->company_id;
                        $companyMembers->memberStatus = 1;
                        $companyMembers->save();
                        $companyMembersHistory = new CompanyMemberHistories();
                        $companyMembersHistory->saveModel($companyMembers, $data);
                    }
                    
                }
                $lang['ru']= 'Номер подтверждён';
                $lang['uz']= 'Raqam tasdiqlandi';
                $validate['message'] =$lang;
                return response()->json($validate['message'],Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }



        
        /// save to main database
    }
    public function mobileUserCheck()
    {
        $company = false;
        if(auth()->user()->role=='Company Owner'){
            $company = Company::with('companyadress')->where('user_id', auth()->user()->id)->first();
        }
        if(auth()->user()->role=='Employee'){
            $companyMembers = CompanyMembers::with('company')->where('member_id', auth()->user()->id)->first();
            if($companyMembers){
                $company = $companyMembers->company;
            }
        }
        $response =[ 'user'=>auth()->user(), 'company'=>$company,'iqc'=> Iqc::where('user_id', auth()->user()->id)->first(),'hasAccess'=>'mobile'];
        return response($response,201);
    }
    public function mobileLogin(Request $request)
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
        config(['sanctum.expiration' => 90*24*60]);
        $token = $user->createToken('myapptoken',['*'], Carbon::now()->addDays(90))->plainTextToken;
        $response =[ 'user'=>$user, 'token'=>$token,'hasAccess'=>'mobile'];
        return response($response,201);
    }
    
}
