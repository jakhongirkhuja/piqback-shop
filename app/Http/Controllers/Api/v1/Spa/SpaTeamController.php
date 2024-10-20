<?php

namespace App\Http\Controllers\Api\v1\Spa;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\Company\TeamAdress;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamLists;
use App\Models\CompanyTeams;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpaTeamController extends Controller
{
    public function companyTeams()
    {
        $user = auth()->user();
        $companyTeams = [];
        $company = null;
        if($user->role=='Company Owner'){
            $company = Company::where('user_id', $user->id)->first();
            if($company){
                $company_id = $company->id;
                if(request()->team_id){
                    $companyTeams = CompanyTeams::with('companyTeamList','companyTeamAddress')->where('company_id',$company_id)->where('id', request()->team_id)->first();
                }else{
                    $companyTeams = CompanyTeams::with(['companyTeamList','companyTeamAddress'])->where('company_id',$company_id)->orderby('teamName','asc')->take(100)->get();
                }
                $companyTeams->map(function($item) {
                    if($item->companyTeamAddress){
                        $city = City::find($item->companyTeamAddress->city_id);
                        $region = Region::find($item->companyTeamAddress->region_id);
                        $item->city_ru =$city? $city->name_ru : '' ; 
                        $item->city_uz = $city? $city->name_uz : '' ;
                        $item->region_ru =$region? $region->name_ru : '' ; 
                        $item->region_uz = $region? $region->name_uz : '' ;
                        $item->street = $item->companyTeamAddress->addressline;
                    }
                    return $item;
               });
            }
            
        }
        
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
            $lang['ru']= 'Названия Команды существует';
            $lang['uz']= 'Kommanda nomi foydalanilgan';
            $responseArr['message'] = $lang;
            
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
                $responseArr['message'] = $validatorAddress->errors();
                return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
            }
        }
        $companyTeam = CompanyTeams::find($data['team_id']);
        if(!$companyTeam){
            $responseArr['error']=true;
            $lang['ru']= 'Команда не найдена';
            $lang['uz']= 'Kommanda topilmadi';
           
            $responseArr['message'] = $lang;
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $companyTeams = CompanyTeams::with('companyTeamAddress')->where('teamName', $data['teamName'])->where('company_id',$data['company_id'] )->where('id','!=',$data['team_id'])->first();
        if($companyTeams){
            $responseArr['error']=true;
            $lang['ru']= 'Названия Команды существует';
            $lang['uz']= 'Kommanda nomi foydalanilgan';
            $responseArr['message'] = $lang;
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
            $lang['ru']= ' Команда не существует';
            $lang['uz']= 'Kommanda topilmadi';
            $responseArr['message'] = $lang;
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
        $teamcheckUser = CompanyTeamLists::where('team_id',$data['team_id'])->first();
        if($teamcheckUser){
            $responseArr['error']=true;
            $lang['ru']= 'Сначала удалите учеников';
            $lang['uz']= 'Avval talabalarni olib tashlang';
            $responseArr['message'] = $lang;
            
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
                 $ar['team_id'] = $team_id;
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
    public function companyTeamsUserNotAlocated()
    {
       
        $user = auth()->user();
        $notAlocated = [];
        if($user->role=='Company Owner'){
            $company = Company::where('user_id', $user->id)->first();
            $allTeams =CompanyTeams::where('company_id', $company->id)->pluck('id')->toArray();
            $allMembers = CompanyTeamLists::whereIn('team_id',$allTeams)->pluck('teamMember')->toArray();
            $companyEmployes  = CompanyMembers::where('company_id',$company->id)->whereNotIn('member_id', $allMembers)->pluck('member_id')->toArray();
    
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
        }
        
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
                $responseArr['message']='Company team has been Deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
