<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\CompanyMembers;
use App\Models\Email;
use App\Models\Groups\GroupMemberLists;
use App\Models\Money\Iqc;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\Scout;
use App\Models\User;
use App\Models\UserLangHistories;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class AuthControllerGlobal extends Controller
{
    public $userGlobal;
    public $userScout;
    public $refUser;
    public function registerUserGlobal(Request $request)
    {
        
        
        $validator = Validator::make($request->all(), [
            'number'=>'required|size:12',
            'firstName'=>'required|max:190',
            'lastName'=>'required|max:190',
            'gender'=>'required|numeric|min:0|max:1',
            'birthDate'=>'required|date_format:d-m-Y',
            'password'=>'required|min:8',
            'scout_id'=>'nullable',
            'email'=>'nullable|email|unique:emails',
            'role'=>'required',
            'ref_id'=>'nullable'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();

        try {
            $res = DB::transaction(function () use ($data) {
                
                if($data['role']=='Company Owner'){
                    
                    $validator = Validator::make($data, [
                        'companyName'=>'required|min:3|unique:pgsql2.companies,companyName',
                        'city_id'=>'required|numeric|min:1',
                        'region_id'=>'required|numeric|min:1',
                        'addressType'=>'required|numeric|min:0|max:1',
                        'street'=>'required',
                        'house'=>'required',
                        'longitude'=>'nullable',
                        'latitude'=>'nullable',
                    ]);
                    if ($validator->fails()) {
                        $responseArr['error']=true;
                        $responseArr['message'] = $validator->errors();
                        return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
                    }
                    if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null'){
                        $usersc = User::where('hrid',$data['scout_id'] )->where('role','Scout')->first();
                        if(!$usersc){
                            $responseArr['error']=true;
                            $responseArr['message'] ='Scout with given id not found';
                            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        }
                        $this->userScout = $usersc;
                    }
                    $user = new  User();
                    $user->saveModelRegister($data);
                    $checkfirstPhone = Phonebook::where('phoneNumber', $data['number'])->first();
        
                    if($checkfirstPhone){
                        $checkfirstPhone->status=0;
                        $checkfirstPhone->save();
                    }else{
                        $phonebook = new Phonebook();
                        $phonebook->saveModel($user->id, $data);
                    }
                    $response = $this->createUser($user, $data);
               
                    if(isset($response->original['error']) && $response->original['error']){
                        return $response;
                    }
                    $this->createUserCompany($user, $data);
                }else{
                    $validator = Validator::make($data, [
                        'company_id'=>'required',
                    ]);
                    if ($validator->fails()) {
                        $responseArr['error']=true;
                        $responseArr['message'] = $validator->errors();
                        return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
                    }
                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!='null'){
                       
                        $refUser = User::where('hrid',$data['ref_id'] )->first();
                        if(!$refUser){
                            $responseArr['error']=true;
                            $responseArr['message'] ='Scout with given id not found';
                            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        }
                        $this->refUser = $refUser;
                        
                    }
                    $user = new  User();
                    $user->saveModelRegister($data);
                    $checkfirstPhone = Phonebook::where('phoneNumber', $data['number'])->first();
        
                    if($checkfirstPhone){
                        $checkfirstPhone->status=0;
                        $checkfirstPhone->save();
                    }else{
                        $phonebook = new Phonebook();
                        $phonebook->saveModel($user->id, $data);
                    }
                    $response = $this->createUser($user, $data);
               
                    if(isset($response->original['error']) && $response->original['error']){
                        return $response;
                    }
                    $response = $this->setUserCompany($user, $data);
                    if(isset($response->original['error']) && $response->original['error']){
                        return $response;
                    }
                }
                $responseArr['user_id']=$user->hrid;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function createUser($user, $data)
    {
        // $user = new  User();
        // $user->saveModelRegister($data);
        $this->userGlobal = $user;
        $userlanghistory = new UserLangHistories();
        $userlanghistory->saveRegisterModel($user->id, $data);
        
       
        // $phonebook = Phonebook::where('phoneNumber', $data['number'])->where('status',1)->first();
        // if($phonebook) return ErrorHelperResponse::returnError('Phone number has already been used',Response::HTTP_FOUND);
        // $phonebookDoubleCHeck = Phonebook::where('phoneNumber', $data['number'])->where('status',0)->first();
        // if(!$phonebookDoubleCHeck){
            // $phonebook = new Phonebook();
            // $phonebook->saveModel($user->id, $data);
        // }
        
        $password = Password::where('user_id', $user->id)->first();
        if($password) return ErrorHelperResponse::returnError('Password for user with given id is set, please change user_id',Response::HTTP_FOUND);
        $password = new Password();
        $password->saveModel($user->id, $data);
        if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null') $this->addUserScoutGroup($user, $data);
        if(isset($data['email']) && $data['email'] && $data['email']!='null'){
            $email = new Email();
            $email->saveModel($user->id, $data);
        }
       
        if(isset($data['ref_id']) && $data['ref_id'] &&  $data['ref_id'] !='null') $this->addUserRefMoney($user, $data);   
        return true;
    }
    public function createUserCompany($user, $data)
    {
        $company = new Company();
        $company->saveModel($user->id, $data);
        $companyAdd= new CompanyAddress();
        $companyAdd->saveModelByIds($company->id, $data);
    }
    public function updateUserCompany($user, $data){
        $company = Company::where('user_id', $user->id)->first();
        if($company){
            $company->updateCompanyNameModel($data);
        }else{
            $this->createUserCompany($user, $data);
        }
    }
    public function setUserCompany($user, $data)
    {
        $company = Company::find($data['company_id']);
        if($company){
           $checkMembership = CompanyMembers::where('member_id', $user->id)->first();
            if($checkMembership){
                $responseArr['error']=true;
                $responseArr['message'] = 'User with given id has been already member of a company';
                return response()->json($responseArr, Response::HTTP_FOUND);
            }else{
                $companyMembers = new CompanyMembers();
                $companyMembers->saveModel($user->id, $data);
                return true;
            }
        }
        $responseArr['error']=true;
        $responseArr['message'] = 'Company or User with given id not found';
        return response()->json($responseArr, Response::HTTP_NOT_FOUND);
    }
    public function addUserScoutGroup($user, $data)
    {
        $userscout = User::where('hrid', $data['ref_id'])->first();
        if($userscout){
            $group_id = Scout::where('scout_id', $userscout->id)->first();
            if(($data['role']=='Company Owner' || $data['role']=='Employee' ) &&  isset($data['scout_id'])  && $group_id){
                $groupMembers = new GroupMemberLists();
                $groupMembers->saveModel($group_id->group_id, $user->id, $data);
            }
        }
        
    }
    public function addUserRefMoney($user, $data)
    {
        
        if(isset($data['ref_id']) && $data['ref_id']){
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
        
    }
    public function updateUserScoutGroup($user, $data)
    {
       
        $userScout = User::where('hrid',$data['scout_id'])->first();
        if($userScout){
            $group_id = Scout::where('scout_id', $userScout->id)->first();
            if(($data['role']=='Company Owner' || $data['role']=='Employee' ) &&  isset($data['scout_id'])  && $group_id){
                $groupMembers = GroupMemberLists::where('group_id',$group_id->group_id)->where('memberID',$user->id)->first();
                if($groupMembers){
                    $groupMembers->delete();
                }
                $groupMembers = new GroupMemberLists();
                $groupMembers->saveModel($group_id->group_id, $user->id, $data);
            } 
        }
          
    }
    public function updateUserRefMoney($data)
    {
        $userref = User::where('hrid', $data['ref_id'])->first();
        if($userref && isset($data['ref_id']) && $data['ref_id']){
            $iqc =  Iqc::where('user_id', $userref->id)->first();
            if(!$iqc){
                $iqc = new Iqc();
                $iqc->saveModel($data,$userref->id, 5,1,'ref link');
            }
        }
        
    }
    public function updateUserGlobal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'number'=>'required|size:12',
            'firstName'=>'required|max:190',
            'lastName'=>'required|max:190',
            'gender'=>'required|min:1|max:2',
            'birthDate'=>'required|date_format:d-m-Y',
            'role'=>'required',
            'language'=>'required',
            'password'=>'required|min:8',
            'scout_id'=>'nullable',
            'email'=>'nullable|email',
            'ref_id'=>'nullable'
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
        $this->userGlobal = $user;
        // dd($user);
        $number = Phonebook::where('phoneNumber',$data['number'])->first();
       
        if($number){
            if($number->user_id!= $user->id){
                $responseArr['error']=true;
                $responseArr['message'] = 'Number exists in database'.$number->user_id.'-'.$user->id;
                return response()->json($responseArr, Response::HTTP_FOUND);
            }
        }
        $phonebook = Phonebook::where('user_id',$user->id)->first();
        if(!$phonebook){
            $responseArr['error']=true;
            $responseArr['message'] = 'Connected number not exist';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $user, $phonebook){
                
                $user->updateModelGlobal($data);
                $password = Password::where('user_id', $user->id)->first();
                if($password){
                    $password->updateModel($data);
                }else{
                    $password = new Password();
                    $password->saveModel($user->id, $data);
                }
                
                if(isset($data['email']) && $data['email']){
                    $email = Email::where('user_id', $user->id)->first();
                    if($email){
                       
                        $email->updateModel($data);
                        
                    }else{
                        $email = new Email();
                        $email->saveModel($user->id, $data);
                    }
                }
               
                $phonebook->updateModel($data);

               
                if($data['role']=='Company Owner'){
                    
                    $validator = Validator::make($data, [
                        'companyName'=>'required|min:3',
                        'city_id'=>'required|numeric|min:1',
                        'region_id'=>'required|numeric|min:1',
                        'addressType'=>'required|numeric|min:0|max:1',
                        'street'=>'required',
                        'house'=>'required',
                        'longitude'=>'nullable',
                        'latitude'=>'nullable',
                    ]);
                    if ($validator->fails()) {
                        $responseArr['error']=true;
                        $responseArr['message'] = $validator->errors();
                        return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
                    }
                    if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null'){
                        $usersc = User::where('hrid',$data['scout_id'] )->where('role','Scout')->first();
                        if(!$usersc){
                            $responseArr['error']=true;
                            $responseArr['message'] ='Scout with given id not found';
                            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        }
                        $this->userScout = $usersc;
                    }
                    $this->updateUserCompany($user, $data);
                }else{
                    $validator = Validator::make($data, [
                        'company_id'=>'required',
                    ]);
                    if ($validator->fails()) {
                        $responseArr['error']=true;
                        $responseArr['message'] = $validator->errors();
                        return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
                    }
                    if(isset($data['ref_id']) && $data['ref_id'] && $data['ref_id']!='null'){
                       
                        $refUser = User::where('hrid',$data['ref_id'] )->first();
                        if(!$refUser){
                            $responseArr['error']=true;
                            $responseArr['message'] ='Scout with given id not found';
                            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
                        }
                        $this->refUser = $refUser;
                        
                    }
                    $company = Company::find($data['company_id']);
                    if($company){
                        $checkMembership = CompanyMembers::where('member_id', $user->id)->first();
                        if($checkMembership){
                            $checkMembership->deleteModel($data);
                        }
                        $companyMembers = new CompanyMembers();
                        $companyMembers->saveModel($user->id, $data);
                    }
                    
                }
                if(isset($data['scout_id']) && $data['scout_id'] && $data['scout_id']!='null') $this->updateUserScoutGroup($user, $data);
                
                if(isset($data['ref_id']) && $data['ref_id'] &&  $data['ref_id'] !='null') $this->updateUserRefMoney($data);  
               
                $responseArr['user_id']=$user->hrid;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
