<?php

namespace App\Http\Controllers\Api\v1\Course;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamLists;
use App\Models\Course\Category;
use App\Models\Course\CategoryHistory;
use App\Models\Course\CategoryInfo;
use App\Models\Course\Course;
use App\Models\Course\CourseHistories;
use App\Models\Course\CourseInfoHistories;
use App\Models\Course\CourseInfos;
use App\Models\Course\CourseLog;
use App\Models\Course\CoursePin;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\GroupMemberLists;
use App\Models\Quizzes\QuizLog;
use App\Models\Quizzes\Quizz;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function categoryList()
    {
        if(request()->category_id){
            $category = Category::select('id','categoryIcon','categoryName','access','*')->where('id', request()->category_id)->first();
        }else{
            $category = Category::select('id','categoryIcon','categoryName','access','*')->paginate(50);
        }
        return response()->json($category, Response::HTTP_OK);
    }
    public function pinList($category_id){
        $pinList = CoursePin::with('course.infos')->where('category_id',$category_id)->orderby('pinOrder','asc')->get();
        return response()->json($pinList, Response::HTTP_OK);
    }
    public function pinSubmit(Request $request, $category_id){
        $validator = Validator::make($request->all(), [
            'orders'=>'required',
            'courses'=>'required',
        ]);

        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $coursesExplode = explode(",",$data['courses']);
        $ordersExplode = explode(",",$data['orders']);
        $coursePins = CoursePin::where('category_id', $category_id)->first();
        if(!$coursePins){
            return ErrorHelperResponse::returnError('Pin with given category not found',Response::HTTP_NOT_FOUND);
        }
        $coursesInsideExists = CoursePin::where('category_id', $category_id)->whereIn('course_id', $coursesExplode)->get();
        if($coursesInsideExists->count()==0){
            return ErrorHelperResponse::returnError('Pin list inside Courses list not exist',Response::HTTP_NOT_FOUND);
        }
        try {
            $res = DB::transaction(function() use ($data, $coursesInsideExists,$coursesExplode, $ordersExplode){
                foreach ($coursesInsideExists as $key => $coursesInsideExist) {
                    $key = array_search($coursesInsideExist->course_id, $coursesExplode); 
                    if($key){
                        $pinOrder = isset($ordersExplode[$key])? $ordersExplode[$key] : 0; 
                        $coursesInsideExist->pinOrder=(int)$pinOrder;
                    }else{
                        $coursesInsideExist->pinOrder=0;
                    }
                    $coursesInsideExist->save();
                }
                $responseArr['pin'] = $coursesInsideExists;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
            
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function categoryPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoryIcon'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryName'=>'required',
            'access'=>'required',
        ]);

        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $category = new Category();
                $category->saveModel($data);
                $responseArr['category'] =$category;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
            
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function categoryPostAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoryIcon_ru'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryIcon_uz'=>'required|image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryName_ru'=>'required',
            'categoryName_uz'=>'required',
            'access'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        try {
            $res = DB::transaction(function() use ($data){
                $category = new Category();
                $category->saveModelAll($data);
                $responseArr['category'] =$category;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_CREATED);
            });
            return $res;
            
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function categoryPostEdit(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'categoryIcon'=>'image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryName'=>'required',
            'language'=>'required',
            'access'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::find($category_id);

        if($category){
            try {
                $res = DB::transaction(function() use($category, $data){
                    $category->updateModel($data);
                    $responseArr['category'] =$category;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }else{
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function categoryPostEditNew(Request $request, $category_id)
    {
        $validator = Validator::make($request->all(), [
            'categoryIcon_ru'=>'image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryIcon_uz'=>'image|mimes:jpg,png,jpeg,gif,svg|max:512|',
            'categoryName_ru'=>'required',
            'categoryName_uz'=>'required',
            'access'=>'required',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $category = Category::find($category_id);

        if($category){
            try {
                $res = DB::transaction(function() use($category, $data){
                    $category->updateModelAll($data);
                    $responseArr['category'] =$category;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        }else{
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function categoryPostDelete(Request $request, $category_id)
    {
        
        $data = $request->all();
        $category = Category::find($category_id);
        if($category){
            $courses = Course::where('category_id', $category_id)->first();
            if(!$courses){
                try {
                    $res= DB::transaction(function() use ($category, $data){
                        $categoryHistory  = new CategoryHistory();
                        $categoryHistory->saveModel($category, $data);
                        $coursePins = CoursePin::where('category_id', $category->id)->get();
                        if($coursePins->count()>0){
                            foreach ($coursePins as $key => $coursePin) {
                                $coursePin->deleteModel('deleted',$data);
                            }
                        }
                        // StandardAttributes::setSA('category_histories',$categoryHistory->id,'deactivated',request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone'],'pgsql3');
                        $responseArr['category_id'] =$category->id;
                        $responseArr['message'] = 'Deleted';
                        $category->delete();
                        return response()->json($responseArr, Response::HTTP_OK);
                    });
                    return $res;
                } catch (\Throwable $th) {
                    return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                
            }
            return ErrorHelperResponse::returnError('First delete all courses which related to category',Response::HTTP_FOUND);
        }else{
            return ErrorHelperResponse::returnError('Category not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function getCourseStatistics($course_id)
    {
        $courseLog = CourseLog::where('course_id',$course_id)->get();
        $course = Course::find($course_id);
         $users = [];
         $arr =[];
         $id = 0;
         $companyName = '';
         $city = '';
         $region = '';
         $addressline1 = '';
         foreach($courseLog as $k=>$clog){
             $user = User::with('phonebook')->find($clog->user_id);
             if($user){
                 if($user->role=='Company Owner'){
                     $company = Company::with('companyadress')->where('user_id', $user->id)->first();
                     if($company){
                        $companyName = $company->companyName;
                        $companyAddress = $company->companyadress;
                        if($companyAddress){
                            $getcity = City::find($companyAddress->city_id);
                            $getRegion  = Region::find($companyAddress->region_id);
                            if($getcity && $getRegion){
                                $city = $getcity->name_ru;
                                $region = $getRegion->name_ru;
                                $addressline1= $companyAddress->addressline1;
                            }
                            
                        }
                        
                     }
                     
                 }else{
                     $companyMember = CompanyMembers::where('member_id', $user->id)->first();
                     if($companyMember){
                        $company = Company::with('companyadress')->find($companyMember->company_id);
                        if($company){
                            $companyName = $company->companyName;
                            $companyAddress = $company->companyadress;
                            if($companyAddress){
                                $getcity = City::find($companyAddress->city_id);
                                $getRegion  = Region::find($companyAddress->region_id);
                                if($getcity && $getRegion){
                                    $city = $getcity->name_ru;
                                    $region = $getRegion->name_ru;
                                    $addressline1= $companyAddress->addressline1;
                                }
                                
                            }
                        }
                     } 
                     
                 }
                 if(count($course->lessons)>0){
                    foreach($course->lessons as $lesson){
                         $users = [];
                         $quiz = Quizz::where('lesson_id',$lesson->id)->first();
                         if($quiz){
                              $logs = QuizLog::where('quiz_id',$quiz->id)->where('user_id',$user->id)->first();
                         }
                         $id ++;
                         $companyTeamList = CompanyTeamLists::with('companyTeam.companyTeamAddress')->where('teamMember',$user->id)->first();
                         $teamName = '-';
                         $teamcity = '';
                         $teamregion = '';
                         $teamaddresstreet = '';
                         if($companyTeamList  && $companyTeamList->companyTeam){
                            $team = $companyTeamList->companyTeam;
                            $teamName = $team->teamName;
                            $teamAdress = $team->companyTeamAddress;
                            if($teamAdress){
                                $getcity = City::find($teamAdress->city_id);
                                $getRegion  = Region::find($teamAdress->region_id);
                                if($getcity && $getRegion){
                                    $teamcity = $getcity->name_ru;
                                    $teamregion = $getRegion->name_ru;
                                    $teamaddresstreet= $teamAdress->addressline;
                                }
                            }
                         }
                         $users['user'] = $user->firstName.' '. $user->lastName; 
                         $users['role'] = $user->role; 
                         $users['phoneNumber'] = $user->phonebook?->phoneNumber; 
                         $users['companyName'] = $companyName;
                         $users['companyCity'] = $city;
                         $users['companyRegion'] = $region;
                         $users['companyStreet'] = $addressline1;
                         $users['teamName'] = $teamName;
                         $users['teamCity'] = $teamcity;
                         $users['teamRegion'] = $teamregion;
                         $users['teamStreet'] = $teamaddresstreet;
                         if(isset($logs) && $logs){
                             $users['quizResult'] = 'Result: '.$logs->numberOfRightAnswers.'. attampts: '.$logs->quizAttempt.' time(s)';
                         }else{
                             $users['quizResult'] ='Not passed';
                         }
                         $users['created_at'] = Carbon::parse($clog->created_at)->toDateString();
                      array_push($arr, $users);
                        
                    }
              }
             }
             
         }
         return response()->json($arr);
    }
    public function getCourseStatisticsNotPassed($course_id)
    {
        $arr =[];
        $course = Course::with('courselogAll')->find($course_id);
        if($course){
            $logsExist = [];
            if($course->courselogAll->count()>0){
                $logsExist[] = $course->courselogAll->pluck('user_id');
            }
            $users = [];
            if($course->courseForGroup!=null){
                $groupCompanyList = GroupCompanyLists::with('company.companymembers')->where('group_id', $course->courseForGroup)->get();
                if($groupCompanyList->count()>0){
                    
                    foreach ($groupCompanyList as $key => $groupCompany) {
                        $company = $groupCompany->company;
                        if($company){
                            $users[]= $company->user_id;
                            $members = $company->companymembers;
                            if($members->count()>0){
                                foreach ($members as $key => $member) {
                                    $users[] = $member->member_id;
                                }
                            }
                        }
                    }
                }else{
                    $groupMemberLists =GroupMemberLists::select('memberID')->where('group_id', $course->courseForGroup)->get();
                    if($groupMemberLists->count()){
                        foreach ($groupMemberLists as $key => $member) {
                            $users[] = $member->memberID;
                        }
                    }
                }
            }
            $notPassed = [];
            foreach($users as $k=>$user){
              
                if(isset($logsExist[0])){
                    if(!in_array($user, $logsExist[0]->toArray())){
                       $notPassed[] = $user;
                    }
                }
            }
           
            if(count(array_unique($notPassed))>0){
                foreach(array_unique($notPassed) as $k=>$us){
                    $user = User::with('phonebook')->find($us);
                    if($user){
                        if($user->role=='Company Owner'){
                            $company = Company::where('user_id', $user->id)->first();
                            if($company) $companyName = $company->companyName;
                            
                        }else{
                            $companyMember = CompanyMembers::where('member_id', $user->id)->first();
                            if($companyMember){
                               $company = Company::find($companyMember->company_id);
                               if($company) $companyName = $company->companyName;
                            } 
                            
                        }
                        $companyTeamList = CompanyTeamLists::with('companyTeam')->where('teamMember',$user->id)->first();
                        $teamName = '-';
                        if($companyTeamList  && $companyTeamList->companyTeam){
                        $teamName = $companyTeamList->companyTeam->teamName;
                        }
                       $usernotPassed['user'] = $user->firstName.' '. $user->lastName; 
                       $usernotPassed['phoneNumber'] = $user->phonebook?->phoneNumber; 
                       $usernotPassed['role'] = $user->role; 
                       $usernotPassed['teamName'] = $teamName;
                       $usernotPassed['companyName'] = $companyName;
                       array_push($arr, $usernotPassed);
                    }
                    
                }
            }
        }
       
         
         return response()->json($arr);
    }
    public function courseList()
    {
        $courseItself = request()->id;
        $category_id = request()->category_id;
        if($courseItself){
            $course = Course::with('infos','lessons.quizes')->where('id', $courseItself)->first();
            return response()->json($course, Response::HTTP_OK);
        }
        if($category_id){
            $course = Course::with('infos','lessons.quizes')->where('category_id', $category_id)->orderby('id','desc')->get();
            return response()->json($course, Response::HTTP_OK);
        }else{
            if(request()->search){
                $course = Course::with('infos','lessons.quizes')->whereHas('infos', function($q){
                    $q->where('courseTitleName->ru', 'ilike', '%'.request()->search.'%')
                    ->orwhere('courseTitleName->uz', 'ilike', '%'.request()->search.'%');
                })->orderby('id','desc')->paginate(20);
            }else{
                $course = Course::with('infos','lessons.quizes')->orderby('id','desc')->paginate(request()->show=='all'? 1000 : 20);
            }
            return response()->json($course, Response::HTTP_OK);
        }
        return ErrorHelperResponse::returnError('Parameter not given',Response::HTTP_NOT_FOUND);
    }
    public function courseSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'startDate'=>'required|date_format:Y-m-d',
            'courseMonetized'=>'required|numeric|min:0|max:1',
            'language'=>'required|max:3',
            'courseTitleName'=>'required|max:190',
            'courseInfo'=>'required',
            'coursePrice'=>'required|numeric',
            'courseBanner'=>'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data=  $request->all();
        if(Category::find($data['category_id'])){
            try {
                $res =DB::transaction(function () use ($data){
                    $course = new Course();
                    $courseHistory = new CourseHistories();
                    $courseInfo = new CourseInfos();
                    // $courseInfoHistory = new CourseInfoHistories();
                    $savedCourse = $course->saveModel($data);
                    $courseHistory->saveModel($data);
                    $courseInfo->saveModel($savedCourse->id,$data);
                    // $courseInfoHistory->saveModel($savedCourse->id,$data, $courseInfo);
                    $responseArr['course'] =Course::with('infos')->where('id', $course->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Exception $e) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$e,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Category with given Id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function courseSubmitAdd(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'startDate'=>'required|date_format:Y-m-d',
            'courseMonetized'=>'required|numeric|min:0|max:1',
            'courseTitleName_ru'=>'required|max:190',
            'courseInfo_ru'=>'required',
            'courseTitleName_uz'=>'required|max:190',
            'courseInfo_uz'=>'required',
            'coursePrice'=>'numeric',
            'courseBanner_ru'=>'required|image|mimes:jpg,png,jpeg,svg|max:2048',
            'courseBanner_uz'=>'required|image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);

        
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data=  $request->all();
        if($data['category_id']=='special'){
            $validator = Validator::make($request->all(), [
                'courseForGroup'=>'required',
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
        }
        $accassPrivate = false;
        if($data['category_id']!='special'){
            $categoryFind = Category::find($data['category_id']);
            
            if($categoryFind && $categoryFind->access){
                $accassPrivate = true;
                $validator = Validator::make($request->all(), [
                    'courseForGroup'=>'required',
                ]);
                if ($validator->fails()) {
                    return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
                }
            }
        }
        
        if($data['category_id']=='special'){
            $categoryFind =true;
        }
        
        if($categoryFind){
            try {
                $res =DB::transaction(function () use ($data, $accassPrivate){
                    $course = new Course();
                    $course->saveModelAll($data, $accassPrivate);
                    $responseArr['course'] =$course;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Exception $e) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$e,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Category with given Id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function courseEdit(Request $request, $course_id)
    {
        
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'startDate'=>'required|date_format:Y-m-d',
            'courseMonetized'=>'required|numeric|min:0|max:1',
            'language'=>'required|max:3',
            'courseInfo'=>'required',
            'coursePrice'=>'required|numeric',
            'courseTitleName'=>'required|max:190',
            'courseBanner'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data=  $request->all();
        $course = Course::find($course_id);
        if($course){
           
            try {
                $res =DB::transaction(function () use ($course, $data){
                   
                    $courseHistory = new CourseHistories();
                    $courseInfo = CourseInfos::where('course_id', $course->id)->first();
                    $courseInfoHistory = new CourseInfoHistories();
                    $savedCourse = $course->updateModel($data);
                    $courseHistory->updateModel($data);
                    $courseInfo->updateModel($savedCourse->id,$data);
                    $courseInfoHistory->updateModel($savedCourse->id,$data, $courseInfo);
                   
                    $responseArr['course'] =Course::with('infos')->where('id', $course->id)->first();
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Exception $e) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$e,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course  with given Id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function courseEditNew(Request $request, $course_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'required',
            'startDate'=>'required|date_format:Y-m-d',
            'courseMonetized'=>'required|numeric|min:0|max:1',
            'courseTitleName_ru'=>'required|max:190',
            'courseInfo_ru'=>'required',
            'courseTitleName_uz'=>'required|max:190',
            'courseInfo_uz'=>'required',
            'coursePrice'=>'numeric',
            'courseBanner_ru'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
            'courseBanner_uz'=>'image|mimes:jpg,png,jpeg,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        
        $data=  $request->all();
        if($data['category_id']=='special'){
            $validator = Validator::make($request->all(), [
                'courseForGroup'=>'required',
            ]);
            if ($validator->fails()) {
                return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
            }
        }
        $accassPrivate = false;
        if($data['category_id']!='special'){
            $categoryFind = Category::find($data['category_id']);
            
            if($categoryFind && $categoryFind->access){
                $accassPrivate = true;
                $validator = Validator::make($request->all(), [
                    'courseForGroup'=>'required',
                ]);
                if ($validator->fails()) {
                    return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
                }
            }
        }
        
        if($data['category_id']=='special'){
            $categoryFind =true;
        }
        $course = Course::find($course_id);
        $courseInfo = CourseInfos::where('course_id', $course->id)->first();
        if($course && $courseInfo){
            try {
                $res =DB::transaction(function () use ($course, $data,$courseInfo,$accassPrivate){
                    $course->updateModelNew($data,$accassPrivate);
                    $courseInfo->updateModelNew($course->id,$data);
                    $responseArr['course'] =$course;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Exception $e) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$e,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course  with given Id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function courseDelete(Request $request, $course_id)
    {
        $course = Course::find($course_id);
       
        $data = $request->all();
        if($course){
            if($course->lessons()->count()>0){
                return ErrorHelperResponse::returnError('First Delete Lessons of the Course',Response::HTTP_FOUND);
            }
            try {
                $res = DB::transaction(function () use ($course, $data){
                    $courseHistory = new CourseHistories();
                    $courseHistory->deleteModel($course, $data);
                    $courseInfo = CourseInfos::where('course_id',$course->id)->first();
                    $courseInfoHistory = new CourseInfoHistories();
                    $courseInfoHistory->deleteModel($data, $courseInfo);
                    $coursePin = CoursePin::where('course_id', $course->id)->first();
                    if($coursePin){
                        $coursePin->deleteModel('deleted',$data);
                    }
                    $course->delete();
                    $responseArr['message'] = 'Deleted';
                    return response()->json($responseArr, Response::HTTP_OK);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return ErrorHelperResponse::returnError('Course  with given Id not found',Response::HTTP_NOT_FOUND);
    }

    public function logs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required',
            'course_id'=>'required',
            'status'=>'required|min:0|max:1'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid', $data['user_id'])->first();
        if(!$user){
            return ErrorHelperResponse::returnError('User with given id not found',Response::HTTP_NOT_FOUND);
        }
        $course = Course::find($data['course_id'])->first();
        if(!$course){
            return ErrorHelperResponse::returnError('Course with given id not found',Response::HTTP_NOT_FOUND);
        }
        $courseLog = CourseLog::where('course_id',$data['course_id'])->where('user_id',$user->id)->first();
        if(!$courseLog){
            $courseLog = new CourseLog();
        }
        try {
            $res = DB::transaction(function () use ($courseLog, $user, $data){
                $courseLog->saveOrUpdate($user->id, $data);
                $responseArr['course_log'] = $courseLog;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
