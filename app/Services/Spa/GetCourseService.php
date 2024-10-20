<?php 
namespace App\Services\Spa;

use App\Helper\ErrorHelperResponse;
use App\Models\Company;
use App\Models\CompanyMembers;
use App\Models\Course\Category;
use Illuminate\Support\Str;
use App\Models\Course\Course;
use App\Models\Course\CourseLog;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\MemberRestrictionList;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonLog;
use App\Models\Lottery\Lottery;
use App\Models\Lottery\LotteryLog;
use App\Models\Money\Iqc;
use App\Models\Quizzes\QuestionVariant;
use App\Models\Quizzes\QuizLimit;
use App\Models\Quizzes\QuizLog;
use App\Models\Quizzes\Quizz;
use App\Models\Quizzes\ReQuizLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GetCourseService{

    function getSpecCourse($course_id=null){
        $company = false;
        $compid = 0;
        $companyMembers = [];
        if(auth()->user()->role=='Creator'){
            return Course::with('getinfo','category','lessons.quizes','courselogAll')->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->orderby('created_at','desc')->paginate(50);
        }
        if(auth()->user()->role=='Company Owner'){
            
            $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
            if($company) $compid = $company->id;
            
        }
        if(auth()->user()->role=='Employee'){
            $company = CompanyMembers::where('member_id', auth()->user()->id)->first();
            if($company) $compid = $company->company_id;
        }
        
        if($company){
            $groupss = GroupCompanyLists::select('group_id')->where('company_id',$compid )->get()->pluck('group_id')->toArray();
            $userrestrice = MemberRestrictionList::where('memberID',auth()->user()->id)->get()->pluck('group_id')->toArray();
            $groups = [];
            foreach ($groupss as $group) {
            
                if(!in_array($group, $userrestrice)){
                    $groups[] = $group;
                }
            }
            if(count($groups)>0){
                if($course_id){
                    $courses = Course::with('getinfo','category','lessons.quizes','courselogAll')->where('id', $course_id)->where('id','!=',280)->whereDate('startDate', '<=', Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->whereIn('courseForGroup',$groups )->paginate(50);
                }else{
                    if(request()->owner){
                        $courses = Course::with('getinfo','category','lessons.quizes','courselogIn')->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->where('id','!=',280)->whereIn('courseForGroup',$groups )->orderby('created_at','desc')->paginate(50);
                    }else{
                        $courses = Course::with('getinfo','category','lessons.quizes','courselogAll')->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->where('id','!=',280)->whereIn('courseForGroup',$groups )->orderby('created_at','desc')->paginate(50);
                    }
                }
                return $courses;
            }else{
                return $courses = Course::where('id',0)->paginate(50);
                // remove when ok;
                return $courses = Course::with('getinfo','category','lessons.quizes','courselogAll')->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->orderby('created_at','desc')->paginate(50);
            }
        }
        // remove when ok; just return zero pagination
        return $courses = Course::where('id',0)->paginate(50);
    }
    function getSpecCourseByCategory($category_id=null){
        $company = false;
        $compid = 0;
        $companyMembers = [];
        if(auth()->user()->role=='Creator'){
            return Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog','courselogAll')->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('courseTypeByAccess',0)->orderby('created_at','desc')->paginate(5);
        }
        if(auth()->user()->role=='Company Owner'){
            
            $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
            if($company) $compid = $company->id;
            
        }
        if(auth()->user()->role=='Employee'){
            $company = CompanyMembers::where('member_id', auth()->user()->id)->first();
            if($company) $compid = $company->company_id;
        }
        
        if($company){
            $groupss = GroupCompanyLists::select('group_id')->where('company_id',$compid )->get()->pluck('group_id')->toArray();
            $userrestrice = MemberRestrictionList::where('memberID',auth()->user()->id)->get()->pluck('group_id')->toArray();
            $groups = [];
            foreach ($groupss as $group) {
            
                if(!in_array($group, $userrestrice)){
                    $groups[] = $group;
                }
            }
            if(count($groups)>0){
                if($category_id){
                    $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog','courselogAll')
                    ->whereDate('startDate', '<=', Carbon::now()->addHours(5))
                    ->where(function ($query) {
                        $query->whereNull('endDate')
                              ->orWhere(function ($query) {
                                  $query->whereNotNull('endDate')
                                        ->whereDate('endDate', '>=', \Carbon\Carbon::now()->addHours(5));
                              });
                        
                    })
                    ->where('access',true)
                    ->where('id','!=',280)
                    // ->where(function($q)  {
                    //     // $q->where('courseTypeByAccess', 0)
                    //     $q->orWhere('access', true);
                    // })
                    ->whereIn('courseForGroup',$groups )
                    ->when($category_id, function ($query, $category_id) {
                        $query->where('category_id', $category_id);
                    })->latest()->paginate(60);
                }else{
                    if(request()->owner){
                        $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog', 'courselogIn')->where('id','!=',280)->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('access',true)->whereIn('courseForGroup',$groups )->latest()->paginate(50);
                    }else{
                        $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog','courselogAll')->where('id','!=',280)->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('access',true)->whereIn('courseForGroup',$groups )->latest()->paginate(60);
                    }
                }
                return $courses;
            }else{
                return $courses = Course::where('id',0)->paginate(50);
                // remove when ok;
                return $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog','courselogAll')->where('id','!=',280)->whereDate('startDate', '<=',Carbon::now()->addHours(5))->where('access',true)->latest()->paginate(50);
            }
        }
        // remove when ok; just return zero pagination
        return $courses = Course::where('id',0)->paginate(50);
    }
    public function getCategory(){
       
        $type = request()->type;
    
        $categories = Category::with('courses','courses.lessons.quizes')->get();

        $categories->map(function($items) use ($type){
            
         
             $lessonCount = 0;
            if($items->courses->count()>0){
                foreach($items->courses as $course){
                    if($type=='free'){
                        if($course->courseTypeByAccess==1 && $course->courseMonetized==0){
                            $lessonCount++;
                        }    
                    }
                    if($type=='paid'){
                        if($course->courseTypeByAccess==1 && $course->courseMonetized==1){
                            $lessonCount++;
                            
                        }         
                    }
                     
                     
                }
             
            }
            $items['lessons'] =$lessonCount;
           
        });

        $response['categories'] = $categories;
        return response()->json($response, Response::HTTP_OK);
    }
    
    public function getcourses()
    {
        $params = request()->params;
        $course_id = request()->category_id;
        $test = request()->test;
        if($params=='free'){
            if($test){
                $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
                ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
                ->where('courseMonetized',0)
                ->where('courseTypeByAccess',1)
                ->when($course_id, function ($query, $course_id) {
                    $query->where('category_id', $course_id);
                })
                ->latest()
                ->paginate(50);
            }else{
                $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
                ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
                ->where('courseMonetized',0)
                ->where('courseTypeByAccess',1)
                ->when($course_id, function ($query, $course_id) {
                    $query->where('category_id', $course_id);
                })
                ->where('id','!=',280)
                ->latest()
                ->paginate(50);
            }
        }else if($params =='paid'){
            $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
            ->whereHas('lessons.quizes', function ($query) {
                $query->where('prizeIQC','>' ,0);
            })
            ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
            ->where('courseMonetized',1)
            ->where('courseTypeByAccess',1)
            ->when($course_id, function ($query, $course_id) {
                $query->where('category_id', $course_id);
            })
            ->where('id','!=',280)
            ->latest()
            ->paginate(50);
        }else if($params == 'special'){
            $courses = $this->getSpecCourse();
        }else{
            $courses = Course::where('id',0)->paginate(50);
        }
        $response['courses'] = $courses;
        if(request()->owner){
            if(auth()->user()->role=='Company Owner'){
                $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
                $response['company'] = $company? $company->companymembers : null;
            }
            
        }
        return response()->json($response, Response::HTTP_OK);
    }
    public function getcoursesByCategory()
    {
        $category_id = request()->category_id;
        $params = request()->params;
        $test =  request()->test;
        if($params=='free'){
            if($category_id==8 || $category_id==9 ){
                $courses = $this->getSpecCourseByCategory($category_id);
                $response['courses'] = $courses;
                return response()->json($response, Response::HTTP_OK);
            }
            if($test){
                $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
                ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
                ->where(function ($query) {
                    $query->whereNull('endDate')
                          ->orWhere(function ($query) {
                              $query->whereNotNull('endDate')
                                    ->whereDate('endDate', '>=', \Carbon\Carbon::now()->addHours(5));
                          });
                    
                })
                ->where('courseMonetized',0)
                ->where(function($q)  {
                    // $q->where('courseTypeByAccess', 1);
                    $q->orWhere('access', false);
                    // When board will update by access change  uncomment 
                })
                ->when($category_id, function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->latest()
                ->paginate(60);
            }else{
                $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
                ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
                ->where(function ($query) {
                    $query->whereNull('endDate')
                          ->orWhere(function ($query) {
                              $query->whereNotNull('endDate')
                                    ->whereDate('endDate', '>=', \Carbon\Carbon::now()->addHours(5));
                          });
                    
                })
                ->where('courseMonetized',0)
                ->where('id','!=',280)
                ->where(function($q)  {
                    // $q->where('courseTypeByAccess', 1);
                    $q->orWhere('access', false);
                    // When board will update by access change  uncomment 
                })
                ->when($category_id, function ($query, $category_id) {
                    $query->where('category_id', $category_id);
                })
                ->latest()
                ->paginate(60);

            }
        }else if($params =='paid'){
            $courses = Course::with('getinfo','category','lessons.quizes.quizlog', 'courselog','lessons.lessonlog')
            ->whereHas('lessons.quizes', function ($query) {
                $query->where('prizeIQC','>' ,0);
            })
            ->whereDate('startDate', '<=', \Carbon\Carbon::now()->addHours(5))
            ->where(function ($query) {
                $query->whereNull('endDate')
                      ->orWhere(function ($query) {
                          $query->whereNotNull('endDate')
                                ->whereDate('endDate', '>=', \Carbon\Carbon::now()->addHours(5));
                      });
                
            })
            ->where('courseMonetized',1)
            ->where(function($q)  {
                $q->where('courseTypeByAccess', 1)
                  ->orWhere('access', false);
            })
            ->when($category_id, function ($query, $course_id) {
                $query->where('category_id', $course_id);
            })
            ->latest()
            ->paginate(60);
        }else if($params == 'special'){
            $courses = $this->getSpecCourseByCategory($category_id);
        }else{
            $courses = Course::where('id',0)->paginate(50);
        }
        $response['courses'] = $courses;
        if(request()->owner){
            if(auth()->user()->role=='Company Owner'){
                $company = Company::with('companymembers')->where('user_id', auth()->user()->id)->first();
                $response['company'] = $company? $company->companymembers : null;
            }
            
        }
        return response()->json($response, Response::HTTP_OK);
    }
    public function getLessons()
    {
        $course_id = request()->course_id;
        if($course_id){
            $lessons = Lesson::with('contents','quizes.questions.variants','quizes.quizlog','materials','lessonlog')->where('course_id',$course_id)->get();
            $response['lessons'] = $lessons;
            return response()->json($response, Response::HTTP_OK);
        }else{
            return response()->json('course id not given', Response::HTTP_NOT_FOUND);
        }
    }
    public function getLesson()
    {
        $lesson_id = request()->lesson_id;
        if($lesson_id){
            $lesson = Lesson::with('contents','quizes.questions.variants','quizes.quizlog','materials')->where('id',$lesson_id)->first();
            $response['lesson'] = $lesson;
            return response()->json($response, Response::HTTP_OK);
        }else{
            return response()->json('lesson id not given', Response::HTTP_NOT_FOUND);
        }
    }
    public function saveQuzLimit($quiz)
    {
        
        if($quiz->type=='with limited reward' && $quiz->prizeStatus==false ){
            $quizLimit= QuizLimit::where('quiz_id', $quiz->id)->first();
            if($quizLimit && $quizLimit->prizeLimit==false){
                $counterLimit = $quizLimit->counter;
                $quizLimit->counter = $counterLimit+1;
                if($quiz->prizeLimit<=$counterLimit+1){
                    $quizLimit->prizeLimit = true;
                    $quiz->prizeStatus = true;
                    $quiz->save();
                    $quizLimit->save();
                }
            }else{
                $quizLimit = new QuizLimit();
                $quizLimit->quiz_id = $quiz->id;
                $quizLimit->counter=1;
                if($quiz->prizeLimit<=1){
                    $quizLimit->prizeLimit = true;
                    $quiz->prizeStatus = true;
                    $quiz->save();
                    $quizLimit->save();
                }

            }
        }
    }
    public function checkLottery($quiz){
        try {
            $courseId = $quiz->lesson->course_id;
            $checkLottery = Lottery::where('course_id', $courseId)->first();
            if($checkLottery){
                $limit = $checkLottery->limit;
                $startDate = Carbon::parse($checkLottery->startDate);
                $endDate = Carbon::parse($checkLottery->endDate);
                $currentDate = Carbon::now()->addHours(5);
                $lotteryLogs = LotteryLog::where('lottery_id',$checkLottery->id)->orderBy('order','desc')->get();
                if ($currentDate->between($startDate, $endDate) && count($lotteryLogs)<$limit) {
                    $lastOrder = count($lotteryLogs)==0? 0 : $lotteryLogs[0]->order;
                    $userID = auth()->user()->id;
                    $lotteryCheckUser = false;
                    foreach ($lotteryLogs as $key => $lotteryLog) {
                        if($lotteryLog->user_id==$userID){
                            $lotteryCheckUser = true;
                        }
                    }
                    if(!$lotteryCheckUser){
                        $lotteryCheck = new LotteryLog();
                        $lotteryCheck->saveModel($checkLottery->id,$lastOrder+1);
                        if($lastOrder+1==$limit){
                            $checkLottery->changeStatus([],'deactivated');
                        }
                    }
                }
                
            } 
        } catch (\Throwable $th) {
            
        }
        
    }
    public function lessonQuizPost($data){
       
        $quiz = Quizz::with('lesson')->find($data['quiz_id']);
        $rightAnserrs = 0;
        $rewardIqc = 0;
        if($quiz){
            foreach (json_decode($data['question'], true) as $key => $question) {
                if(count(json_decode($question['variants'], true))>0){
                    $newVariant = json_decode($question['variants'], true);
                    $getVariant = QuestionVariant::with('question')->find($newVariant[0]['id']);
                    if($getVariant){
                        $questionType =$getVariant->question;
                        switch ($questionType->questionType) {
                            case 'single':
                                foreach (json_decode($question['variants'], true) as $key => $variant) {
                                    $variantcheck = QuestionVariant::find($variant['id']);
                                    if($variantcheck){
                                        // dd($variantcheck, $variantcheck->rightAnswer == $variant['choose'],$question['variants'], $variant);
                                        if($variantcheck->rightAnswer == true && $variant['choose'] == true){
                                            $rightAnserrs++;
                                            break;
                                        }
                                    }
                                }
                                break;
                            case 'multiple':
                                $newcounter  = 0;
                                $newcounterRightAnswer  = 0;
                                foreach (json_decode($question['variants'], true) as $key => $variant) {
                                    $variantcheck = QuestionVariant::find($variant['id']);
                                    if($variantcheck){
                                        if($variantcheck->rightAnswer == true){
                                            $newcounter++;
                                        }
                                        // dd($variantcheck, $variantcheck->rightAnswer == $variant['choose'],$question['variants'], $variant);
                                        if($variantcheck->rightAnswer == true && $variant['choose'] == true){
                                            $newcounterRightAnswer++;
                                        }
                                    }
                                }
                                if($newcounter==$newcounterRightAnswer){
                                    $rightAnserrs++;
                                }
                                break;
                            case 'filltheblank':
                                $questionAnswers  = isset($question['question_answers']) && $question['question_answers']!=''? $question['question_answers'] : false;
                                $language =  isset($question['question_language'])? $question['question_language'] : false;
                                $find = false;
                                
                                
                                foreach (json_decode($question['variants'], true) as $key => $variant) {
                                    $variantcheck = QuestionVariant::find($variant['id']);
                                    
                                    if($variantcheck && $variantcheck->keyWords!=null && $questionAnswers){
                                        $jsonGet = json_decode($variantcheck->keyWords, true);
                                        $getKeysRu = explode(',',$jsonGet['ru']);
                                        $getKeysUz = explode(',',$jsonGet['uz']);
                                        $exploadedAnswers  = explode(',',$questionAnswers);
                                        if($language){
                                            if($language=='uz'){
                                                foreach ($getKeysUz as $key => $getKeys) {
                                                    foreach ($exploadedAnswers as $key => $exploadedAnswer) {
                                                        mb_internal_encoding('UTF-8');
                                                        setlocale(LC_CTYPE, 'ru_RU');
                                                        if(mb_strtolower(trim($getKeys)) ==mb_strtolower(trim($exploadedAnswer," "))){
                                                            $find = true;
                                                        }
                                                    }
                                                    
                                                }
                                            }else{
                                                foreach ($getKeysRu as $key => $getKeys) {
                                                    foreach ($exploadedAnswers as $key => $exploadedAnswer) {
                                                        mb_internal_encoding('UTF-8');
                                                        setlocale(LC_CTYPE, 'ru_RU');
                                                        if(mb_strtolower(trim($getKeys))==mb_strtolower(trim($exploadedAnswer," "))){
                                                            $find = true;
                                                        }
                                                    }
                                                    
                                                }
                                            }
                                        }
                                    }
                                }
                                if($find){
                                    $rightAnserrs++;
                                }
                                break;     
                            case 'gapfill':
                                $questionAnswers  = isset($question['question_answers']) && $question['question_answers']!=''? $question['question_answers'] : false;
                                $language =  isset($question['question_language'])? $question['question_language'] : false;
                                $find = false;
                                foreach (json_decode($question['variants'], true) as $key => $variant) {
                                    $variantcheck = QuestionVariant::find($variant['id']);
                                    if($variantcheck && $variantcheck->keyWords!=null && $questionAnswers){
                                        $jsonGet = json_decode($variantcheck->keyWords, true);
                                        $getKeysRu = explode(',',$jsonGet['ru']);
                                        $getKeysUz = explode(',',$jsonGet['uz']);
                                        $exploadedAnswers  = explode(',',$questionAnswers);
                                        if($language){
                                            if($language=='uz'){
                                                foreach ($getKeysUz as $key => $getKeys) {
                                                    foreach ($exploadedAnswers as $str) {
                                                        mb_internal_encoding('UTF-8');
                                                        setlocale(LC_CTYPE, 'ru_RU');
                                                        $modifiedStr = mb_strtolower($str);
                                                        $modifiedKey = mb_strtolower($getKeys);
                                                        if (strpos($modifiedStr, trim($modifiedKey)) !== false) {
                                                            $find = true;
                                                        }
                                                    }
                                                }
                                            }else{
                                                foreach ($getKeysRu as $key => $getKeys) {
                                                    foreach ($exploadedAnswers as $str) {
                                                        mb_internal_encoding('UTF-8');
                                                        setlocale(LC_CTYPE, 'ru_RU');
                                                        $modifiedStr = mb_strtolower($str);
                                                        $modifiedKey = mb_strtolower($getKeys);
                                                        if (strpos($modifiedStr, trim($modifiedKey)) !== false) {
                                                            $find = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if($find){
                                    $rightAnserrs++;
                                }
                                break;            
                            default:
                                foreach (json_decode($question['variants'], true) as $key => $variant) {
                                    $variantcheck = QuestionVariant::find($variant['id']);
                                    if($variantcheck){
                                        // dd($variantcheck, $variantcheck->rightAnswer == $variant['choose'],$question['variants'], $variant);
                                        if($variantcheck->rightAnswer == true && $variant['choose'] == true){
                                            $rightAnserrs++;
                                            break;
                                        }
                                    }
                                }
                                break;
                        }
                    }
                    
                }
                
                
               
            }
           
           if($quiz->numberRightAnswersToPass<=$rightAnserrs){
                $responseArr['passed'] =true;
                $quizLogs = QuizLog::where('quiz_id', $data['quiz_id'])->where('user_id',auth()->user()->id)->first();
                if(!$quizLogs){
                    $quizLogs = new QuizLog();
                    $quizLogs->user_id = auth()->user()->id;
                    $quizLogs->quiz_id= $data['quiz_id'];
                    $quizLogs->quizAttempt = 1;
                }else{
                    $quizLogs->quizAttempt = $quizLogs->quizAttempt +1;
                }
                
                $quizLogs->addressIP = request()->ip();
                $quizLogs->timeLeft = $data['timeLeft'];
                
                $quizLogs->numberOfRightAnswers = $rightAnserrs;
                $quizLogs->save();
                
                

                if($quiz->type=='with limited reward' && $quiz->prizeStatus){

                }else{
                    $iqc = Iqc::where('user_id',  auth()->user()->id)->first();
                    if($iqc){
                            if($quizLogs->status && $quizLogs->status!=null){
                                     $newPrice = 0;
                            }else{
                                if($quizLogs->quizAttempt!=1 ){
                                    $newPrice = $quiz->prizeIQC - (5 * ($quizLogs->quizAttempt-1));
                                }else{
                                    $newPrice = $quiz->prizeIQC;
                                }
                                $quizLogs->status = 1;
                                $quizLogs->prizeOut = $newPrice<=0 ? 0 : $newPrice;
                                $quizLogs->save();
                                $reQuizLog = new ReQuizLog();
                                $reQuizLog->saveModel($quizLogs, $data);
                                
                                
                            }
                            $changedPrice = $newPrice<=0 ? 0 : $newPrice; 
                            $rewardIqc = $changedPrice;
                            $iqc->updateModel($data, $changedPrice,1,'quiz', $quiz->id);
                            if($newPrice>0){
                                if($quiz->type=='with limited reward' && $quiz->prizeStatus==false ){
                                    $quizLimit= QuizLimit::where('quiz_id', $quiz->id)->first();
                                    if($quizLimit && $quizLimit->prizeLimit==false){
                                        $counterLimit = $quizLimit->counter;
                                        $quizLimit->counter = $counterLimit+1;
                                        if($quiz->prizeLimit<=$counterLimit+1){
                                            $quizLimit->prizeLimit = true;
                                            $quiz->prizeStatus = true;
                                            $quiz->save();
                                        }
                                        $quizLimit->save();
                                    }else{
                                        $quizLimit = new QuizLimit();
                                        $quizLimit->quiz_id = $quiz->id;
                                        $quizLimit->counter=1;
                                        if($quiz->prizeLimit<=1){
                                            $quizLimit->prizeLimit = true;
                                            $quiz->prizeStatus = true;
                                            $quiz->save();
                                        }
                                        $quizLimit->save();
                        
                                    }
                                }
                            }
                    }else{
                        if($quizLogs->status && $quizLogs->status!=null){
                            $newPrice = 0;
                        }else{
                            if(($quizLogs->quizAttempt)!=1){
                                $newPrice = $quiz->prizeIQC - (5 * ($quizLogs->quizAttempt-1));
                            }else{
                                $newPrice = $quiz->prizeIQC;
                            }
                            $quizLogs->status = 1;
                            $quizLogs->prizeOut = $newPrice<=0 ? 0 : $newPrice;
                            $quizLogs->save();
                            $reQuizLog = new ReQuizLog();
                            $reQuizLog->saveModel($quizLogs, $data);
                           
                        }
                        
                        $changedPrice = $newPrice<=0 ? 0 : $newPrice;
                        $rewardIqc = $changedPrice;
                        $iqc = new Iqc();
                        $iqc->saveModel($data, auth()->user()->id,$changedPrice,1,'quiz', $quiz->id);
                        if($newPrice>0){
                            if($quiz->type=='with limited reward' && $quiz->prizeStatus==false ){
                                $quizLimit= QuizLimit::where('quiz_id', $quiz->id)->first();
                                if($quizLimit && $quizLimit->prizeLimit==false){
                                    $counterLimit = $quizLimit->counter;
                                    $quizLimit->counter = $counterLimit+1;
                                    if($quiz->prizeLimit<=$counterLimit+1){
                                        $quizLimit->prizeLimit = true;
                                        $quiz->prizeStatus = true;
                                        $quiz->save();
                                        
                                    }
                                    $quizLimit->save();
                                }else{
                                    $quizLimit = new QuizLimit();
                                    $quizLimit->quiz_id = $quiz->id;
                                    $quizLimit->counter=1;
                                    if($quiz->prizeLimit<=1){
                                        $quizLimit->prizeLimit = true;
                                        $quiz->prizeStatus = true;
                                        $quiz->save();
                                        
                                    }
                                    $quizLimit->save();
                    
                                }
                            }
                        }
                    }
                }

                
           }else{
                $responseArr['passed'] =false;
                $quizLogs = QuizLog::where('quiz_id', $data['quiz_id'])->where('user_id', auth()->user()? auth()->user()->id: 1)->first();
                if(!$quizLogs){
                    $quizLogs = new QuizLog();
                    $quizLogs->user_id = auth()->user()->id;
                    $quizLogs->quiz_id= $data['quiz_id'];
                    $quizLogs->quizAttempt = 1;
                }else{
                    $quizLogs->quizAttempt = $quizLogs->quizAttempt +1;
                }
                
                $quizLogs->addressIP = request()->ip();
                $quizLogs->timeLeft = $data['timeLeft'];
                $quizLogs->numberOfRightAnswers = $rightAnserrs;
                
                //dd($quizLogs);
                $quizLogs->save();
                
           }
           $lessonGet = $quiz->lesson;
           if($lessonGet){
                $courseGet = Course::with('courselog')->find($lessonGet->course_id);
                if($courseGet){
                    $courseLog = $courseGet->courselog;
                    if($courseLog){
                        $courseLog->progress = 1;
                        $courseLog->totalContent = 1;
                        $courseLog->status = 1;
                        $courseLog->save();
                        
                    }else{
                        $courseLog = new CourseLog();
                        $courseLog->user_id = auth()->user()->id;
                        $courseLog->course_id =$lessonGet->course_id;
                        $courseLog->progress = 2;
                        $courseLog->totalContent = 2;
                        $courseLog->status = 1;
                        $courseLog->save();
                    }
                }
           }
           
            $responseArr['rightAnswers'] = $rightAnserrs;
        }else{
            $responseArr['message'] = 'Error';
            $responseArr['passed'] =false;
            $responseArr['rightAnswers'] = $rightAnserrs;
        }
        $responseArr['iqc'] =$rewardIqc;
        $this->checkLottery($quiz);
        return response()->json($responseArr, Response::HTTP_OK);

    }
    public function saveLessonLog($data){
        $lesson = Lesson::with('lessonlog')->where('id',$data['lesson_id'])->first();
        if($lesson){
            
            $course = Course::with('courselog','lessons')->find($lesson->course_id);
            if($course){
                if(!$lesson->lessonlog){
                    $lessonLog = new LessonLog();
                    $lessonLog->saveOrUpdate(auth()->user()->id, $data);
                }
                $progress = 0;
                $countLength = 0;
                $lessonCount = $course->lessons->count();
                $lessonLogCount = 0;
                if($course && $lessonCount>0){
                    foreach($course->lessons as $les){
                        if($les->lessonlog){
                            $lessonLogCount++;
                            $progress += (int) isset(json_decode($les->videoLength)->ru)? json_decode($les->videoLength)->ru : 0;
                        }
                        $countLength+= (int) isset(json_decode($les->videoLength)->ru)? json_decode($les->videoLength)->ru : 0;
                    }
                }
                // $courseLog = $course->courselog;
                // if($courseLog){
                //     $courseLog->progress = $progress;
                //     $courseLog->totalContent = $countLength;
                //     $status = 1;
                //     $courseLog->status = $status;
                //     $courseLog->save();
                    
                // }else{
                //     $courseLog = new CourseLog();
                //     $courseLog->user_id = auth()->user()->id;
                //     $courseLog->course_id =$lesson->course_id;
                //     $courseLog->progress = $progress;
                //     $courseLog->totalContent = $countLength;
                //     $status = 1;
                //     $courseLog->status = $status;
                //     $courseLog->save();
                // }
                return response()->json('success', 200);
            }
            
    
        }
        
        return ErrorHelperResponse::returnError('Course or Lesson not found',Response::HTTP_NOT_FOUND);
    }
    public function sendNotificationApi($data)
    {
        $arr =[];
        $userCompany = Company::where('user_id', auth()->user()->id)->first();
        if(!$userCompany){
            return ErrorHelperResponse::returnError('You are not company owner',Response::HTTP_NOT_FOUND);
        }
        $course = Course::with('infos','courselogAll')->find($data['course_id']);
        if($course){
            // $logsExist = [];
            // if($course->courselogAll->count()>0){
            //     $logsExist[] = $course->courselogAll->pluck('user_id');
            // }
            // $users = [];
            // if($course->courseForGroup!=null){
            //     $groupCompanyList = GroupCompanyLists::has('company.companymembers')->where('company_id', $userCompany->id)->where('group_id', $course->courseForGroup)->get();
            //     if($groupCompanyList->count()>0){
                    
            //         foreach ($groupCompanyList as $key => $groupCompany) {
            //             $company = $groupCompany->company;
            //             if($company){
            //                 $users[]= $company->user_id;
            //                 $members = $company->companymembers;
            //                 if($members->count()>0){
            //                     foreach ($members as $key => $member) {
            //                         $users[] = $member->member_id;
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }
            // $notPassed = [];
            // foreach($users as $k=>$user){
              
               
            //     if(!in_array($user, $logsExist[0]->toArray())){
            //        $notPassed[] = $user;
            //     }
            // }
           
            // if(count(array_unique($notPassed))>0){
            //     foreach(array_unique($notPassed) as $k=>$us){
            //         $user = User::where('role','Employee')->find($us);
            //         if($user){
            //            $usernotPassed['user_id'] = $user->hrid;
            //            array_push($arr, $usernotPassed);
            //         }
            //     }
            // }
        }
        
            $data = [
                "to" =>"/topics/all",
                "data"=>[
                    "type"=>'custom',
                    "role"=>'all',
                    "gender"=>'all',
                    "age"=>'12,90',
                    "title_ru"=>'Пройдите курс '.json_decode($course->infos->courseTitleName)->ru,
                    "title_uz"=>'Kursni oting '.json_decode($course->infos->courseTitleName)->uz,
                    "body_ru"=>Str::words(json_decode($course->infos->courseInfo)->uz, 4, '...'),
                    "body_uz"=>Str::words(json_decode($course->infos->courseInfo)->uz, 4, '...'),
                    "users"=>$data['user_ids'],
                    "course"=>"1234",
                ]
            ];

            $response = Http::withToken('AAAAdIwC2Bk:APA91bE0CwdTZ5QI85HHRGEhFuKjIUYMhJfLTgbv1dXuF-VkSyYDNKRE0Fif7rlaoinSfaiiani322stENPQsSuSEeJ7s_8qYLLgCsiDHQTqvXFmcLmg5wlYrW4xp6O131iuIJ1t4Oz1')
            ->post('https://fcm.googleapis.com/fcm/send', $data);
            if($response->successful()){
                return response()->json(true, Response::HTTP_OK);
            }
       
        return response()->json(false, Response::HTTP_NOT_FOUND);
    }
}