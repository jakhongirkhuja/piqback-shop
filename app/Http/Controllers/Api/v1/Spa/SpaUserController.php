<?php
namespace App\Http\Controllers\Api\v1\Spa;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamLists;
use App\Models\Course\Course;
use App\Models\Course\CourseLog;
use App\Models\Email;
use App\Models\General;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\GroupMemberLists;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Lottery\Lottery;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\Phonebook\Phonebook as PhonebookPhonebook;
use App\Models\PhonebookHistories;
use App\Models\PhonebookOperator;
use App\Models\Promocode\Promocode;
use App\Models\Promocode\PromocodeLog;
use App\Models\Quizzes\Quizz;
use App\Models\StoreLatest\Store;
use App\Models\User;
use Carbon\Carbon;
use COM;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SpaUserController extends Controller
{
    public function userQrcodeAppearCheck() {

        $lists = ['998946121812','998935355445','998935776480','998933856325','998909539988','998991110072','998946321844','998909632147','998900276969',
        '998971317911','998947047788','998997162838','998913396787','998909757389','998978420017','998909319392','998946593268','998935134636','998936265692','998978884698','998994642968','998935288535','998934625544','998991974838','998970049134','998909587978','998917981310','998946450038','998907882382','998971814101','998990771125','998946965696','998909351837','998974008048','998998257712','998903249096','998946782399','998770691101','998946831445','998998813124','998900380321','998900224050','998950488472','998917794686','998903219940','998909515348','998909288719','998903507078','998909869892','998914652881','998998928090','998994943099','998977570437','998909865122','998930911578','998946380036','998971021555','998945537553','998909040920','998943664475','998945590333','998881875152','998900117550','998997968999','998998317276','998903986970','998946913537','998900099737','998905010909','998998526675','998990267646','998946385901','998917731197','998974777703','998946205418','998990222420','998903491071','998909601331','998909983522','998909748216','998977083770','998944524333','998909207194','998906805515','998946380075','998909640930','998940746044','998935121222'
    ];
        $phoneNumber= Phonebook::where('user_id',  auth()->user()->id)->first();
        if($phoneNumber){
            foreach ($lists as $list) {
                if($list==$phoneNumber->phoneNumber) return response(true, Response::HTTP_OK);
            }
        }
        return response(false, Response::HTTP_OK);
    }
    public function userInfo()
    {
        
        $user = User::with('phonebook')->find(auth()->user()->id);
        $iqc= Iqc::where('user_id', $user->id);
        $iqcTransactions = IqcTransaction::where('user_id',$user->id)->get();
        $company = [];
        if($user->role=='Company Owner'){
            $company = Company::with('companyadress')->where('user_id', $user->id)->first();
        }
        $response['user'] = $user;
        $response['iqc'] = $iqc;
        $response['iqcTransactions'] = $iqcTransactions;
        $response['company'] = $company;
        return response($response, Response::HTTP_OK);
    }
    public function userTopIqcStat()
    {
        $user = auth()->user();
        $userIds = null;
        if($user->role=='Company Owner'){
            $company = Company::with('companymembers')->where('user_id', $user->id)->first();
            if($company && $company->companymembers->count()>=5){
                $userIds = $company->companymembers->pluck('member_id')->toArray();
            }
        }elseif($user->role=='Employee'){
            $companyMembers = CompanyMembers::select('company_id')->where('member_id', $user->id)->first();
            if($companyMembers){
                $userIds = CompanyMembers::where('company_id', $companyMembers->company_id)
                ->pluck('member_id')
                ->toArray();
            }
        }
        $requestValue = request()->count && request()->count>0? request()->count : null;
        $iqcTop = [];
        if($userIds!=null && count($userIds)>=5){
            $iqces= IqcTransaction::where('serviceName','quiz')->whereIn('user_id',$userIds)->where('valueType','1')->get()->groupby('user_id')->map(function ($item) {
                return $item->sum('value');
            })->sortDesc()->take($requestValue? $requestValue : 10);
        }else{
            $iqces= IqcTransaction::where('serviceName','quiz')->where('valueType','1')->get()->groupby('user_id')->map(function ($item) {
                return $item->sum('value');
            })->sortDesc()->take($requestValue? $requestValue : 10);
        }
        
        
      
        // dd($iqces);
        // $iqces = Iqc::orderby('amountofIQC','desc')->take(10)->get();
        if(count($iqces)>0){
            foreach ($iqces as $key => $iqc) {
                
                $user = User::with('phonebook')->find($key);
                $iqc2 = Iqc::where('user_id',$user->id)->first();
                if($user->role=='Company Owner'){
                    
                    $company = Company::where('user_id', $user->id)->first();
                    if($company){
                        $temp['user']  = $user->firstName.' '.mb_substr($user->lastName, 0, 1, 'UTF-8').'.';
                        $temp['company'] = $company->companyName;
                        $temp['iqc'] = $iqc;
                        $temp['iqc2'] = $iqc2->amountofIQC;
                        $iqcTop[] = $temp;
                    }
                }elseif ($user->role=='Employee') {
                    $companyMembers = CompanyMembers::with('company')->where('member_id', $user->id)->first();
                   
                    if($companyMembers && $companyMembers->company){
                        $temp['user']  = $user->firstName.' '. mb_substr($user->lastName, 0, 1, 'UTF-8').'.';
                        $temp['company'] = $companyMembers->company->companyName;
                        $temp['iqc'] = $iqc;
                        $temp['iqc2'] = $iqc2->amountofIQC;
                        $iqcTop[] = $temp;
                    }
                }
                
              
                if(count($iqcTop)>($requestValue? ($requestValue-1) : 4)){
                    break;
                }
            }
        }
        $newString = mb_convert_encoding($iqcTop, "UTF-8");
        return response()->json($newString,200);
    }
    public function userAddIqc(Request $request){
        $validator = Validator::make($request->all(), [
            'iqc'=>'required',
            'quest_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
       

        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data ){
                $iqc = Iqc::where('user_id', auth()->user()->id)->first();
                if($iqc){
                   $iqc->updateStoreModel($data,(int)$data['iqc'],1,'quest',$data['quest_id'] );
                }else{
                    $iqc = new Iqc();
                    $iqc->saveModel($data, auth()->user()->id, (int)$data['iqc'],1,'quest',$data['quest_id']);
                }
                $res['ru']='Успешно';
                $res['uz']="Muvaffaqiyatli";
                return response()->json($res, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response('Success', Response::HTTP_OK);
    }
    public function userTransactions()
    {
        $iqcTransactions = IqcTransaction::where('user_id',auth()->user()->id)->where('value','!=',0)->where('serviceName','!=','removed')->orderby('created_at','desc')->get();
        $response['iqcTransactions'] = $iqcTransactions;
        $response['iqcTransactionsReferral'] = IqcTransaction::where('user_id',auth()->user()->id)->where('value','!=',0)->where('serviceName','ref link')->count();
        $response['iqcTransactionsReferralCompany'] = IqcTransaction::where('user_id',auth()->user()->id)->where('value','!=',0)->where('serviceName','ref company')->count();
        return response($response, Response::HTTP_OK);
    }
    public function userInfoChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName'=>'required',
            'lastName'=>'required',
            'birthDate'=>'required',
            'gender'=>'required|min:0|max:1',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
       
        $user = User::find(auth()->user()->id);
        if($user){
            $data = $request->all();
            $user->firstName =$data['firstName'];
            $user->lastName =$data['lastName'];
            $user->birthDate =  \Carbon\Carbon::createFromFormat('d/m/Y', $data['birthDate'])->format('Y-m-d');
            $user->gender = $data['gender'];
            $user->save();
        }
        return response('Success', Response::HTTP_OK);
    }
    public function UserPhoneNumberChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber'=>'required|size:12',
           
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $phoneNumber = Phonebook::where('user_id','!=',auth()->user()->id)->where('phoneNumber',$data['phoneNumber'])->first();
        if($phoneNumber){
            $lang['en']= 'PhoneNumber exist';
            $lang['ru']= 'Номер существует';
            $lang['uz']= 'Raqam tizimda mavjud';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
        }
        $phoneBook = Phonebook::where('user_id',auth()->user()->id)->first();
        if($phoneBook){
            try {
                $res =DB::transaction(function () use ($data,$phoneBook){
                    $phoneBook->updateModel($data);
                    return response()->json('Updated', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }
        $lang['en']= 'PhoneNumber not found';
        $lang['ru']= 'Номер не существует';
        $lang['uz']= 'Raqam tizimda mavjud emas';
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
    }
    public function UserPasswordChange(Request $request)
    {
       
       $validator = Validator::make($request->all(), [
        'password'=>'required|confirmed|min:5',
        'password_old'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
       $password = Password::where('user_id',auth()->user()->id)->first();
       $data = $request->all();
       if($password){
            
            if(Hash::check($data['password_old'], $password->passwd)){
                try {
                    $res =DB::transaction(function () use ($data,$password){
                        $password->updateModel($data);
                        return response()->json('Updated', Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                
            }else{
                $lang['en']= 'Old Password is not same';
                $lang['ru']= 'Старый пароль не подходит';
                $lang['uz']= "So'ngi mahfiy so'z bir xil emas";
                return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
            }
            
        }else{
            try {
                $res =DB::transaction(function () use ($data){
                    $password = new Password();
                    $password->saveModel(auth()->user()->id, $data);
                    return response()->json('Updated', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function profileEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        $checkEmail = Email::where('user_id', '!=',auth()->user()->id)->where('email',$data['email'])->first();
        if($checkEmail){
            $lang['en']= 'Email has already been used by other users';
            $lang['ru']= 'Почта уже использовуется';
            $lang['uz']= "Email band";
            return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
        }
        $email = Email::where('user_id', auth()->user()->id)->first();
        try {
            $res =DB::transaction(function () use ($data,$email){
                if($email){
                    $email->saveModel(auth()->user()->id, $data);
                }else{
                    $email->updateModel($data);
                }
                return response()->json('Updated', Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function UserprofileCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyName'=>'required',
            'city_id'=>'required',
            'region_id'=>'required',
            'street'=>'required',
            'house'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if(auth()->user()->role!='Company Owner'){
            $lang['en']= 'Your are not Company Owner';
            $lang['ru']= 'Вы не являетесь владельцем компании';
            $lang['uz']= "Siz kompaniya egasi emassiz";
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        if(Company::where('user_id', '!=',auth()->user()->id)->where('companyName', strtoupper($data['companyName']))->first()){
            $lang['en']= 'Company name Exist';
            $lang['ru']= 'Название компании имеется';
            $lang['uz']= "Kompaniya nomi mavjud";
            return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
        }
        $company = Company::with('companyadress')->where('user_id', auth()->user()->id)->first();
        
        if($company){
            try {
                $res =DB::transaction(function () use ($company, $data){
                    if($company->companyName != $data['companyName']){
                        $company->updateCompanyNameModel($data);
                    }
                    $company->companyadress->updateModelAdress($data);
                    return response()->json('Updated', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
       
        $lang['en']= 'Company not found';
        $lang['ru']= 'Компания не найдена';
        $lang['uz']= "Sizning kompaniyangiz topilmadi";
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        
    }
    public function UserprofileCompanyMembersApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'memberStatus'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
        if(!$company){
            $lang['en']= 'Company not found';
            $lang['ru']= 'Компания не найдена';
            $lang['uz']= "Sizning kompaniyangiz topilmadi";
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $use =$company->companymembers->where('member_id', $data['user_id'])->first();
        if($use){
            try {
                $res =DB::transaction(function () use ($use, $data){
                    $use->memberStatus = $data['memberStatus'];
                    $use->save();
                    return response()->json('Changed Status', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }
        $lang['en']= 'Company member not found';
        $lang['ru']= 'Участник компании не найден';
        $lang['uz']= "Kompaniya a'zosi topilmadi";
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
    }
    public function userCompanyMembersList()
    {
        $users = [];
        $company =Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
        if($company && $company->companymembers->count()>0){

         foreach ($company->companymembers as $key => $member) {
            $user = User::find($member->member_id);
            if($user){
                $temp['user_id'] = $member->member_id;
                $temp['name'] = $user->firstName.' '.$user->lastName;
                $users [] = $temp;
            }
         }   
        }
        return response()->json( $users, 200);
    }
    public function userCompanyMemberChange(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'member_id'=>'required',
        //     ]);
        // if ($validator->fails()) {
        //     $responseArr['error']=true;
        //     $responseArr['message'] = $validator->errors();
        //     return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        // }
        $data = $request->all();
        $company = Company::where('user_id', auth()->user()->id)->first();
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company with given user id not  exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if(isset($data['member_id']) && $data['member_id']){

            $user = User::where('id', $data['member_id'])->first();
            if(!$user){
                $responseArr['error']=true;
                $responseArr['message'] = 'User with given id not exist';
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }
            $companyMember = CompanyMembers::where('company_id', $company->id)->where('member_id', $data['member_id'])->first();
            if(!$companyMember){
                $responseArr['error']=true;
                $responseArr['message'] = 'User is not exist in this company';
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }
            try {
                $res = DB::transaction(function ()use ($company, $data, $user, $companyMember) {
                    // return response()->json([$company, $user, User::find(94)]);
                    $companyMember->updateMemberOnly($company->user_id, $data);
                    $oldUser = User::find($company->user_id);
                    if($oldUser){
                        $oldUser->updateUserRole('Employee', $data);
                       
                    }
                    $company->updateOwnerModel($user->id, $data);
                    $user->updateUserRole('Company Owner', $data);
                    
                    return response()->json('Updated', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            $companyMember = CompanyMembers::where('company_id', $company->id)->first();
            if($companyMember){
                $lang['ru']='У вас есть сотрудники';
                $lang['uz'] ='Xodimlaringiz mavjud';
                return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
            }
            try {
                $res = DB::transaction(function ()use ($company, $data,$companyMember) {
                    // return response()->json([$company, $user, User::find(94)]);
                    
                    $oldUser = User::find($company->user_id);
                    if($oldUser){
                        $oldUser->updateUserRole('Employee', $data);
                    }
                    $companyMembers = new CompanyMembers();
                    $data['company_id'] =1666;
                    $companyMembers->saveModel($company->user_id, $data);
                    // $company->deleteModel($data);
                    return response()->json('Updated', Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
    public function userCompanyChange(Request $request){
        $user = auth()->user;
        $data = $request->all();
        if($user->role=='Employee'){
            $data['company_id'] =1666;
            $companyMembers = new CompanyMembers();
            $companyMembers->saveModel(auth()->user()->id, $data);
            return response()->json('Updated', Response::HTTP_OK);
        }
        $lang['ru']='Вы не являетесь сотрудник аптеки';
        $lang['uz']="Siz dorixona xodimi emassiz";
        return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
    }
    public function buyCourseIqc(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $course = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')->find($data['course_id']);
        if(!$course){
            return ErrorHelperResponse::returnError('Course not found',Response::HTTP_NOT_FOUND);
        }
        $iqc = Iqc::where('user_id', auth()->user()->id)->first();
        if(!$iqc){
            return ErrorHelperResponse::returnError('IQC not enough',Response::HTTP_NOT_FOUND);
        }
        $iqcTransactions = IqcTransaction::where('user_id',auth()->user()->id)->where('serviceName','course')->where('identityText', $course->id)->first();
        if(!$iqcTransactions){
            if($iqc){
                if($iqc->amountofIQC>=$course->coursePrice){
                    $iqc->updateModel($data, $course->coursePrice,0,'course', $data['course_id']);
                }
            }
        }
        
        return response()->json($course, Response::HTTP_OK);
    }
    public function boughtCourseStatistics()
    {
        $transaction = IqcTransaction::where('user_id', auth()->user()->id)
        ->where('serviceName','course')->pluck('identityText');

        $array['transaction'] = $transaction;

        $company = false;
        $compid = 0;
        if(auth()->user()->role=='Company Owner'){
            
            $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
            if($company) $compid = $company->id;
            
        }
        if(auth()->user()->role=='Employee'){
            $company = CompanyMembers::where('member_id', auth()->user()->id)->first();
            if($company) $compid = $company->company_id;
        }
        if($company){
            $groups = GroupCompanyLists::select('group_id')->where('company_id',$compid )->get()->pluck('group_id');
            if(count($groups)==0){
                $array['special'] = false;
            }else{
                $array['special'] = true;
            }
        }else{
            $array['special'] = false;
        }
        return response()->json($array);
    }
    public function deleteUserProfile(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'user_id'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User not found please update the list';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $groupMemberList = GroupMemberLists::where('memberID', $user->id)->first();
        if($groupMemberList){
            $responseArr['error']=true;
            $responseArr['message'] = 'First Change or delete Group Member list';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $companyTeam = CompanyTeamLists::where('teamMember', $user->id)->first();
        if($companyTeam){
            $responseArr['error']=true;
            $responseArr['message'] = 'First Delete user from Company Team list';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $storeOwner = Store::where('storeOwner', $user->id)->first();
        if($storeOwner){
            $responseArr['error']=true;
            $responseArr['message'] = 'First Change or delete Store Owner';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $company= false;
        $companyMembers = false;
        if($user->role=='Company Owner'){
            $company = Company::where('user_id',$user->id)->first();
            if($company){
                $companyMembers = CompanyMembers::where('company_id', $company->id)->get();
                if(count($companyMembers)>0){
                    foreach ($companyMembers as $key => $companyMember) {
                        $userMember =  User::with('phonebook')->find($companyMember->member_id);
                        if($userMember && $userMember->phonebook){
                            $responseArr['error']=true;
                            $responseArr['message'] = 'First delete Compony Members';
                            return response()->json($responseArr, Response::HTTP_FOUND);
                        }
                    }
                }
                $groupList = GroupCompanyLists::where('company_id',$company->id)->first();
                if($groupList){
                    $responseArr['error']=true;
                    $responseArr['message'] = 'Company exists in group list, first delete from the list';
                    return response()->json($responseArr, Response::HTTP_FOUND);
                }
            }
            
        }
        $phonebook = Phonebook::where('user_id', $user->id)->first();
        if($phonebook){
            try {
                $res = DB::transaction(function() use ($data, $phonebook, $user,$companyMembers,$company ){
                    if($user->role=='Company Owner'){
                        if($companyMembers && count($companyMembers)>0){
                            foreach ($companyMembers as $key => $companyMember) {
                                $userMember =  User::find($companyMember->member_id);
                                if($userMember){
                                    $companyMember->delete();
                                }
                                $companyMember->delete();
                            }
                        }
                        if($company){
                            $company->deleteModel($data);
                        }
                    }else{
                        $companyMemberS = CompanyMembers::where('member_id', $user->id)->first();
                        if($companyMemberS) $companyMemberS->deleteModel($data);
                    }
                    $phonebook->deleteModel($data);
                    $email = Email::where('user_id', $user->id)->first();
                    if($email) $email->deleteModel($data);
                    $password = Password::where('user_id', $user->id)->first();
                    if($password) $password->deleteModel($data);
                    $user->deleteModel($data);
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        $responseArr['error']=true;
        $responseArr['message'] = 'Phone Number not found please update the list';
        return response()->json($responseArr, Response::HTTP_NOT_FOUND);
    }
    public function promoActivate(Request $request) {
        $validator = Validator::make($request->all(), [
            'promocode'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $promocode = Promocode::where('promocode',$data['promocode'])->first();
        if(!$promocode){
            $responseArr['error']=true;
            $lang['ru']= 'Промокод не найден';
            $lang['uz']= 'Promokod topilmadi';
            $responseArr['message'] = $lang;
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($promocode->amountOfWinners===0){
            $responseArr['error']=true;
                $lang['ru']= 'Promocode Исчерпан';
                $lang['uz']= 'PromoKod muddati tugagan';
                $responseArr['message'] = $lang;
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($promocode->amountOfWinners!==0 && $promocode->amountOfWinners!==null){
            $promoCodeLog = PromocodeLog::where('promocode_id',$promocode->id)->get();
            if($promocode->amountOfWinners<count($promoCodeLog)){
                $responseArr['error']=true;
                $lang['ru']= 'Promocode Исчерпан';
                $lang['uz']= 'PromoKod muddati tugagan';
                $responseArr['message'] = $lang;
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }
        }
        $promocodeTimeCheck = Promocode::where('promocode',$data['promocode'])
        ->where('endDate', '>=', Carbon::now('Asia/Tashkent'))
         ->where('startDate', '<=', Carbon::now('Asia/Tashkent'))->first();
        if(!$promocodeTimeCheck){
            $responseArr['error']=true;
            $lang['ru']= 'Promocode истёк';
            $lang['uz']= 'PromoKod muddati tugagan';
            $responseArr['message'] = $lang;
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $iqcTransaction = IqcTransaction::where('user_id', auth()->user()->id)->where('serviceName','promoCode')->where('identityText', $promocode->id)->first();
        if($iqcTransaction){
            $responseArr['error']=true;
            $lang['ru']= 'Promocode был использован';
            $lang['uz']= 'Promokod foydalanilgan';
            $responseArr['message'] = $lang;
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $iqc = Iqc::where('user_id', auth()->user()->id)->first();
        
        
        try {
            $res = DB::transaction(function() use ($iqc,$promocode, $data ){
                if(!$iqc){
                    $iqc = new Iqc();
                    $iqc->saveModel($data, auth()->user()->id, $promocode->prizeAmount,1,'promoCode',  $promocode->id);
                }else{
                    $iqc->updateStoreModel($data, $promocode->prizeAmount,1,'promoCode',  $promocode->id);
                }
                $newPromoLog = new PromocodeLog();
                $newPromoLog->saveModel($promocode->id);
                $responseArr['message'] = $promocode->prizeAmount;
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function userCourseCount(){

        $passed = 0;
        $ongoing  = 0;
        $rate = 0;
        
        
        $passed = CourseLog::
        with('course.getinfo','course.category','course.lessons.quizes.quizlog')
        ->has('course.lessons.quizes.quizlog')
        ->where('user_id', auth()->user()->id)
        ->count();
        
        $ongoing = CourseLog::
        with('course.getinfo','course.category','course.lessons.quizes.quizlog','course.lessons.lessonlog')
        ->where('user_id', auth()->user()->id)
        ->whereHas('course.lessons.lessonlog')
        ->whereDoesntHave('course.lessons.quizes.quizlog')
        ->count();
       
        $quizzes = Quizz::with('quizlog', 'questions')->get();
        $eachPercent = [];
       // return response()->json($quizzes);
        if($quizzes->count()>0){
            
            foreach ($quizzes as $key => $quizz) {
              
                if(count($quizz->quizlog)>0){
                    $lg = $quizz->quizlog[0];
                    
                    if($lg->status && $lg->prizeOut>0){
                        $rightPass = $lg->numberOfRightAnswers;
                        $quizPass = $quizz->numberRightAnswersToPass;
                        
                        if($quizPass<=$rightPass){
                            $total =count($quizz->questions);
                            if($total>0){
                                $eachPercent[]=  (int) ($quizPass * 100 / $total);
                            }

                        }
                        
                    }
                }
            }
            // return response()->json($eachPercent);
        }
        
        $responseArr['passed'] = $passed;
        $responseArr['ongoing'] = $ongoing;
        $responseArr['rate'] = $eachPercent ?  (int) ( array_sum($eachPercent)/ count($eachPercent)) : 0 ;
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function getToken()
    {
        try {
            $res = DB::transaction(function(){
                
                
                 $json  =  [
                    'email'=>'Maylantim@gmail.com',
                    'password'=>'5Juzs7eDSoHl876ScDbe42kafZhie36ej6WjvVV3',

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
    public function saveSentSms($phoneNumber, $operator, $method)
    {
        $phoneBookOperator = new PhonebookOperator();
        $phoneBookOperator->phoneNumber = $phoneNumber;
        $phoneBookOperator->operator = $operator;
        $phoneBookOperator->method = $method;
        $phoneBookOperator->save();
    }
    public function sendSms(Request $request)
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
        
        $checkNumber = Phonebook::where('phoneNumber',$data['phoneNumber'])->first();
        if($checkNumber && $checkNumber->user_id!=auth()->user()->id){
            $lang['ru']='Номер существует';
            $lang['uz']='Raqam mavjud';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_FOUND);
        }

        $ownNumber = Phonebook::where('user_id', auth()->user()->id)->first();
        if(!$ownNumber){
            $lang['ru']='Ошибка';
            $lang['uz']='Ошибка';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $start = \Carbon\Carbon::parse($ownNumber->randomTime);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
    
        if((int)$diffminut<5){
            $lang['ru']= 'Смс можно отправить через 5 минут';
            $lang['uz']= 'SMS ni 5 soniyadan keyn yuborish mumkin';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $ownNumber){
                $random = mt_rand(100000, 1000000);
                $ownNumber->random = $random;
                $ownNumber->randomTime = Carbon::now();
                $ownNumber->temp_number = $data['phoneNumber'];
                $ownNumber->save();
                $json  =  [
                    'mobile_phone'=>$ownNumber->temp_number,
                    'message'=>'PharmIQ Confirmation code: '.$ownNumber->random,
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
                        $this->saveSentSms($ownNumber->temp_number, 'eskiz','webUpdateNumber');
                        $lang['ru']= 'СМС отправлено';
                        $lang['uz']= 'SMS yuborildi';
                        $validate['message'] =$lang;
                        return response()->json($lang,Response::HTTP_OK);
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
                                $this->saveSentSms($ownNumber->phoneNumber, 'eskiz','webUpdateNumber');
                                $lang['ru']= 'СМС отправлено';
                                $lang['uz']= 'SMS yuborildi';
                                $validate['message'] =$lang;
                                Log::info(json_encode($lang));
                                return response()->json($lang,Response::HTTP_OK);
                            }
                        }
                        
                    }
                       
                }
                $lang['ru']= 'CМС не отправлено';
                $lang['uz']= 'SMS yuborilmadi';
                Log::info(json_encode($lang));
                return response()->json($lang,Response::HTTP_NOT_FOUND);
                
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
    public function confirmUpdateNumber(Request $request){
        $lang['ru']= 'Заполните секретнет номер';
        $lang['uz']= 'Mahfiy raqamni kiriting';
        $validate['code']['required'] = $lang;
        $validator = Validator::make($request->all(), [
           
            'code'=>'required|numeric|digits:6',
        ],
        [
            'code.required' => json_encode($validate['code']['required']),
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $ownNumber = Phonebook::where('user_id', auth()->user()->id)->first();
        if(!$ownNumber){
            $lang['ru']='Ошибка';
            $lang['uz']='Hatolik';
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        $start = \Carbon\Carbon::parse($ownNumber->randomTime);
        $end = \Carbon\Carbon::now();
        $diffminut = $start->diff($end)->format('%I');
        $diffhour = $start->diff($end)->format('%H');

        
    
        
        if((int)$diffhour>0){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }

        if((int)$diffminut>5){
            $lang['ru']= 'Срок действия кода подтверждения истек';
            $lang['uz']= 'Tasdiqlash kodi muddati tugagan';
            $validate['message'] =$lang;
            return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $ownNumber){
                if($ownNumber->random==$data['code']){
            
                    $ownNumber->phoneNumber = $ownNumber->temp_number;
                    $ownNumber->temp_number = null;
                    $ownNumber->save();
                    $numberHistory = new PhonebookHistories();
                    $numberHistory->saveModel($ownNumber, $data,1);
                    $lang['ru']= 'Номер обновлен';
                    $lang['uz']= 'Raqam yangilandi';
                    return response()->json($lang);
                }
                $lang['ru']= 'Неправильный код подтверждения';
                $lang['uz']= "Tasdiqlash kodi noto'g'ri";
                Log::info(json_encode($lang));
                return ErrorHelperResponse::returnError($lang,Response::HTTP_NOT_FOUND);
                
                
            });
            return $res;
        } catch (\Throwable $th) {
            
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function userlotteries(){
        $lotteries = Lottery::with('lotteryUserLogs')->whereHas('lotteryUserLogs', function($query){
            return $query->where('user_id',auth()->user()->id);
        })->get();
        return response()->json($lotteries, Response::HTTP_OK);
    }
}