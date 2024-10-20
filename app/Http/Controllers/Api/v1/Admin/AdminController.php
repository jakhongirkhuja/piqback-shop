<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamLists;
use App\Models\CompanyTeams;
use App\Models\Course\CourseLog;
use App\Models\Email;
use App\Models\General;
use App\Models\Groups\Group;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\GroupHistories;
use App\Models\Groups\GroupMemberLists;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\PasswdHistories;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\Region;
use App\Models\Scout;
use App\Models\StoreLatest\Store;
use App\Models\User;
use App\Models\Wish\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function statistics()
    {
        $phonebooks = PhoneBook::select('phoneNumber','created_at')->orderby('created_at','asc')->whereDate('created_at', '>=', \Carbon\Carbon::now()->subDays(60)->toDateTimeString())->get()->
                groupBy(function($val) {
                return \Carbon\Carbon::parse($val->created_at)->format('d F');
        })->toArray();
            $company = Company::select('created_at')->orderby('created_at','asc')->whereDate('created_at', '>=', \Carbon\Carbon::now()->subDays(60)->toDateTimeString())->get()->
            groupBy(function($val) {
            return \Carbon\Carbon::parse($val->created_at)->format('d F');
        })->toArray();
        $employees = DB::table('users')->where('role', 'Employee')->count();
        $owners = DB::table('users')->where('role', 'Company Owner')->count();
        $companies = DB::connection('pgsql2')->table('companies')->count();
        $courses = DB::connection('pgsql3')->table('courses')->count();
        $usersLast = User::with('phonebook')->has("phonebook")->latest()->take(10)->get();
        $companiesLast = Company::latest()->take(10)->get();
        $coor = DB::connection('pgsql2')->table('company_addresses')->select('longitude','latitude', 'companies.companyName')->join('companies','company_addresses.company_id','companies.id')->where('longitude','!=',null)->where('latitude','!=',null)->get()->toArray();
        $responseArr['employees'] = $employees;
        $responseArr['owners'] = $owners;
        $responseArr['companies'] = $companies;
        $responseArr['courses'] = $courses;
        $responseArr['usersLast'] = $usersLast;
        $responseArr['companiesLast'] = $companiesLast;
        $responseArr['coor'] = $coor;
        $responseArr['phonebooks']=$phonebooks;
        $responseArr['companystat']=$company;
        $responseArr['user'] = auth()->user();
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function statisticsFinanceDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date'=>'required|date_format:Y-m-d H:i',
            'end_date'=>'required|date_format:Y-m-d H:i',
           
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }

        $iqcPerDays = IqcTransaction::select('value','created_at','user_id')->whereBetween('created_at', [$request->start_date, $request->end_date])->where('value','!=',0)->where('value','<',300)->get()->groupby(function($date) {
            // return Carbon::parse($date->created_at)->format('Y'); // grouping by years
            //return Carbon::parse($date->created_at)->format('m'); // grouping by months
            return \Carbon\Carbon::parse($date->created_at)->format('F d'); // grouping by day
        })->toArray();
        
       
        
        $userGraph = [];
        foreach($iqcPerDays as $k=>$iqcPerDay){
            $value = 0;
            $userCount = [];
            if(count($iqcPerDay)>0){
                foreach($iqcPerDay as $iqcDay){
                    $value+=$iqcDay['value'];
                    if (!in_array($iqcDay['user_id'], $userCount)){
                        $userCount[]= $iqcDay['user_id'];
                    }
                }
            }
            $userGraph[$k]['iqc'] = [count($userCount),$value];
           
        }
        $responseArr['userGraph'] = $userGraph;
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function removedIqcUsers(){
        $trs = IqcTransaction::where('serviceName','removed')->latest()->take(20)->get();
        $arr = [];
        foreach ($trs as $key => $tr) {
            $user['useR'] = User::with('phonebook')->find($tr->user_id);
            $user['iqcTransaction'] = $tr;
            $arr[] = $user;
        }
        return response()->json($arr,Response::HTTP_OK);
    }
    public function statisticsFinance()
    {
        $iqcAll = IQC::sum('amountofIQC');
        $iqcAllTransaction = IqcTransaction::where('valueType',1)->sum('value');
        // $iqcAll = $iqcAllAdd-$iqcAllUsed;
        // dd($iqcAllAdd,$iqcAllUsed,$iqcAll,$iqc);
        $iqcPerDays = IqcTransaction::select('value','created_at','user_id')->where('value','!=',0)->where('value','<',300)->get()->groupby(function($date) {
        // return Carbon::parse($date->created_at)->format('Y'); // grouping by years
        //return Carbon::parse($date->created_at)->format('m'); // grouping by months
            return \Carbon\Carbon::parse($date->created_at)->format('F d'); // grouping by months
        })->toArray();
        
        $arraDay = [];
        
        $userGraph = [];
        foreach($iqcPerDays as $k=>$iqcPerDay){
            $value = 0;
            $userCount = [];
            if(count($iqcPerDay)>0){
                foreach($iqcPerDay as $iqcDay){
                    $value+=$iqcDay['value'];
                    if (!in_array($iqcDay['user_id'], $userCount)){
                        $userCount[]= $iqcDay['user_id'];
                    }
                }
            }
            $userGraph[$k]['iqc'] = [count($userCount),$value];
            $arraDay[]=$value;
       }
    //   dd($userGraph);
       $countArray = count($arraDay);
       // avarageDay;
       $avarageDay = $countArray!=0? (int) (array_sum($arraDay)/count($arraDay)) : 0;
       
       
       
       //////////////
       $iqcPerWeeks = IqcTransaction::select('value','created_at')->where('value','!=',0)->where('value','<',2500)->get()->groupby(function($date) {
        // return Carbon::parse($date->created_at)->format('Y'); // grouping by years
        return \Carbon\Carbon::parse($date->created_at)->format('m W'); // grouping by months
           
        })->toArray();
        $arraWeek = [];
       
        foreach($iqcPerWeeks as $k=>$iqcPerWeek){
            $value = 0;
            if(count($iqcPerWeek)>0){
                foreach($iqcPerWeek as $iqcPerWe){
                    $value+=$iqcPerWe['value'];
                }
            }
            $arraWeek[]=$value;
       }
       $countArray = count($arraWeek);
       // avarageWeek;
       $avarageWeek = $countArray!=0? (int) (array_sum($arraWeek)/count($arraWeek)) : 0;
       
       
       /////////////
       
        $iqcPerMonths = IqcTransaction::select('value','created_at')->where('value','!=',0)->where('value','<',2500)->get()->groupby(function($date) {
        return \Carbon\Carbon::parse($date->created_at)->format('Y m'); // grouping by years
       
           
        })->toArray();
        $arraMonth = [];
       
        foreach($iqcPerMonths as $k=>$iqcPerMonth){
            $value = 0;
            if(count($iqcPerMonth)>0){
                foreach($iqcPerMonth as $iqcPerMo){
                    $value+=$iqcPerMo['value'];
                }
            }
            $arraMonth[]=$value;
       }
       $countArray = count($arraMonth);
    
       // avarageMonth;
      $avarageMonth = $countArray!=0? (int) (array_sum($arraMonth)/count($arraMonth)) : 0;
       
       
       ////////////
      
      
       $avaragePerUser = (int) ($iqcAll/PhoneBook::count());
   
       ////////////
        $iqcNoZero = IQC::where('amountofIQC','!=',0)->count();
       $avaragePerUserNoZero =(int) ($iqcAll/$iqcNoZero);
       
       ////////////
       
    //   $userGraph
       
       
       ///////////
            $countIqcStore = 0;
            $pieIqcStores = IqcTransaction::where('serviceName','storeProduct')->where('value','!=',0)->get();
            if(count($pieIqcStores)>0){
                foreach($pieIqcStores as $pieIqcStore){
                    $countIqcStore+=$pieIqcStore->value;
                        
                    
                }
            }

            $countIqcPromo = 0;
            $pieIqcPromos = IqcTransaction::where('serviceName','promoCode')->where('value','!=',0)->get();
            if(count($pieIqcPromos)>0){
                foreach($pieIqcPromos as $pieIqcPromo){
                    $countIqcPromo+=$pieIqcPromo->value;
                        
                    
                }
            }
            $countIqcQuiz = 0;
            $pieIqcQuizes = IqcTransaction::where('serviceName','quiz')->where('value','!=',0)->get();
            if(count($pieIqcQuizes)>0){
                foreach($pieIqcQuizes as $pieIqcQuiz){
                    $countIqcQuiz+=$pieIqcQuiz->value;
                        
                    
                }
            }
       
           
       //////////////////
       
            $countIqcCoursePaid = 0;
            $pieIqcStoresPaids = IqcTransaction::where('serviceName','paidCourses')->where('value','!=',0)->get();
            if(count($pieIqcStoresPaids)>0){
                foreach($pieIqcStoresPaids as $pieIqcStoresPaid){
                    $countIqcCoursePaid+=$pieIqcStoresPaid->value;
                }
            }
       
       
       ////////////
       
        
         $iqcNotSpend= $iqcAll;
           
       
       
       /////////////
        $AlluserCount = User::count();
        $haveUserIqc =IQC::where('amountofIQC','!=',0)->count();

        $iqcPercentage1 = (int) ($AlluserCount-$haveUserIqc) <0? 0 : $AlluserCount-$haveUserIqc;
        $iqcPercentage2 = IQC::where('amountofIQC',5)->count();
        $iqcPercentage3 = IQC::whereBetween('amountofIQC', [5, 20])->count();
        $iqcPercentage4 = IQC::whereBetween('amountofIQC', [20, 100])->count();
        $iqcPercentage5 = IQC::whereBetween('amountofIQC', [100, 200])->count();
        $iqcPercentage6 = IQC::whereBetween('amountofIQC', [200, 500])->count();
        $iqcPercentage7 = IQC::where('amountofIQC','>=',500)->count();
       
       
       //////////////
      
       $responseArr['iqcAll'] = $iqcAllTransaction;
       $responseArr['avarageDay'] = $avarageDay;
       $responseArr['avarageWeek'] = $avarageWeek;
       $responseArr['avarageMonth'] = $avarageMonth;
       $responseArr['avaragePerUser'] = $avaragePerUser;
       $responseArr['avaragePerUserNoZero'] = $avaragePerUserNoZero;
       $responseArr['userGraph'] = $userGraph;
       $responseArr['countIqcStore'] = $countIqcStore;
       $responseArr['countIqcPromo'] = $countIqcPromo;
       $responseArr['countIqcQuiz'] = $countIqcQuiz;
       $responseArr['countIqcCoursePaid'] = $countIqcCoursePaid;
       $responseArr['iqcNotSpend'] =$iqcNotSpend;
       $responseArr['iqcPercentage1'] =$iqcPercentage1;
       $responseArr['iqcPercentage2'] =$iqcPercentage2;
       $responseArr['iqcPercentage3'] =$iqcPercentage3;
       $responseArr['iqcPercentage4'] =$iqcPercentage4;
       $responseArr['iqcPercentage5'] =$iqcPercentage5;
       $responseArr['iqcPercentage6'] = $iqcPercentage6;
       $responseArr['iqcPercentage7'] = $iqcPercentage7;
       return response()->json($responseArr, Response::HTTP_OK);
    }
    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number'=>'required|size:12',
            'firstName'=>'required|max:190',
            'lastName'=>'required|max:190',
            'gender'=>'required|min:1|max:2',
            'birthDate'=>'required|date_format:Y-m-d',
            'role'=>'required',
            'passwd'=>'required|min:5'  
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $number = Phonebook::where('phoneNumber',$data['number'])->first();
        if($number){
            $responseArr['error']=true;
            $responseArr['message'] = 'Number exists in database';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        
        try {
            $res = DB::transaction(function() use ($data){
                $user = new User();
                $user->saveModel($data);
                $password=  Password::where('user_id',$user->id)->first();
                if($password){
                    $password->passwd = Hash::make($data['passwd']);
                    $password->save();
                    $passwdHistories = new PasswdHistories();
                    $passwdHistories->user_id = $user->id;
                    $passwdHistories->passwd = $password->passwd;
                    $passwdHistories->save();
                    StandardAttributes::setSA('passwd_histories',$passwdHistories->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                }else{
                    $password = new Password();
                    $password->user_id = $user->id;
                    $password->passwd = Hash::make($data['passwd']);
                    $password->save();
                    $passwdHistories = new PasswdHistories();
                    $passwdHistories->user_id = $user->id;
                    $passwdHistories->passwd = $password->passwd;
                    $passwdHistories->save();
                    StandardAttributes::setSA('passwd_histories',$passwdHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                }
                if(isset($data['email']) && $data['email'] && $data['email']!='null'){
                    $email = new Email();
                    $email->saveModel($user->id, $data);
                }
                if($data['role'] == 'Scout'){
                    $newGroupName = new Group();
                    $newGroupName->groupName = Str::slug($user->firstName).'-'.$user->hrid;
                    $newGroupName->save();
                    $groupHistory = new GroupHistories();
                    $groupHistory->saveModel($newGroupName, $data);
                    $connectScout = new Scout();
                    $connectScout->saveModel($newGroupName->id,$user->id,$data);
                    
                }
                $number = new Phonebook();
                $number->user_id = $user->id;
                $number->phoneNumber = $data['number'];
                $number->save();
                $responseArr['user_id'] =$user;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        

    }
    public function showGroups(){
        $search = request()->search;
        if($search){
            $group = Group::where('groupName','ilike','%'.$search.'%')->paginate(20);
        }else{
            $group = Group::where('groupName',2)->paginate(20);
        }
        return response()->json($group, Response::HTTP_OK);
    }
    public function showUsers()
    {
        $search = request()->search;
        $pagination = request()->paginate;
        if($search){
            if(auth()->user()->role =='Creator'){
                $users = User::with('phonebook','email')->whereHas("phonebook",function($q) use($search){
                    $q->where('phoneNumber','like','%'.$search.'%');
                })->select('*', 'hrid as user_id')->latest()->paginate($pagination? $pagination : 100);
            }else{
                $users = User::with('phonebook','email')->where('role','!=','Creator')->whereHas("phonebook",function($q) use($search){
                    $q->where('phoneNumber','like','%'.$search.'%');
                })->select('*', 'hrid as user_id')->latest()->paginate($pagination? $pagination : 100);
            }
            
        }else{
            if(auth()->user()->role =='Creator'){
                $users = User::with('phonebook','email')->select('*', 'hrid as user_id')->latest()->paginate($pagination? $pagination : 20);
            }else{
                $users = User::with('phonebook','email')->where('role','!=','Creator')->select('*', 'hrid as user_id')->latest()->paginate($pagination? $pagination : 20);
            }
            
        }
        return response()->json($users, Response::HTTP_OK);
    }
    public function showUsersId($user_id)
    {
        $user = User::with('phonebook','email')->where('hrid',$user_id)->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User not  exists in database, please update users list';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $iqc = Iqc::where('user_id', $user->id)->first();
        $iqcTransction = IqcTransaction::where('user_id',$user->id)->get();
        $savedCourse = Wishlist::with('courselist.getinfo')->where('user_id',$user->id)->get();
        $courseLog = CourseLog::with('course.getinfo','course.lessons.specificlessonlog')->where('user_id', $user->id)->get();

        $company = false;
        $companyAdress = false;
        if($user->role=='Company Owner'){
            $company = Company::with('companymembers')->where('user_id', $user->id)->first();
            if($company){
                $companyAdress = CompanyAddress::where('company_id', $company->id)->first();
            }
        }else{
            $companyMembers = CompanyMembers::where('member_id', $user->id)->first();
            if($companyMembers){
                $company = Company::find($companyMembers->company_id);
                if($company){
                    $companyAdress = CompanyAddress::where('company_id', $companyMembers->company_id)->first();
                }
            }
        }
        $groups = GroupMemberLists::with('group')->where('memberID', $user->id)->get();
        $groupName=[];
        if($groups->count()>0){
            foreach ($groups as $key => $group) {
                if($group->group) $groupName[] = $group->group->groupName;
            }
        }
        $responseArr['groups']=$groupName;
        $responseArr['user'] =$user;
        $responseArr['iqc'] =$iqc;
        $responseArr['iqcTransction'] =$iqcTransction;
        $responseArr['savedCourse'] =$savedCourse;
        $responseArr['courseLog'] =$courseLog;
        $responseArr['company'] =$company;
        $responseArr['companyAdress'] =$companyAdress;
        $responseArr['city'] =$companyAdress? City::find($companyAdress->city_id) : null;
        $responseArr['region'] =$companyAdress? Region::find($companyAdress->region_id) : null;
        $responseArr['courseLog'] =$courseLog;
        $responseArr['message'] = 'Success';
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'number'=>'required|size:12',
            'firstName'=>'required|max:190',
            'lastName'=>'required|max:190',
            'gender'=>'required|min:1|max:2',
            'birthDate'=>'required|date_format:Y-m-d',
            'role'=>'required',
            'language'=>'required',
            'password'=>'nullable'  
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
            $responseArr['message'] = 'User not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $number = Phonebook::where('phoneNumber',$data['number'])->whereNot('user_id', $user->id)->first();
        if($number){
            $responseArr['error']=true;
            $responseArr['message'] = 'Number exists in database';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $phonebook = Phonebook::where('user_id',$user->id)->first();
        if(!$phonebook){
            $responseArr['error']=true;
            $responseArr['message'] = 'Connected number not exist';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $user, $phonebook){
                $user->updateModel($data);
                // if(isset($data['password']) && $data['password']){
                //     $password = Password::where('user_id', $user->id)->first();
                //     if($password){
                //         $password->updateModel($data);
                //     }else{
                //         $password = new Password();
                //         $password->saveModel($user->id, $data);
                //     }
                // }
                if(isset($data['email']) && $data['email']){
                    $email = Email::where('user_id', $user->id)->first();
                    if($email){
                        $email->updateModel($data);
                    }else{
                        $email = new Email();
                        $email->saveModel($user->id, $data);
                    }
                }
                if($data['email']==''){
                    $email = Email::where('user_id', $user->id)->first();
                    if($email) $email->deleteModel($data);
                }
                $phonebook->updateModel($data);
                $responseArr['user_id'] =$user;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateUserRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required|numeric',
            'role'=>'required', 
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
            $responseArr['message'] = 'User not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }else{
            if($user->role!='Employee'){
                $responseArr['error']=true;
                $responseArr['message'] = 'User Must be employee';
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }
        }
        
       
        $companyMember = CompanyMembers::where('member_id', $user->id)->first();
        if(!$companyMember){
            $responseArr['error']=true;
            $responseArr['message'] = 'CompanyMember not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
      
        $company = Company::find($companyMember->company_id);
        
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyOwnerUser = User::find($company->user_id);
        if(!$companyOwnerUser){
            $responseArr['error']=true;
            $responseArr['message'] = 'CompanyOwner in Users not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
       
        try {
            $res = DB::transaction(function() use ($data, $user, $company, $companyMember, $companyOwnerUser){
                $oldCompanyOwner = $company->user_id;
                $newCompanyOwner = $user->id;
                
                $company->updateOwnerModel($newCompanyOwner,$data);
                
                $companyMember->updateMemberOnly($oldCompanyOwner,$data);
               
                $user->updateUserRole($data['role'], $data);
               
                $companyOwnerUser->updateUserRole('Employee', $data);
                $responseArr['user_id'] =$user;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function deleteUser(Request $request)
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
                $companyTeamExist = CompanyTeams::where('company_id', $company->id)->first();
                if($companyTeamExist){
                    $responseArr['error']=true;
                    $responseArr['message'] = 'First Delete Company Teams';
                    return response()->json($responseArr, Response::HTTP_FOUND);
                }
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
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'user_id'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['user_id'])->first();
        if(!$user){
            $responseArr['message'] = 'User with given id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $email  = Email::where('email', $data['email'])->where('user_id','!=', $user->id)->first();
        if(!$email){
            $responseArr['message'] = 'Email not exist in databee';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $responseArr['message'] = 'Email exists in database';
        return response()->json($responseArr, Response::HTTP_FOUND);
    }
    
    public function updateUserCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|numeric',
            'user_id'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['user_id'])->first();
        if(!$user){
            $responseArr['message'] = 'User with given id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        
        if($user->role!='Employee'){
            $responseArr['message'] = 'User is not an Employee';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $company = Company::find($data['company_id']);
        
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyMembers = CompanyMembers::where('company_id', $data['company_id'])->where('member_id',$user->id)->first();
        if($companyMembers){
            $responseArr['error']=true;
            $responseArr['message'] = 'User is exist in this company';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $company,$user){
                $companyMembers = CompanyMembers::where('member_id',$user->id)->first();

                if($companyMembers){
                    $companyMembers->deleteModel($data);
                    $log = [
                        'URI' => 'changeUserCompany',
                        'METHOD' => 'updateUserCompany',
                        'Message' => "deleted old members"
                    ];
                    Log::info(json_encode($log));
                    $newCompany = new CompanyMembers();
                    $newCompany->saveModel($user->id,$data);
                    $responseArr['message'] = 'Updated';
                    return response()->json($responseArr, Response::HTTP_OK);
                }else{
                    $responseArr['message'] = 'Not updated error';
                    $log = [
                        'URI' => 'changeUserCompany',
                        'METHOD' => 'updateUserCompany',
                        'Message' => "Not updated error userid:".$data,
                    ];
                    Log::info(json_encode($log));
                    return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                }
                
                
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function mergeUserCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|numeric',
            'user_id'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['user_id'])->first();
        if(!$user){
            $responseArr['message'] = 'User with given id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        
        if($user->role!='Company Owner'){
            $responseArr['message'] = 'User is not Company Owner';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyOwner = Company::where('user_id', $user->id)->first();
        if(!$companyOwner){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company of the owner not found, something went wrong';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $companyOwnerMembers = CompanyMembers::where('company_id', $companyOwner->id)->get();
        if(count($companyOwnerMembers)>0){
            foreach ($companyOwnerMembers as $key => $companyMembe2r) {
                $userInfo =  User::with('phonebook')->find($companyMembe2r->member_id);
                if($userInfo && $userInfo->phonebook){
                    $responseArr['error']=true;
                    $responseArr['message'] = 'Company have '.count($companyOwnerMembers).' member(s)';
                    return response()->json($responseArr, Response::HTTP_FOUND);
                }
            }
            
        }
        
        $company = Company::find($data['company_id']);
        
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Target Company not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($company->user_id == $user->id){
            $responseArr['error']=true;
            $responseArr['message'] = 'User cannot be merged to its own company';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyMembers = CompanyMembers::where('company_id',$data['company_id'])->where('member_id',$user->id)->first();
        if($companyMembers){
            $responseArr['error']=true;
            $responseArr['message'] = 'User has already benn assigned as a member to this group';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        
        $groupListCHeck = GroupCompanyLists::where('company_id', $companyOwner->id)->first();
        if($groupListCHeck){
            $responseArr['error']=true;
            $responseArr['message'] = 'User`s Company that you mergin  is exist inside group, please first remove from group list';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data,$user, $companyOwner){
                $user->updateUserRole('Employee',$data);
                $newCompany = new CompanyMembers();
                $newCompany->saveModel($user->id,$data);
                $newCompany->memberStatus =1;
                $newCompany->save();
                $companyOwner->deleteModel($data);
                $responseArr['message'] = 'Merged Successfully';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function userExport(Request $request)
    {
        return Excel::download(new \App\Exports\UsersExport, 'users.xlsx');
    }
    public function usersAndIqc(Request $request)
    {
        return Excel::download(new \App\Exports\UsersIqc, 'usersAndIqc.xlsx');
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
    public function getStatuses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date'=>'required|date_format:Y-m-d H:i',
            'end_date'=>'required|date_format:Y-m-d H:i',
            'page'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $json  =  [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
        ];
        
        $general = General::where('name','eskiz')->first();
        if($general){
            $token =  $general->value;
        }else{
            $token = $this->getToken();
        }
        
        if($token){
            $response = Http::withHeaders([
               
                'Authorization'=>'Bearer '.$token,
            ])->post('https://notify.eskiz.uz/api/message/sms/get-user-messages?page='.$request->page,$json);
            if(!$response->ok()){
                $lang['ru']= 'CМС не отправлено';
                $lang['uz']= 'SMS yuborilmadi';
                $validate['message'] =$lang;
                return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
            }
            if($response->status()==401){
                $token = $this->getToken();
                if($token){
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/get-user-messages?page='.$request->page,$json);
                    if(!$response->ok()){
                        $lang['ru']= 'CМС не отправлено';
                        $lang['uz']= 'SMS yuborilmadi';
                        $validate['message'] =$lang;
                        return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                    }
                }
                
            }
            return response()->json($response->json(),201);    
        }
        return response()->json('Token not valid', Response::HTTP_EXPECTATION_FAILED);  
    }
    public function getStatusesMailing(Request $request)
    {
        
        $json  =  [
            'dispatch_id'=>'120',
        ];
        $general = General::where('name','eskiz')->first();
        if($general){
            $token =  $general->value;
        }else{
            $token = $this->getToken();
        }
        
        if($token){
            $response = Http::withHeaders([
               
                'Authorization'=>'Bearer '.$token,
            ])->post('https://notify.eskiz.uz/api/message/sms/get-dispatch-status',$json);
            
            if($response->status()==401){
                $token = $this->getToken();
                if($token){
                    $response = Http::withHeaders([
                        'Accept'=>'application/json',
                        'Content-Type'=>'application/json',
                        'Authorization'=>'Bearer '.$token,
                    ])->post('https://notify.eskiz.uz/api/message/sms/get-dispatch-status',$json);
                    if(!$response->ok()){
                        $lang['ru']= 'CМС не отправлено';
                        $lang['uz']= 'SMS yuborilmadi';
                        $validate['message'] =$lang;
                        return response()->json(json_encode($validate['message']),Response::HTTP_NOT_FOUND);
                    }
                }
                
            }
            return response()->json($response->json(),201);    
        }
        return response()->json('Token not valid', Response::HTTP_EXPECTATION_FAILED);  
    }
    public function iqlabsUsers(Request $request){
        
        return response()->json(User::with('phonebook')->whereIn('hrid', $request->users)->paginate(100),Response::HTTP_OK);
    }
    public function removeIqcUsersGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'searchby'=>'required',
            'iqc' =>'required',
            'comment' =>'required',
            'notify' =>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        try {
            if($data['searchby']==0){
                $iqc = Iqc::where('user_id', $data['selectedUserId'])->first();
                if($iqc){
                    $arr['before'] = $iqc->amountofIQC;
                    $iqc->updateModel($data, $data['iqc'],0,'removed', $data['comment'], $data['notify']);
                    $arr['after'] = $iqc->amountofIQC;
                    $newArray[]= $arr;
                }
            }
            if($data['searchby']==1){
                $groupMembers= GroupMemberLists::where('group_id',$data['selectedGroupId'])->get();
                if($groupMembers->count()>0){
                    foreach ($groupMembers as $key => $groupMember) {
                        $iqc = Iqc::where('user_id', $groupMember->memberID)->first();
                        if($iqc){
                            $arr['before'] = $iqc->amountofIQC;
                            $iqc->updateModel($data, $data['iqc'],0,'removed', $data['comment'],  $data['notify']);
                            $arr['after'] = $iqc->amountofIQC;
                            $newArray[]= $arr;
                        }
                    }
                    
                }
            }
            return response()->json($newArray,Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(''.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function removeIqcUsers(Request $request){
        $validator = Validator::make($request->all(), [
            'phoneAndIqc'=>'required',
            'single'=>'required',
            'force'=>'required',
            'comment'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($data['single']){

            $newArray = [];
            $phoneNumber = Phonebook::where('phoneNumber', $data['phoneAndIqc'])->first();
            if($phoneNumber){
                $iqcTransction = IqcTransaction::where('user_id', $phoneNumber->user_id)->where('serviceName','removed')->first();
                if(!$iqcTransction){
                    $iqc = Iqc::where('user_id', $phoneNumber->user_id)->first();
                    if($iqc){
                        $arr['before'] = $iqc->amountofIQC;
                        $iqc->updateModel($data, $data['value'],0,'removed', $data['comment']);
                        $arr['after'] = $iqc->amountofIQC;
                        $newArray[]= $arr;
                    }
                }else{
                    if($data['forceRemove']==1){
                        $iqc = Iqc::where('user_id', $phoneNumber->user_id)->first();
                        if($iqc){
                            
                            
                            $arr['before'] = $iqc->amountofIQC;
                            
                            $iqc->updateModel($data, $data['value'],0,'removed',$data['comment']);
                            $arr['after'] = $iqc->amountofIQC;
                            $newArray[]= $arr;
                        }
                    }
                }
            }
            return response()->json($newArray,Response::HTTP_OK);
        }
        $json_datas = json_decode($data['phoneAndIqc'], true);
        
       
        if(count($json_datas)>0){
            
            $newArray = [];
            foreach ($json_datas as $key => $json_data2) {
            
                if(count($json_data2)>0){
                    foreach ($json_data2 as $key => $json_data) {
                        
                        $phoneNumber = Phonebook::where('phoneNumber', $json_data['phone'])->first();
                        if($phoneNumber){
                            
                            $iqcTransction = IqcTransaction::where('user_id', $phoneNumber->user_id)->where('serviceName','removed')->first();
                            if(!$iqcTransction){
                                $iqc = Iqc::where('user_id', $phoneNumber->user_id)->first();
                                if($iqc){
                                   
                                    $arr['phone'] = $json_data['phone'];
                                    $arr['before'] = $iqc->amountofIQC;
                                   
                                    $iqc->updateModel($data, $json_data['value'],0,'removed',$data['comment']);
                                    $arr['after'] = $iqc->amountofIQC;
                                    $newArray[]= $arr;
                                }
                            }
                            
                        }
                        
                    }
                }
            }
            return response()->json($newArray,Response::HTTP_OK);
        }
        return response()->json('Empty',Response::HTTP_NOT_FOUND);
    }
}
