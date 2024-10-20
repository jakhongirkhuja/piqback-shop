<?php

namespace App\Http\Controllers\Api\v1\Spa;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyMembers;
use App\Models\Course\Category;
use App\Models\Course\Course;
use App\Models\Course\CourseLog;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Groups\TargetFilter;
use App\Models\Inbox\InboxMessage;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonLog;
use App\Models\Money\Iqc;
use App\Models\Quizzes\QuizLog;
use App\Models\Quizzes\Quizz;
use App\Models\Quizzes\ReQuizLog;
use App\Models\User;
use App\Services\Spa\GetCourseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SpaCourseController extends Controller
{
    public function courseInfo()
    {
        $id = request()->id;
        $latest = request()->latest;
        if($id){
            $course = Course::with('category','infos','lessons.quizes.quizlog','courselog','lessons.lessonlog')->where('id', $id)->first();
            return response()->json($course, Response::HTTP_OK);
        }
        if($latest){
            $course = Course::with('category','infos','lessons.quizes','lessons.quizes.quizlog','courselog','lessons.lessonlog')->where('courseTypeByAccess',1)->latest()->take(50)->get();
            return response()->json($course, Response::HTTP_OK);
        }
        $course = Course::with('category','infos','lessons.quizes','lessons.quizes.quizlog','courselog','lessons.lessonlog')->where('courseTypeByAccess',1)->latest()->paginate(50);
        return response()->json($course, Response::HTTP_OK);
    }
    public function courseSearch(){
        if(strlen(request()->s)>4){
            $course = Course::with('category','getinfo','lessons.quizes','lessons.quizes.quizlog','courselog','lessons.lessonlog')
            ->whereHas('getinfo', function($query){
                return $query->where('courseTitleName->ru','ilike','%'.request()->s.'%')
                ->orWhere('courseInfo->ru','ilike','%'.request()->s.'%')
                ->orWhere('courseInfo->uz','ilike','%'.request()->s.'%')
                ->orWhere('courseTitleName->uz','ilike','%'.request()->s.'%');
            })
            ->where('courseTypeByAccess',1)
            ->latest()
            ->take(50)
            ->get();
        }else{
            $course = []; 
        }
        
        return response()->json($course, Response::HTTP_OK);
    }
    public function myCourses()
    {
        $courseLog = CourseLog::with('course.getinfo','course.category','course.lessons.quizes.quizlog','course.lessons.lessonlog')->whereHas('course.lessons.lessonlog')->whereDoesntHave('course.lessons.quizes.quizlog')->orderby('created_at','desc')->where('user_id', auth()->user()->id)->get();
        return response()->json($courseLog, Response::HTTP_OK);
    }
    public function myPassedCourses()
    {
        $coursePassedLog = CourseLog::with('course.getinfo','course.category','course.lessons.quizes.quizlog')->has('course.lessons.quizes.quizlog')->orderby('created_at','desc')->where('user_id', auth()->user()->id)->get();
        return response()->json($coursePassedLog, Response::HTTP_OK);
    }
    public function myCoursesNew()
    {
        $category_id = request()->category_id;
        $type = request()->type;
        $compid = 0;
        $companyMembers = [];
        $groups = [];
        if($type=='special'){
            if(auth()->user()->role=='Company Owner'){
            
                $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
                if($company) $compid = $company->id;
                
            }
            if(auth()->user()->role=='Employee'){
                $company = CompanyMembers::where('member_id', auth()->user()->id)->first();
                if($company) $compid = $company->company_id;
            }
            $groupss = GroupCompanyLists::select('group_id')->where('company_id',$compid )->get()->pluck('group_id')->toArray();
            $userrestrice = MemberRestrictionList::where('memberID',auth()->user()->id)->get()->pluck('group_id')->toArray();
            
            foreach ($groupss as $group) {
            
                if(!in_array($group, $userrestrice)){
                    $groups[] = $group;
                }
            }
        }
        if($type=='special'){
            $courseLog = CourseLog::
            with(['course'=>function($query) use ($groups){
                $query->whereIn('courseForGroup',$groups);
            }],['course.getinfo'],['course.category'],['course.lessons.quizes.quizlog'])
            ->where('user_id', auth()->user()->id)
            ->whereHas('course.lessons.lessonlog')
            ->whereDoesntHave('course.lessons.quizes.quizlog')
            ->when($category_id, function ($query) use ($category_id) {
                $query->whereHas('course', function ($query) use ($category_id) {
                    if($category_id!=0){
                        $query->where('category_id', $category_id);
                    }
                    
                });
            })
            ->orderby('created_at','desc')
            
            ->paginate(5);
        }else{
            $courseLog = CourseLog::
            with('course.getinfo','course.category','course.lessons.quizes.quizlog','course.lessons.lessonlog')
            ->where('user_id', auth()->user()->id)
            ->whereHas('course.lessons.lessonlog')
            ->whereDoesntHave('course.lessons.quizes.quizlog')
            ->when($type, function ($query) use ($type,$groups) {
                $query->whereHas('course', function ($query) use ($type,$groups) {
                    if($type=='special'){
                        $query->whereIn('courseForGroup',$groups);
                    }
                });
            })
            ->when($category_id, function ($query) use ($category_id) {
                $query->whereHas('course', function ($query) use ($category_id) {
                    if($category_id!=0){
                        $query->where('category_id', $category_id);
                    }
                    
                });
            })
            ->orderby('created_at','desc')
            
            ->paginate(5);
        }
        

        
        return response()->json($courseLog, Response::HTTP_OK);
    }
    public function myPassedCoursesNew()
    {
        $category_id = request()->category_id;
        $type = request()->type;
        $compid = 0;
        $companyMembers = [];
        $groups = [];
        if($type=='special'){
            if(auth()->user()->role=='Company Owner'){
            
                $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
                if($company) $compid = $company->id;
                
            }
            if(auth()->user()->role=='Employee'){
                $company = CompanyMembers::where('member_id', auth()->user()->id)->first();
                if($company) $compid = $company->company_id;
            }
            $groupss = GroupCompanyLists::select('group_id')->where('company_id',$compid )->get()->pluck('group_id')->toArray();
            $userrestrice = MemberRestrictionList::where('memberID',auth()->user()->id)->get()->pluck('group_id')->toArray();
            
            foreach ($groupss as $group) {
            
                if(!in_array($group, $userrestrice)){
                    $groups[] = $group;
                }
            }
        }
        
        $coursePassedLog = CourseLog::
        with('course.getinfo','course.category','course.lessons.quizes.quizlog')
        ->has('course.lessons.quizes.quizlog')
        ->where('user_id', auth()->user()->id)
        ->when($type, function ($query) use ( $type,$groups) {
            $query->whereHas('course', function ($query) use ($type,$groups) {
                if($type=='special'){
                    $query->whereIn('courseForGroup',$groups);
                }
            });
        })
        ->when($category_id, function ($query) use ($category_id) {
            $query->whereHas('course', function ($query) use ($category_id) {
                
                $query->where('category_id', $category_id);
            });
        })
        ->orderby('created_at','desc')
        
        ->paginate(5);
        return response()->json($coursePassedLog, Response::HTTP_OK);
    }
    public function courses(GetCourseService $getCourseService)
    {
        return $getCourseService->getcourses();
    }
    public function getCategories(GetCourseService $getCourseService)
    {
        return $getCourseService->getCategory();
    }
    
    public function getLessons(GetCourseService $getCourseService)
    {
        return $getCourseService->getLessons();
    }
    public function getLesson(GetCourseService $getCourseService)
    {
        return $getCourseService->getLesson();
    }
    public function checkQuizTry()
    {
        $reQuizLogs = ReQuizLog::where('user_id', auth()->user()->id)->whereDate('created_at', \Carbon\Carbon::today())->get();
        $countExist = true;
        $lesson = Lesson::find(request()->lesson_id);

        if($lesson && $lesson->quizes && count($reQuizLogs)>=3){
                
            foreach ($reQuizLogs as $reQuizLog) {
                if($reQuizLog->quiz_id == $lesson->quizes->id){
                    $countExist = false;
                    break;
                }
            }
            
            
            
        }else{
            $countExist = false;
        }
        if($countExist){
            return response()->json('ok',200);
        }
        return response()->json('limit exceeded',404);
    }
    public function lessonQuizPost(Request $request, GetCourseService $getCourseService)
    {
        $validator = Validator::make($request->all(), [
            'quiz_id'=>'required',
            'lesson_id'=>'required',
            'question'=>'required',
            'timeLeft'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        return $getCourseService->lessonQuizPost($data);
    }
    public function lessonQuizAccessCheck()
    {
        $reQuizLogs = ReQuizLog::where('user_id', auth()->user()->id)->whereDate('created_at', \Carbon\Carbon::today())->get()->orderby('quiz_id');
        if(count($reQuizLogs)>=3){
            return response()->json('no access', Response::HTTP_FOUND);
        }else{
            return response()->json('access', 200);
        }
    }
    public function saveLessonLog(Request $request, GetCourseService $getCourseService)
    {
        $validator = Validator::make($request->all(), [
            'typeContent'=>'required',
            'lesson_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        return $getCourseService->saveLessonLog($data);
    }
    public function saveCourseLog(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $courseLog = null;
        try {
            $course = Course::with('courselog')->find($data['course_id']);
            if($course){
                $courseLog = $course->courselog;
                if($courseLog){
                    $courseLog->progress = 0;
                    $courseLog->totalContent = 0;
                    if(!$courseLog->status){
                        $courseLog->status = 0;
                    }
                    $courseLog->save();
                    
                }else{
                    $courseLog = new CourseLog();
                    $courseLog->user_id = auth()->user()->id;
                    $courseLog->course_id =$data['course_id'];
                    $courseLog->progress = 0;
                    $courseLog->totalContent = 0;
                    $courseLog->status = 0;
                    $courseLog->save();
                }
            }
        } catch (\Throwable $th) {
            
        }
        return response()->json($courseLog, 200);
    }
    public function getCourseStatistics($course_id)
    {
        $company = Company::with('companymembers')->where('user_id',auth()->user()->id)->first();
        $course = Course::find($course_id);
        $users = [];
        $arr =[];
        if($company && $course){
            $companyMembers = $company->companymembers->pluck('member_id');
            $courseLog = CourseLog::where('course_id',$course_id)->whereIn('user_id', $companyMembers)->get();
            if($courseLog->count()>0){
                
                 foreach($courseLog as $k=>$clog){
                     $user = User::find($clog->user_id);
                     if($user){
                         if(count($course->lessons)>0){
                            foreach($course->lessons as $lesson){
                                 $users = [];
                                 $quiz = Quizz::with('questions')->where('lesson_id',$lesson->id)->first();
                                 if($quiz){
                                      $logs = QuizLog::where('quiz_id',$quiz->id)->where('user_id',$user->id)->first();
                                 }
                                
                                 $users['user'] = $user->firstName.' '. $user->lastName;
                                 $users['created_at'] = Carbon::parse($clog->created_at)->toDateString();
                                 if(isset($logs) && $logs){
                                     $users['quizAttempt'] = $logs->quizAttempt;
                                     $users['quizRightAnswers'] = $logs->numberOfRightAnswers;
                                     $users['quizQuestionTotal'] = $quiz->questions->count();
                                    }else{
                                     $users['quizResult'] =0;
                                     $users['quizRightAnswers'] = 0;
                                     $users['quizQuestionTotal'] = 0;
        
                                 }
                              array_push($arr, $users);
                            }
                      }
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
                $groupCompanyList = GroupCompanyLists::with('company.companymembers')->where('group_id', $course->courseForGroup)->whereHas('company', function($query){
                    return $query->where('user_id',auth()->user()->id);
                })->first();
                if($groupCompanyList){
                        $company = $groupCompanyList->company;
                        if($company){
                            $members = $company->companymembers;
                            if($members->count()>0){
                                foreach ($members as $key => $member) {
                                    $users[] = $member->member_id;
                                }
                            }
                        }
                    
                }
            }
            $notPassed = [];
            foreach($users as $k=>$user){
              
               
                if(!in_array($user, $logsExist[0]->toArray())){
                   $notPassed[] = $user;
                }
            }
           
            if(count(array_unique($notPassed))>0){
                foreach(array_unique($notPassed) as $k=>$us){
                    $user = User::find($us);
                    if($user){
                       $usernotPassed['user'] = $user->firstName.' '. $user->lastName; 
                       $usernotPassed['user_id'] = $user->id;
                       $usernotPassed['user_hrid'] = ''.$user->hrid;
                       array_push($arr, $usernotPassed);
                    }
                    
                }
            }
        }
       
         
         return response()->json($arr);
    }
    public function sendNotification(Request $request, GetCourseService $getCourseService)
    {
        
        $validator = Validator::make($request->all(), [
            'course_id'=>'required',
            'user_ids'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        return $getCourseService->sendNotificationApi($data);
    }











    public function getCategoriesByType(GetCourseService $getCourseService){
        $response['categories'] = Category::where('access', request()->type)->get();
        return response()->json($response, Response::HTTP_OK);
    }
    public function coursesByCategory(GetCourseService $getCourseService)
    {
        return $getCourseService->getcoursesByCategory();
    }
}
