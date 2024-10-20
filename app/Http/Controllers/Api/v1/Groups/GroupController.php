<?php

namespace App\Http\Controllers\Api\v1\Groups;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Course\Course;
use App\Models\Groups\CompanyRestrictionList;
use App\Models\Groups\Group;
use App\Models\Groups\GroupCompanyListHistories;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\GroupHistories;
use App\Models\Groups\GroupMemberListHistories;
use App\Models\Groups\GroupMemberLists;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Groups\TargetFilter;
use App\Models\Inbox\InboxMessage;
use App\Models\Phonebook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function grouplist()
    {
        $group_id = request()->group_id;
        if($group_id){
            $group = Group::find($group_id);
            $targetfilter = TargetFilter::where('group_id',$group_id)->first();
            $res['group']= $group;
            $res['target']= $targetfilter;
            return response()->json($res, Response::HTTP_OK);
        }
        $groups = Group::latest()->paginate(request()->show=='all'? 1000 : 200);
        return response()->json($groups,Response::HTTP_OK);
    }
    public function groupCompanylist($group_id)
    {
        $groups = GroupCompanyLists::where('group_id',$group_id)->get();
        $companies  = [];
        if(count($groups)>0){
            foreach ($groups as $key => $group) {
                $company = Company::find($group->company_id);
                if($company){
                    $companies[] = $company;
                }
            }
        }
        return response()->json($companies,Response::HTTP_OK);
    }
    public function groupMemberslist($group_id)
    {
        $groups = GroupMemberLists::where('group_id',$group_id)->get();
        $members  = [];
        $arry = [];
        if(count($groups)>0){
            foreach ($groups as $key => $group) {
                $user = User::with('phonebook')->find($group->memberID);
                if($user){
                    $members['user'] = $user;
                    $members['memberlist'] = $group;
                    $arry[]=$members;
                }
            }
        }
        return response()->json($arry,Response::HTTP_OK);
    }
    public function groupPostName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groupName'=>'required|unique:pgsql2.groups',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        
        $group = new Group();
        $group->groupName = $data['groupName'];
        if($group->save()){
            $groupHistory = new GroupHistories();
            $groupHistory->group_id = $group->id;
            $groupHistory->groupName = $data['groupName'];
            $groupHistory->moderated = auth()->user()->id;
            if($groupHistory->save()){
                StandardAttributes::setSA('group_histories',$groupHistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');     
                $responseArr['group_id'] =$group->id;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_CREATED);
            }
        }
        return ErrorHelperResponse::returnError('Group not created',Response::HTTP_BAD_GATEWAY);
    }
    public function groupNameEdit(Request $request, $id)
    {
        
        $validator = Validator::make($request->all(), [
            'groupName'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $checkgroup = Group::where('groupName',$data['groupName'])->where('id','!=', $id)->first();
        if($checkgroup){
            return ErrorHelperResponse::returnError('Group with this name is found',Response::HTTP_FOUND);
        }
        $group = Group::find($id);
       
        if($group){
            $group->groupName = $data['groupName'];
            if($group->save()){
                $groupHistory = new GroupHistories();
                $groupHistory->group_id = $id;
                $groupHistory->groupName = $data['groupName'];
                $groupHistory->moderated = auth()->user()->id;
               
                if($groupHistory->save()){
                    StandardAttributes::setSA('group_histories',$groupHistory->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');     
                    $responseArr['group_id'] =$id;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                }
            }
            return ErrorHelperResponse::returnError('Group not updated',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
    }
    public function addGroupMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required|numeric',
            'group_id'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();

        $user = User::find($data['user_id']);
        if(!$user){
            return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
        }
        $group = Group::find($data['group_id']);
        if(!$group){
            return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
        }
        $groupMembers = new GroupMemberLists();
        $groupMembers->group_id = $data['group_id'];
        $groupMembers->memberID = $data['user_id'];
        if($groupMembers->save()){
            $groupMembersHistory = new GroupMemberListHistories();
            $groupMembersHistory->group_id = $data['group_id'];
            $groupMembersHistory->memberID = $data['user_id'];
            $groupMembersHistory->moderated = auth()->user()->id;
            if($groupMembersHistory->save()){
                StandardAttributes::setSA('group_member_list_histories',$groupMembersHistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');     
                $responseArr['group_id'] =$group->id;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            }
        }
        return ErrorHelperResponse::returnError('Group member not added',Response::HTTP_BAD_GATEWAY);
    }

    public function deleteGroupMembers(Request $request, $member_id)
    {
        $groupmember = GroupMemberLists::find($member_id);
        if($groupmember){
            $data = $request->all();
            $groupMemberaddhistory =new GroupMemberListHistories();
            $groupMemberaddhistory->memberID = $groupmember->memberID;
            $groupMemberaddhistory->group_id = $groupmember->group_id;
            $groupMemberaddhistory->moderated = auth()->user()->id;
            if($groupMemberaddhistory->save()){
                $groupmember->delete();
                StandardAttributes::setSA('group_member_list_histories',$groupMemberaddhistory->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');  
                $responseArr['message'] = 'Deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            }
            return ErrorHelperResponse::returnError('Company from Group not removed',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ErrorHelperResponse::returnError('Given ID company not found',Response::HTTP_NOT_FOUND);
    }

    public function addGroupCompanies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'group_id'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::with('companymembers')->find($data['company_id']);
        if(!$company){
            return ErrorHelperResponse::returnError('Company with given id not found',Response::HTTP_NOT_FOUND);
        }
        $group = Group::find($data['group_id']);
        if(!$group){
            return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
        }
        $groupListCHeck = GroupCompanyLists::where('group_id',$data['group_id'])->where('company_id', $data['company_id'])->first();
        if($groupListCHeck){
            return ErrorHelperResponse::returnError('Company exists',Response::HTTP_FOUND);
        }
        $groupcompanyadd = new GroupCompanyLists();
        $groupcompanyadd->company_id = $data['company_id'];
        $groupcompanyadd->group_id = $data['group_id'];
        if($groupcompanyadd->save()){
            $groupcompanyaddhistory =new GroupCompanyListHistories();
            $groupcompanyaddhistory->company_id = $data['company_id'];
            $groupcompanyaddhistory->group_id = $data['group_id'];
            $groupcompanyaddhistory->moderated = auth()->user()->id;
            if($groupcompanyaddhistory->save()){
                StandardAttributes::setSA('group_company_list_histories',$groupcompanyaddhistory->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2'); 
                
                $userCompanyOwnercheck = GroupMemberLists::where('group_id', $data['group_id'])->where('memberID', $company->user_id)->first();
                if(!$userCompanyOwnercheck){
                    $groupMembers = new GroupMemberLists();
                    $groupMembers->saveModel($data['group_id'],$company->user_id, $data);
                }
                if($company->companymembers && count($company->companymembers)>0){
                    
                    foreach ($company->companymembers as $key => $member) {
                        $user = User::find($member->member_id);
                        if($user){
                            $usercheck = GroupMemberLists::where('group_id', $data['group_id'])->where('memberID', $member->member_id)->first();
                            if(!$usercheck){
                                $groupMembers = new GroupMemberLists();
                                $groupMembers->saveModel($data['group_id'],$member->member_id, $data);
                            }
                        }
                    }
                }
                $responseArr['group_id'] = $data['group_id'];
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            }
            return ErrorHelperResponse::returnError('Group Company History not added',Response::HTTP_BAD_GATEWAY);
        }
        return ErrorHelperResponse::returnError('Company  not added to Group',Response::HTTP_BAD_GATEWAY);
    }
    public function deleteGroupCompany(Request $request, $company_id)
    {
        $groupcompany = GroupCompanyLists::where('company_id',$company_id)->first();
        if($groupcompany){
            $data = $request->all();
            $groupcompanyaddhistory =new GroupCompanyListHistories();
            $groupcompanyaddhistory->company_id = $company_id;
            $groupcompanyaddhistory->group_id = $groupcompany->group_id;
            $groupcompanyaddhistory->moderated = auth()->user()->id;
            if($groupcompanyaddhistory->save()){
                $groupcompany->delete();
                StandardAttributes::setSA('group_company_list_histories',$groupcompanyaddhistory->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');  
                
                $company = Company::with('companymembers')->find( $company_id);
                if($company){
                    $userCompanyOwnercheck = GroupMemberLists::where('group_id', $groupcompanyaddhistory->group_id)->where('memberID', $company->user_id)->first();
                    if($userCompanyOwnercheck){
                        $userCompanyOwnercheck->deleteModel($data);
                    }
                    $companyMembers = $company->companymembers;
                    if($companyMembers &&  count($companyMembers)>0){
                    
                        foreach ($companyMembers as $key => $member) {
                            $usercheck = GroupMemberLists::where('group_id', $groupcompanyaddhistory->group_id)->where('memberID', $member->member_id)->first();
                            if($usercheck){
                                $usercheck->deleteModel($data);
                            }
                        }
                    }
                }
                $responseArr['message'] = 'Deleted';
                return response()->json($responseArr, Response::HTTP_OK);
            }
            return ErrorHelperResponse::returnError('Company from Group not removed',Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ErrorHelperResponse::returnError('Given ID company not found',Response::HTTP_NOT_FOUND);
    }
    public function groupstatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status'=>'required|numeric|max:1|min:0',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $group = Group::find($id);
        if(!$group){
            return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
        }
        $data = $request->all();
        $group->currentStatus = $data['status'];
        if($group->save()){
            $groupHistory = new GroupHistories();
            $groupHistory->group_id = $id;
            $groupHistory->groupName = $group->groupName;
            $groupHistory->moderated = auth()->user()->id;
            if($groupHistory->save()){
                StandardAttributes::setSA('group_histories',$groupHistory->id,$data['status']==0? 3 : 4,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql2');     
                $responseArr['group_id'] =$id;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            }
        }
        return ErrorHelperResponse::returnError('Group Status not updated',Response::HTTP_BAD_GATEWAY);
    }

    public function groupDelete(Request $request, $group_id)
    {
        $group = Group::find($group_id);
        $data = $request->all();
        if($group){
            $groupMember = GroupMemberLists::where('group_id', $group->id)->first();
            if($groupMember){
                return ErrorHelperResponse::returnError('Group Members not empty, first delete members inside group',Response::HTTP_NOT_FOUND);
            }
            $groupCompanies = GroupCompanyLists::where('group_id', $group->id)->first();
            if($groupCompanies){
                return ErrorHelperResponse::returnError('Group Companies not empty, first delete companies inside group',Response::HTTP_NOT_FOUND);
            }
            $inboxMessage =InboxMessage::where('phonebook_id', $group->id)->first();
            if($inboxMessage){
                return ErrorHelperResponse::returnError('Inbox Message with group exist, first delete group inside InboxMessage name: '.$inboxMessage->titleName,Response::HTTP_NOT_FOUND);
            }
            $courseGroupExist =Course::where('courseForGroup', $group->id)->first();
            if($courseGroupExist){
                return ErrorHelperResponse::returnError('Course  with this group exist is allocated, first delete group inside Course ',Response::HTTP_NOT_FOUND);
            }
            try {
                $res = DB::transaction(function () use ($group, $data){
                    $targetfilter = TargetFilter::where('group_id', $group->id)->first();
                    $groupHistory = new GroupHistories();
                    $groupHistory->deleteModel($group, $data);
                    $group->delete();
                    if($targetfilter){
                        $targetfilter->deleteModel($data);
                    }
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return ErrorHelperResponse::returnError('Group   with given Id not found',Response::HTTP_NOT_FOUND);
    }
    public function listMemberRestriction($group_id)
    {
        $comapnyRestriction = MemberRestrictionList::where('group_id',$group_id)->get();
        return response()->json($comapnyRestriction,Response::HTTP_OK);
    }
    public function addMemberRestriction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'group_id'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $phoneNumber = Phonebook::where('user_id',$data['user_id'])->first();
        if(!$phoneNumber){
            return ErrorHelperResponse::returnError('User with given number not found',Response::HTTP_NOT_FOUND);
        }
        $group = Group::find($data['group_id']);
        if(!$group){
            return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
        }
        $memberGroupList = GroupMemberLists::where('group_id', $data['group_id'])->where('memberID',$data['user_id'])->first();
        if(!$memberGroupList){
            return ErrorHelperResponse::returnError('User Not exist in Member List inside Group',Response::HTTP_NOT_FOUND);
        }
        $memberRestrictionListCHeck = MemberRestrictionList::where('group_id',$data['group_id'])->where('memberID', $data['user_id'])->first();
        if($memberRestrictionListCHeck){
            return ErrorHelperResponse::returnError('Member exists in this striction  list',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($phoneNumber,$data){
                $memberRestrictionList = new MemberRestrictionList();
                $data['memberID']= $data['user_id'];
                $data['memberPhone'] = $phoneNumber->phoneNumber;
                $memberRestrictionList->saveModel($data, 0);
                $responseArr['memberRestrictionList'] =$memberRestrictionList;
                $responseArr['message'] = 'Member Restriction List has been created';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function deleteMemberRestriction(Request $request, $member_id)
    {
        $data = $request->all();
        $memberRestriction = MemberRestrictionList::find($member_id);
        if(!$memberRestriction){
            return ErrorHelperResponse::returnError('Id not found',Response::HTTP_NOT_FOUND);
        }
        
        $memberRestriction->deleteModel($data);
        $responseArr['message'] = 'Deleted';
        return response()->json($responseArr, Response::HTTP_OK);
    }
    public function listCompanyRestriction($group_id)
    {
        $comapnyRestriction = CompanyRestrictionList::with('company')->where('group_id',$group_id)->get();
        return response()->json($comapnyRestriction,Response::HTTP_OK);
    }
    public function addCompanyRestriction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required',
            'group_id'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $company = Company::with('companymembers')->find($data['company_id']);
        if(!$company){
            return ErrorHelperResponse::returnError('Company with given id not found',Response::HTTP_NOT_FOUND);
        }
        $group = Group::find($data['group_id']);
        if(!$group){
            return ErrorHelperResponse::returnError('Group with given id not found',Response::HTTP_NOT_FOUND);
        }
        $companyGroupList = GroupCompanyLists::where('group_id', $data['group_id'])->where('company_id',$data['company_id'])->first();
        if(!$companyGroupList){
            return ErrorHelperResponse::returnError('Company Not exist in Company List inside Group',Response::HTTP_NOT_FOUND);
        }
        $companyRestrictionListCHeck = CompanyRestrictionList::where('group_id',$data['group_id'])->where('company_id', $data['company_id'])->first();
        if($companyRestrictionListCHeck){
            return ErrorHelperResponse::returnError('Company exists in this estrictionLis list',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($company,$data){
                $companyRestrictionList = new CompanyRestrictionList();
                $companyRestrictionList->saveModel($data, 0);
                $companyOwnercheck  = MemberRestrictionList::where('memberID',$company->user_id )->where('group_id',$data['group_id'] )->first();
                if(!$companyOwnercheck){
                    $memberRestriction = new MemberRestrictionList();
                    
                    $userPhoneNew = Phonebook::where('user_id', $company->user_id)->first();
                    $data['memberID'] =  $company->user_id;
                    $data['memberPhone'] = $userPhoneNew? $userPhoneNew->phoneNumber : '99';
                    $memberRestriction->saveModel($data, 0);
                }
                if($company->companymembers->count()>0){
                    foreach ($company->companymembers as $key => $companymembers) {
                        $userPhone = Phonebook::where('user_id', $companymembers->member_id)->first();
                        if($userPhone){
                            $userRestrictionCheck = MemberRestrictionList::where('memberID',$companymembers->member_id )->where('group_id',$data['group_id'] )->first();
                            if(!$userRestrictionCheck){
                                $memberRestriction = new MemberRestrictionList();
                                $data['memberID'] =  $companymembers->member_id;
                                $data['memberPhone'] = $userPhone->phoneNumber;
                                $memberRestriction->saveModel($data, 0);
                            } 
                        }
                        
                    }
                }
                $responseArr['companyRestrictionList'] =$companyRestrictionList;
                $responseArr['message'] = 'Company Restriction List has been created';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    public function deleteCompanyRestriction(Request $request, $company_id)
    {
        $data = $request->all();
        $comapnyRestriction = CompanyRestrictionList::with('company.companymembers')->find($company_id);
        if(!$comapnyRestriction){
            return ErrorHelperResponse::returnError('Id not found',Response::HTTP_NOT_FOUND);
        }
        if($comapnyRestriction->company && $comapnyRestriction->company->companymembers->count()>0 ){
            foreach ($comapnyRestriction->company->companymembers as $key => $members) {
                $userRestrictionCheck = MemberRestrictionList::where('memberID',$members->member_id )->where('group_id',$comapnyRestriction->group_id)->first();
                if($userRestrictionCheck){
                    $userRestrictionCheck->deleteModel($data);
                } 
            }
        }
        $comapnyRestriction->deleteModel($data);
        $responseArr['message'] = 'Deleted';
        return response()->json($responseArr, Response::HTTP_OK);
    }
}
