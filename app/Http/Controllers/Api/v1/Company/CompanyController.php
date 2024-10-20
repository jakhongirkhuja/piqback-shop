<?php

namespace App\Http\Controllers\Api\v1\Company;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeamUserAddRequest;
use App\Models\Company;
use App\Models\Company\TeamAdress;
use App\Models\CompanyAddress;
use App\Models\CompanyAddressHistories;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamHistories;
use App\Models\CompanyTeamListHistories;
use App\Models\CompanyTeamLists;
use App\Models\CompanyTeams;
use App\Models\Email;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    public function showCompanySearch()
    {
        $search = request()->s;
        if(strlen($search)<2){
            $responseArr['error']=true;
            $responseArr['message'] = 'Search parametr not given';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        if($search){
            $companies = Company::select('id','companyName')->where('companyName', 'ilike','%'.$search.'%')->get();
            $responseArr['companies']=$companies;
            $responseArr['message'] = 'Success';
            return response()->json($responseArr, Response::HTTP_OK);
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Search parametr not given';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
    }
    public function showAllCompanies()
    {
        $search = request()->search;
        $pagination = request()->paginate;
        $company_id = request()->company_id;
        
        if($company_id){
            $company    = Company::with('companyadress','companyadress.city','companyadress.region')->find($company_id);
            $companyHistoryUser = [];
            $companyMembersUser = [];
            
            if($company){
                $owner = User::with('phonebook')->find($company->user_id);
                $companyHistory =\App\Models\CompanyHistories::where('company_id', $company->id)->get();
                if(count($companyHistory)==0){
                    $companyHistory =\App\Models\CompanyHistories::where('company_id', null)->where('companyName', $company->companyName)->get();
                }
               
                if(count($companyHistory)>0){
                    foreach($companyHistory as $k=>$hist){
                        $user = User::with('phonebook')->find($hist->user_id);
                        
                        if($user){
                            $companyHistoryUser[$k]['companyName'] = $hist->companyName;
                            $companyHistoryUser[$k]['status'] = $hist->status==0? 'Created' : 'Updated';
                            $companyHistoryUser[$k]['user'] =$user;
                            $companyHistoryUser[$k]['created_at'] = $hist->created_at;
                        }
                    }
                }
                $companyMembers = \App\Models\CompanyMembers::where('company_id',$company->id)->get();
                
                if(count($companyMembers)>0){
                    foreach($companyMembers as $k=>$member){
                        $user = User::with('phonebook')->find($member->member_id);
                        if($user){
                            $companyMembersUser[$k]['user'] = $user;
                            $companyMembersUser[$k]['id'] = $member->id;
                            $companyMembersUser[$k]['status'] = $member->memberStatus==0? 'Not approved' : 'Approved';
                            
                            $companyMembersUser[$k]['created_at'] = $member->created_at;
                        }
                    }
                }
                $responseArr['company']=$company;
                $responseArr['companyAddress']=$company? $company->companyadress : [];
                $responseArr['companyHistory']=$companyHistoryUser;
                $responseArr['companyMembers']=$companyMembersUser;
                $responseArr['user']=$owner;
                return response()->json($responseArr, Response::HTTP_OK);
            }else{
                $responseArr['error']=true;
                $responseArr['message'] = 'Company  not  exist, please update Company List';
                return response()->json($responseArr, Response::HTTP_NOT_FOUND);
            }
            
            
            
        }else{
            if($search){
                $companies = Company::with('companyadress','companymembers','companyadress.city','companyadress.region')->where('companyName', 'ilike','%'.$search.'%')->latest()->paginate($pagination? $pagination : 100);
            }else{
                $companies = Company::with('companyadress','companymembers','companyadress.city','companyadress.region')->latest()->paginate($pagination? $pagination : 100);
            }
            
            $responseArr['companies']=$companies;
            return response()->json($responseArr, Response::HTTP_OK);
        }
        
    }
    public function companyUpdateOwner(Request $request)
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
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company with given id not  exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User with given id not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyMember = CompanyMembers::where('company_id', $data['company_id'])->where('member_id', $user->id)->first();
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
                $companyMembers =CompanyMembers::where('company_id',$company->id)->get();
                
                if(count($companyMembers)>0){
                    foreach($companyMembers as $k=>$member){
                        $user2 = User::with('phonebook')->find($member->member_id);
                        if($user2){
                            $companyMembersUser[$k]['user'] = $user2;
                            $companyMembersUser[$k]['status'] = $member->memberStatus==0? 'Not approved' : 'Approved';
                            
                            $companyMembersUser[$k]['created_at'] = $member->created_at;
                        }
                    }
                }
                $responseArr['companyMembers']=isset($companyMembersUser)? $companyMembersUser : [];
                $responseArr['user']=$user;
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function companyUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'companyName'=>'required|string|min:6',
            'city_id'=>'required|numeric',
            'region_id'=>'required|numeric',
            'longitude'=>'nullable|numeric',
            'latitude'=>'nullable|numeric',
            'addressline1'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::with('companyadress','companyadress.city','companyadress.region')->find($data['company_id']);
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company with given id not  exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyNameCheck = Company::where('companyName', $data['companyName'])->where('user_id','!=', $company->user_id)->first();
        if($companyNameCheck){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company Name must be Unique, please use another name';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function ()use ($company, $data) {
                $company->updateCompanyNameModel($data);
                $companyAddress = CompanyAddress::where('company_id', $company->id)->first();
                if($companyAddress){
                    $companyAddress->updateModel($data);
                }else{
                    $companyAdd= new CompanyAddress();
                    $companyAdd->country_id = 1;
                    $companyAdd->company_id = $company->id;
                    $companyAdd->city_id = $data['city_id'];
                    $companyAdd->region_id = $data['region_id'];
                    $companyAdd->addressline1 = $data['addressline1'];
                    $companyAdd->addressType = 0;
                    $companyAdd->longitude = $data['longitude'];
                    $companyAdd->latitude = $data['latitude'];
                    $companyAdd->save();
                    $companyAdressHistory = new CompanyAddressHistories();
                    $companyAdressHistory->saveModel($companyAdd, $data);
                }
                
                $responseArr['company']=$company;
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function companyDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::with('companyadress','companyadress.city','companyadress.region')->find($data['company_id']);
        if(!$company){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company with given id not  exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $groupListCHeck = GroupCompanyLists::where('company_id', $data['company_id'])->first();
        if($groupListCHeck){
            $responseArr['error']=true;
            $responseArr['message'] = 'Company is exist inside group, please first remove from group list';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        $companyMembers = CompanyMembers::where('company_id', $company->id)->get();
        if(count($companyMembers)>0){
            foreach ($companyMembers as $key => $companyMember) {
                $user =  User::with('phonebook')->find($companyMember->member_id);
                if($user && $user->phonebook){
                    $responseArr['error']=true;
                    $responseArr['message'] = 'First delete Compony Members';
                    return response()->json($responseArr, Response::HTTP_FOUND);
                }
            }
        }
        try {
            $res = DB::transaction(function ()use ($company, $data,$companyMembers) {
                foreach ($companyMembers as $key => $companyMember) {
                    $user =  User::find($companyMember->member_id);
                    if($user){
                        $companyMember->delete();
                    }
                }
                $owner = User::find($company->user_id);
                if($owner){
                    $phonebook = Phonebook::where('user_id', $owner->id)->first();
                    if($phonebook)  $phonebook->deleteModel($data);
                    $email = Email::where('user_id', $owner->id)->first();
                    if($email) $email->deleteModel($data);
                    $password = Password::where('user_id', $owner->id)->first();
                    if($password) $password->deleteModel($data);
                    $owner->deleteModel($data);
                }
                $company->deleteModel($data);
                $responseArr['message']='Company fully deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function companyGetById($company_id)
    {
        return response()->json(Company::find($company_id), Response::HTTP_OK);
    }
    public function companyTeams($company_id)
    {
        if(request()->team_id){
            $companyTeams = CompanyTeams::with('companyTeamList','companyTeamAddress')->where('company_id',$company_id)->where('id', request()->team_id)->first();
        }else{
            $companyTeams = CompanyTeams::with('companyTeamList','companyTeamAddress')->where('company_id',$company_id)->orderby('teamName','asc')->paginate(100);
        }
        
        $company = Company::find($company_id);
        $arr['companyTeams'] = $companyTeams;
        $arr['company'] = $company;
        return response()->json($arr, Response::HTTP_OK);
    }
    public function companyTeamCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'teamName'=>'required|string|max:190',
            'teamType'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($request->teamType==0 || $request->teamType==false){
            $validatorAddress = Validator::make($request->all(), [
                'city_id'=>'required',
                'region_id'=>'required',
                'addressType'=>'required',
                'addressline'=>'required',
            ]);
            if ($validatorAddress->fails()) {
                $responseArr['error']=true;
                $responseArr['message'] = $validator->errors();
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }
        }
        $companyTeams = CompanyTeams::where('teamName', $data['teamName'])->where('company_id',$data['company_id'])->first();
        if($companyTeams){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team name exist, please try another name';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($data) {
                $companyTeam = new CompanyTeams();
                $companyTeam->saveModel($data,'created');
                if($data['teamType']==0 || $data['teamType']==false){
                    $companyTeamAddress = new TeamAdress();
                    $companyTeamAddress->saveModel($data,'created', $companyTeam->id);
                }
                $responseArr['message']='Company team has been added';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
       
    }
    public function companyTeamUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id'=>'required',
            'company_id'=>'required',
            'teamName'=>'required|string|max:190',
            'teamType'=>'required'
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        if($request->teamType==0 || $request->teamType==false){
            $validatorAddress = Validator::make($request->all(), [
                'city_id'=>'required',
                'region_id'=>'required',
                'addressType'=>'required',
                'addressline'=>'required',
            ]);
            if ($validatorAddress->fails()) {
                $responseArr['error']=true;
                $responseArr['message'] = $validator->errors();
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }
        }
        $companyTeam = CompanyTeams::find($data['team_id']);
        if(!$companyTeam){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyTeams = CompanyTeams::with('companyTeamAddress')->where('teamName', $data['teamName'])->where('company_id',$data['company_id'] )->where('id','!=',$data['team_id'])->first();
        if($companyTeams){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team name exist, please try another name';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($companyTeam, $data) {
                $companyTeam->saveModel($data, 'updated');
                if($data['teamType']==0 || $data['teamType']==false){
                    if($companyTeam->companyTeamAddress){
                        $companyTeam->companyTeamAddress->saveModel($data,'updated',$data['team_id']);
                    }else{
                        $companyTeamAddress = new TeamAdress();
                        $companyTeamAddress->saveModel($data,'created',$data['team_id']);
                    }
                }
                
                $responseArr['message']='Company team has been updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
       
    }
    public function companyTeamDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id'=>'required',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $companyTeam = CompanyTeams::find($data['team_id']);
        if(!$companyTeam){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $teamcheckUser = CompanyTeamLists::where('team_id',$data['team_id'])->first();
        if($teamcheckUser){
            $responseArr['error']=true;
            $responseArr['message'] = 'First delete Company Team Users';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($companyTeam, $data) {
                $companyTeam->deleteModel($data, 'deleted');
                $responseArr['message']='Company team has been deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function companyTeamsUserList($team_id)
    {
        $companyTeam = CompanyTeams::find($team_id);
        if(!$companyTeam){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $allTeams =CompanyTeams::where('company_id', $companyTeam->company_id)->pluck('id')->toArray();
        
        
        $teamMembers = CompanyTeamLists::where('team_id',$team_id)->pluck('teamMember')->toArray();
        
        $allMembers = CompanyTeamLists::whereIn('team_id',$allTeams)->pluck('teamMember')->toArray();
        $companyEmployes  = CompanyMembers::where('company_id',$companyTeam->company_id)->whereNotIn('member_id', $allMembers)->pluck('member_id')->toArray();

        $teamUsers = [];
        if(count($teamMembers)>0){
            foreach ($teamMembers as $key => $teamMember) {
               $user = User::with('phonebook')->find($teamMember);
               if($user){
                 $ar['user_id'] = $user->id;
                 $ar['name'] = $user->firstName.' '.$user->lastName;
                 $ar['phoneNumber'] = $user->phonebook->phoneNumber;
                 $teamUsers[] = $ar;
               }
            }
        }
        $notAlocated = [];
       
        if(count($companyEmployes)>0){
            foreach ($companyEmployes as $key => $newDiffArray) {
               $user = User::with('phonebook')->find($newDiffArray);
               if($user){
                 $ars['user_id'] = $user->id;
                 $ars['name'] = $user->firstName.' '.$user->lastName;
                 $ars['phoneNumber'] = $user->phonebook->phoneNumber;
                 $notAlocated[] = $ars;
               }
            }
        }
        $newresponse['teamMembers'] = $teamUsers;
        $newresponse['notAlocated'] = $notAlocated;
        return response()->json($newresponse,200);
    }
    public function companyTeamUserAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id'=>'required',
            'user_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $companyTeam = CompanyTeams::find($data['team_id']);
        if(!$companyTeam){
            $responseArr['error']=true;
            $responseArr['message'] = 'Team not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $user = User::find($data['user_id']);
        if(!$user){
            $responseArr['error']=true;
            $responseArr['message'] = 'User not exist';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $checkUser = CompanyTeamLists::where('teamMember',$data['user_id'])->first();
        if($checkUser){
            $responseArr['error']=true;
            $responseArr['message'] = 'User exists in team';
            return response()->json($responseArr, Response::HTTP_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($data) {
                $companyTeamList = new CompanyTeamLists();
                $companyTeamList->saveModel($data,0);
                $responseArr['message']='Company team has been added';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function companyTeamUserDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id'=>'required',
            'user_id'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $checkUser = CompanyTeamLists::where('teamMember',$data['user_id'])->where('team_id',$data['team_id'])->first();
        if(!$checkUser){
            $responseArr['error']=true;
            $responseArr['message'] = 'User not found in this team';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($checkUser, $data) {
                
                $checkUser->saveModel($data,1);
                $responseArr['message']='Company team has been added';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    public function companyMemberStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyMember_id'=>'required',
            'memberStatus'=>'required|min:0|max:1'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        $companyMember = CompanyMembers::find($data['companyMember_id']);
        if(!$companyMember){
            return ErrorHelperResponse::returnError('Company Member with with given id is not found',Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function () use ($data,$companyMember) {
                
                $companyMember->updateMemberStatus($data);
                $responseArr['message']='Company Member updated';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function companyExport(Request $request)
    {
        return Excel::download(new \App\Exports\CompanyExport, 'companies.xlsx');
    }
}
