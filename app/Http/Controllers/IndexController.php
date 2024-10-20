<?php

namespace App\Http\Controllers;

use App\Helper\ErrorHelperResponse;
use App\Helper\StandardAttributes;
use App\Models\AppAccess;
use App\Models\Certificate\Certificate;
use App\Models\City;
use App\Models\Company;
use App\Models\Company\TeamAdress;
use App\Models\CompanyAddress;
use App\Models\CompanyAddressHistories;
use App\Models\CompanyHistories;
use App\Models\CompanyMemberHistories;
use App\Models\CompanyMembers;
use App\Models\CompanyTeamHistories;
use App\Models\CompanyTeams;
use App\Models\Course\Category;
use App\Models\Course\CategoryHistory;
use App\Models\Course\CategoryInfo;
use App\Models\Course\Course;
use App\Models\Course\CourseHistories;
use App\Models\Course\CourseInfoHistories;
use App\Models\Course\CourseInfos;
use App\Models\Course\CourseLog;
use App\Models\Email;
use App\Models\EmailHistories;
use App\Models\Groups\Group;
use App\Models\Groups\GroupCompanyListHistories;
use App\Models\Groups\GroupCompanyLists;
use App\Models\Groups\GroupHistories;
use App\Models\Groups\GroupMemberListHistories;
use App\Models\Inbox\InboxMessage;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonContent;
use App\Models\Lessons\LessonContentHistory;
use App\Models\Lessons\LessonHistory;
use App\Models\Money\Iqc;
use App\Models\Money\IqcTransaction;
use App\Models\PasswdHistories;
use App\Models\Password;
use App\Models\Phonebook;
use App\Models\PhonebookHistories;
use App\Models\Quarter;
use App\Models\Quizzes\Question;
use App\Models\Quizzes\QuestionVariant;
use App\Models\Quizzes\QuizLog;
use App\Models\Quizzes\Quizz;
use App\Models\Quizzes\QuizzHistory;
use App\Models\Quizzes\ReQuizLog;
use App\Models\Region;
use App\Models\Store\KorzinkaBarcode;
use App\Models\StoreLatest\StoreProduct;
use App\Models\Temporary\RegisterPhone;
use App\Models\Temporary\RegisterСompanyMember;
use App\Models\User;
use App\Models\UserBioHistoires;
use App\Models\UserLangHistories;
use App\Models\Wish\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class IndexController extends Controller
{
    public function profileAuth($id)
    {
        $user = User::where('hrid', $id)->first();
        if($user){
            Auth::login($user);
            return redirect()->route('index');
        }else{
            return Redirect::to('http://go.895773-cx81958.tmweb.ru/register');
        }
    }
    public function logout()
    {
        Auth::logout();
        
        request()->session()->invalidate();

        request()->session()->regenerateToken();
       
        sleep(1);
        header("Location: https://go.pharmiq.uz/redirect?redirected=academy");
            exit();
    }
    public function language($locale)
    {
        App::setLocale($locale);
        
        session()->put('locale', $locale);
        // dd(App::getLocale());
     
        return redirect()->back();
    }
    public function index()
    {
            // $address= TeamAdress::select('id','city_id','region_id','longitude','latitude','team_id')->get();
            // $count = $address->count();
        
            // $add = [];
            // $notNUll=[];
            // foreach ($address as $key => $addres) {
            //     $city = City::find($addres->city_id);
            //     $region = Region::find($addres->region_id);
            //     if(!$city || !$region){
            //         $team = CompanyTeams::select('id','teamName','company_id')->find($addres->team_id);
            //         // $s['address']= $addres;
            //         $s[] = $team?->toArray();
                
            //        if( $addres->longitude!=null) $notNUll[] = $addres;
            //     }
            //     # code...
            // }
            // foreach($s as $ss){
                
            //     if($ss!=null) $string[] = 'https://board.pharmiq.uz/companyTeams/'.$ss['company_id'].'/edit/'.$ss['id'];
                
            // }
            
            
            // return response()->json($string);
            // dd($add);
        // $courseLogs = CourseLog::with('course')->where('status',false)->where('totalContent',0)->get();
        // foreach ($courseLogs as $key => $courseLog) {
        //     $lesson = Lesson::with('quizes.quizlog')->where('course_id', $courseLog->course_id)->first();
        //     if($lesson && $lesson->quizes?->quizlog){
        //         foreach ($lesson->quizes?->quizlog as $key => $logs) {
        //             if($logs->user_id == $courseLog->user_id){
        //                 dd($logs, $courseLog);
        //             }
                    
        //         }
                
        //     }
        //     # code...
        // }
        // $logs = QuizLog::where('quiz_id',108)->where('user_id',3080)->get();
        // $requizLog = ReQuizLog::where('quiz_id',108)->where('user_id',3080)->get();
        // dd($logs,$requizLog );
        //15-null,
        // $regions = Region::find(202);
        // // $regions->name_ru = 'город Дўстлик';
        // // $regions->save();
        
        // // $regions = Region::find(459);
        // // $regions->delete();
        // // dd($regions);
        // $cities = City::select('id','name_ru')->get();
        // $regions = Region::select('id','name_ru')->where('city_id',14)->get()->toArray();
        //   $company = CompanyAddress::where('region_id',459)->first();
        // //   $company->region_id= 174;
        // //   $company->save();
        // //   dd($company);
        // $teamAddress =TeamAdress::where('region_id',459)->first();
        // // $teamAddress->region_id = 177;
        // // $teamAddress->save();
        // // dd($teamAddress);
        // for ($i=382; $i <=392 ; $i++) { 
        //     // $regions = Region::find($i);
        //     // if($regions){
        //     //   $regions->delete();
        //     // }
        //     // $company[] = CompanyAddress::where('region_id',$i)->get()->toArray();
        //     // $teamAddress[] = TeamAdress::where('region_id',$i)->get()->toArray();
        //     // // $cities[] = City::select('id','name_ru')->where('id',$i)->first();
        //     // $regions[]= Region::select('id','name_ru')->where('city_id',$i)->first();
        // }
        // dd($company,$teamAddress, $regions);
        // $company = CompanyAddress::where('city_id',16)->get();
        
        // $orderType = 4;
        // $itemName = 'ITEM NAME';
        // $itemPrice = '400';
        // $phoneBook = Phonebook::first();
        // $phoneNumber = '';
        // if($phoneBook){
        //     $phoneNumber =$phoneBook->phoneNumber;
        // }
        // $apiToken = "6926842942:AAE8V-0UUq0GBc1WcKX4QgyTtf0v4g-KpLM";

        // $data = [
        //     'chat_id' => '-1001835764381',
        //     'text' => 'Store'.PHP_EOL.''.PHP_EOL.'PhoneNumber: '.$phoneNumber.''.PHP_EOL.'Item Name: '.$itemName.''.PHP_EOL.'Item Price: '.$itemPrice.''.PHP_EOL.'datetime: '.Carbon::now()->addHours(5)->format('Y-m-d H:i:s')
        // ];
        // if($orderType==4){
        //     $iqcTransaction = IqcTransaction::select('identityText')->where('serviceName','storeProductDigital')->distinct()->get()->toArray();
        //     $empty = KorzinkaBarcode::whereNotIn('id', $iqcTransaction)->count();
        //     if($empty<=25 && $empty>=0){
        //         $data = [
        //             'chat_id' => '-1001835764381',
        //             'text' =>'Store'.PHP_EOL.''.PHP_EOL.'PhoneNumber: '.$phoneNumber.''.PHP_EOL.'Item Name: '.$itemName.''.PHP_EOL.'Item Price: '.$itemPrice.''.PHP_EOL.'datetime: '.Carbon::now()->addHours(5)->format('Y-m-d H:i:s').''.PHP_EOL.''.PHP_EOL.'Korzinka voucher'.PHP_EOL.''.PHP_EOL.'Left: '.$empty.''.PHP_EOL.'datetime: '.Carbon::now()->addHours(5)->format('Y-m-d H:i:s')
        //         ];
        
                
        //     }
        // }
        // $response2 = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
        
      //   $lists = [
    //     '998971317911','998947047788','998997162838','998913396787','998909757389','998978420017','998909319392','998946593268','998935134636','998936265692','998978884698','998994642968','998935288535','998934625544','998991974838','998970049134','998909587978','998917981310','998946450038','998907882382','998971814101','998990771125','998946965696','998909351837','998974008048','998998257712','998903249096','998946782399','998770691101','998946831445','998998813124','998900380321','998900224050','998950488472','998917794686','998903219940','998909515348','998909288719','998903507078','998909869892','998914652881','998998928090','998994943099','998977570437','998909865122','998930911578','998946380036','998971021555','998945537553','998909040920','998943664475','998945590333','998881875152','998900117550','998997968999','998998317276','998903986970','998946913537','998900099737','998905010909','998998526675','998990267646','998946385901','998917731197','998974777703','998946205418','998990222420','998903491071','998909601331','998909983522','998909748216','998977083770','998944524333','998909207194','998906805515','998946380075','998909640930','998940746044','998935121222'
    // ];
    // $phoneNumber= Phonebook::whereIn('phoneNumber',  $lists)->get()->pluck('user_id');

    //     $appacces = AppAccess::whereIn('user_id',$phoneNumber)->get();
    //     $appaccess = AppAccess::whereIn('user_id',$phoneNumber)->where('os','android')->get();
    //     dd($appacces, $appaccess);
    // $iqcTransaction = IqcTransaction::where('serviceName','storeProductDigital')->latest()->take(10)->get();
    // $arr['iqcTransactions'] = $iqcTransaction;
    // $newcode = [];
    // foreach ($iqcTransaction as $key => $iqcTransac) {
    //     $barcode = KorzinkaBarcode::find($iqcTransac->identityText);
    //     if($barcode){
    //         $newcode[] = $barcode;
    //     }
    // }
    //     $arr['karzinka'] = $newcode;
   
        return response()->json('0',404);
   
        return Excel::download(new \App\Exports\CustomExport, 'rate.xlsx');
        // if($userinfo->ageRange)
        $tables = InboxMessage::
         where('used',0)
         ->where('sentBy','SMS to phone Number')
         ->where('endDate', '>', \Carbon\Carbon::now('Asia/Tashkent')->subMinutes(10))
         ->where('startDate', '<=', \Carbon\Carbon::now('Asia/Tashkent'))
         ->get();
         dd($tables,\Carbon\Carbon::now('Asia/Tashkent'), \Carbon\Carbon::now('Asia/Tashkent')->subMinutes(50));
        dd(RegisterСompanyMember::where('addressIP', Str::replace('.','',request()->ip()))->first());
        //multiple message
            $phonebook = Phonebook::select('phoneNumber as recipient','id as message-id')
            ->where('phoneNumber', '998946121812')
            ->orwhere('phoneNumber','998994682893')->where('status', 0)->get()->toArray();
            
            $json  =  [
            'sms'=>[
                'originator'=>'3700',
                        'content'=>[
                            'text'=>"PharmIQ Academy'da yangi dars! Videoni ko'rganingizdan keyin test topshiring va +20 ballga ega bo'ling. Darsni boshlash: https://bit.ly/PharmiqUz
\n \t \nНовый урок от PharmIQ Academy! Пройдите тест после просмотра видео и получите +20 баллов. Начать обучение: https://bit.ly/PharmiqUz"
                    ]   
                        ],    
            'messages'=>$phonebook
            ];
         dd($json);
          $response =   Http::withHeaders([
                'Accept'=>'application/json',
                'Content-Type'=>'application/json',
                'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
            ])->post('http://91.204.239.44/broker-api/send',$json);
       return $response->body();
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $category = Category::with('courses')->take(15)->get();
        $courses = Course::with('getinfo','category','lessons')->take(10)->get();
        // dd($courses);
        // dd( App::getLocale() );
        return view('pages.main',[
            'categories'=>$category,
            'courses'=>$courses,
            'active'=>'main',
            'language'=>App::isLocale('ru')
        ]);
        // dd(Phonebook::all(), User::all());
        
        // dd::all());
        // 764 704 133 654 998 668
        // 73 541 199 664 277
        // 1 585 712 892 936 151 808
        // 73 567 504 870 998
        
       
        // dd($city);
    //     $json  =  [
    //         'messages'=>[
    //             [
    //                 'recipient'=>'998946121812',
    //                 'message-id'=>'abc000000004',
    //                 'sms'=>[
    //                     'originator'=>'PharmIQ',
    //                     'content'=>[
    //                         'text'=>"Test message"
    //                     ]
    //                 ]
    //             ]
    //         ]
    //         ];
    //       $response =   Http::withHeaders([
    //             'Accept'=>'application/json',
    //             'Content-Type'=>'application/json',
    //             'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
    //         ])->post('http://91.204.239.44/broker-api/send',$json);
    //    return $response->body();
        return view('welcome');
    }
    public function uploadcity()
    {
        $xmlString = file_get_contents(public_path('cities.xml'));
        $xmlObject = simplexml_load_string($xmlString);
                   
        $json = json_encode($xmlObject,true);
        $phpArray = json_decode($json, true); 
        // dd(City::where('name_ru','ilike','Ташкентская область')->first());
        foreach ($phpArray['table_regions']['regions'] as $key => $cities) {
        //    dd($cities['@attributes']['name_uz']);
        // dd($cities['@attributes']);
           $city  = new City();
           $city->country_id =  1;
           $city->name_uz =  $cities['@attributes']['name_uz'];
           $city->name_ru =  $cities['@attributes']['name_ru'];
           $city->save();

         

        }
        return 'ok';
    }
    public function uploadregion()
    {
        $xmlString = file_get_contents(public_path('regions.xml'));
        $xmlObject = simplexml_load_string($xmlString);
                   
        $json = json_encode($xmlObject,true);
        $phpArray = json_decode($json, true); 
        // dd(Region::select('name_ru')->where('name_ru','Зангиатинский район')->get());
        // dd($phpArray['table_districts']);
        foreach ($phpArray['table_districts']['districts'] as $key => $cities) {
        //    dd($cities['@attributes']['name_uz']);
        // dd($cities['@attributes']);
           $city  = new Region();
           $city->city_id = $cities['@attributes']['region_id'];

           $city->name_uz =  $cities['@attributes']['name_uz'];
           $city->name_ru =  $cities['@attributes']['name_ru'];
           $city->save();
    
        }
        return 'ok';
    }
    public function uploadquarter()
    {
        $xmlString = file_get_contents(public_path('quarter.xml'));
        $xmlObject = simplexml_load_string($xmlString);
                   
        $json = json_encode($xmlObject,true);
        $phpArray = json_decode($json, true); 
        dd($phpArray['table_quarters']);
        foreach ($phpArray['table_quarters']['quarters'] as $key => $cities) {
        //    dd($cities['@attributes']['name_uz']);
        // dd($cities['@attributes']);
           $city  = new Quarter();
           $city->regions_id = $cities['@attributes']['district_id'];
           $city->name =  $cities['@attributes']['name'];
        //    $city->save();
    
        }
        return 'ok';
    }

    public function category($id)
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        return view('pages.category',[
            'category'=>Category::findorfail($id),
            'language'=>App::isLocale('ru')
        ]);
    }
    public function course($id)
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $course = Course::with('lessons','getinfo','category')->where('id',$id)->first();
        if(!$course){
            return redirect()->route('index');
        }
        return view('pages.course',[
            'course'=>$course,
            'wishlish'=>Wishlist::where('user_id', auth()->user()? auth()->user()->id : 1 )->where('course_id', $course->id)->first(),
            'iqc'=>Iqc::where('user_id', auth()->user()? auth()->user()->id : 1)->first(),
            'language'=>App::isLocale('ru')
        ]);
    }
    public function lesson($id)
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $lesson = Lesson::with('contents','quizes','course.getinfo')->where('id',$id)->first();
        if(!$lesson){
            return redirect()->route('index');
        }
        $alllessons = Lesson::with('contents','quizes','course.getinfo')->where('course_id', $lesson->course_id)->get();
       
        return view('pages.lesson',[
            'lesson'=>$lesson,
            'alllessons'=>$alllessons,
            'language'=>App::isLocale('ru')
        ]);
    }
    public function lessonQuiz($id)
    {

        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $lesson = Lesson::with(['quizes.questions.variants'=>function($query) {
           
        }])->where('id',$id)->first();
        
        if(!$lesson){
            return redirect()->route('index');
        }
        if($lesson->quizes){
            $reQuizLogs = ReQuizLog::where('user_id', auth()->user()->id)->whereDate('created_at', \Carbon\Carbon::today())->get();
            if(count($reQuizLogs)>0){
                $countExist = false;
                foreach ($reQuizLogs as $reQuizLog) {
                   if($reQuizLog->quiz_id == $lesson->quizes->id){
                       $countExist = true;
                   }
                }
                if(!$countExist){
                    return redirect()->route('lesson.web',['id'=>$lesson->id, 'overflow'=>true]);
                }
            }
            
        }else{
            return redirect()->route('lesson.web',['id'=>$lesson->id]);
        }
        
        return view('pages.quizz',[
            'lesson'=>$lesson,
            'language'=>App::isLocale('ru')
        ]);
    }
    public function lessonQuizPost(Request $request)
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
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $data = $request->all();
        $quiz = Quizz::find($data['quiz_id']);
        $rightAnserrs = 0;
       
        if($quiz){
            foreach (json_decode($data['question'], true) as $key => $question) {
                if(count($question['variants'])>0){
                   
                    foreach ($question['variants'] as $key => $variant) {
                        $variantcheck = QuestionVariant::find($variant['id']);
                        if($variantcheck){
                            // dd($variantcheck, $variantcheck->rightAnswer == $variant['choose'],$question['variants'], $variant);
                            if($variantcheck->rightAnswer == true && $variant['choose'] == true){
                                
                                $rightAnserrs++;
                                break;
                            }
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
                $lesson = Lesson::find($data['lesson_id']);
                $lessonNext = false;
                if($lesson){
                    $lessonNext = Lesson::where('course_id', $lesson->course_id)->where('order','>', $lesson->order)->first();
                }
                $iqc = Iqc::where('user_id',  auth()->user()->id)->first();



                if($iqc){
                        if($quizLogs->status){
                                 $newPrice = 0;
                        }else{
                            if($quizLogs->quizAttempt!=1 ){
                                $newPrice = $quiz->prizeIQC - (5 * ($quizLogs->quizAttempt-1));
                            }else{
                                $newPrice = $quiz->prizeIQC;
                            }
                            $quizLogs->status = 1;
                            $quizLogs->prizeOut = $newPrice;
                            $quizLogs->save();
                            $reQuizLog = new ReQuizLog();
                            $reQuizLog->saveModel($quizLogs, $data);
                        }
                        $changedPrice = $newPrice<=0 ? 0 : $newPrice; 
                        $iqc->updateModel($data, $changedPrice,1,'quiz');
                }else{
                    if($quizLogs->status){
                        $newPrice = 0;
                    }else{
                        if(($quizLogs->quizAttempt)!=1){
                            $newPrice = $quiz->prizeIQC - (5 * ($quizLogs->quizAttempt-1));
                        }else{
                            $newPrice = $quiz->prizeIQC;
                        }
                        $quizLogs->status = 1;
                        $quizLogs->prizeOut = $newPrice;
                        $quizLogs->save();
                        $reQuizLog = new ReQuizLog();
                        $reQuizLog->saveModel($quizLogs, $data);
                    }
                    $changedPrice = $newPrice<=0 ? 0 : $newPrice;
                    $iqc = new Iqc();
                    $iqc->saveModel($data, auth()->user()->id,$changedPrice,1,'quiz', $quiz->id);
                }
                $responseArr['view'] =  view('pages.good')->with(['quiz'=>$quiz, 'lessonNext'=>$lessonNext, 'changedPrice'=>$changedPrice])->render();
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
                $responseArr['view'] =  view('pages.bad')->with('lesson',$data['lesson_id'])->render();
           }

        }else{
            $responseArr['message'] = 'Error';
            $responseArr['passed'] =false;
            $responseArr['view'] =  view('pages.bad')->render();
        }
        return response()->json($responseArr, Response::HTTP_OK);
        
    }
    public function mycourses()
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $courses = Course::with('getinfo','category','lessons')->take(2)->get();
       return view('pages.mycourses',[
        'courses'=>$courses,
        'language'=>App::isLocale('ru'),
        'active'=>'mycourses',
       ]);
    }
    public function savedcourse()
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
       return view('pages.savedcourses',[
        'wishlists'=>Wishlist::with('courselist')->where('user_id',auth()->user()? auth()->user()->id : 1)->get(),
        'language'=>App::isLocale('ru'),
        'active'=>'saved',
       ]);
    }
    public function savedcoursePost(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id'=>'required|numeric',
        ]);
        if ($validator->fails()) {
            $responseArr['error']=true;
            $responseArr['message'] = $validator->errors();
            return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::find(auth()->user()? auth()->user()->id : 1)->first();
        if(!$user){
            return ErrorHelperResponse::returnError('Auth user not exist',Response::HTTP_NOT_FOUND);
        }
        $wishList = Wishlist::where('user_id', $user->id)->where('course_id', $data['course_id'])->first();
        if($wishList){
            return ErrorHelperResponse::returnError('Course is exist in wishlist',Response::HTTP_FOUND);
        }
        $course = Course::find($data['course_id']);
       
        if($course){
            try {
                $res =DB::transaction(function () use ($user, $data){
                    $wishlist = new Wishlist();
                    $wishlist->saveModel($user,$data);
                    $responseArr['wishlist'] =$wishlist;
                    $responseArr['message'] = 'Success';
                    return response()->json($responseArr, Response::HTTP_CREATED);
                });
                return $res;
            } catch (\Throwable $th) {
                return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ErrorHelperResponse::returnError('Course with given id not found',Response::HTTP_NOT_FOUND);
        }
    }
    public function savedcourseRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wish_id'=>'required',
            'status'=>'required'
        ]);
        if ($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $wishList = Wishlist::where('id',$data['wish_id'])->first();
        if(!$wishList){
            return ErrorHelperResponse::returnError('WishList id is not exist',Response::HTTP_FOUND);
        }
        try {
            $res =DB::transaction(function () use ($wishList, $data){
                $wishList->updateModel($data, $data['status']);
                $responseArr['message'] = $data['status'];
                return response()->json($responseArr, Response::HTTP_OK);
            });
            return $res;
        } catch (\Throwable $th) {
            return ErrorHelperResponse::returnError('Something wrong please connect with admin'.$th,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function profile()
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        $company = Company::with('companymembers','companyadress')->where('user_id', auth()->user()? auth()->user()->id : 1)->first();
       
        $city = City::all();
        
        $region =Region::where('city_id', 14)->get();
        
        $iqc = Iqc::where('user_id', auth()->user()? auth()->user()->id : 1)->first();
        $email = Email::where('user_id', auth()->user()? auth()->user()->id : 1)->first();
        return view('pages.user',[
            'iqc'=>$iqc,
            'user'=>User::find(auth()->user()? auth()->user()->id : 1),
            'language'=>App::isLocale('ru'),
            'company'=>$company,
            'cities'=>$city,
            'regions'=>$region,
            'email'=>$email
           ]);
    }
    public function profileInfo(Request $request)
    {
       $request->validate([
        'firstName'=>'required',
        'lastName'=>'required',
        'birthDate'=>'required',
        'gender2'=>'required',
       ]);
       $user = User::find(auth()->user()? auth()->user()->id : 1);
       if($user){
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->birthDate = $request->birthDate;
            $user->gender = $request->gender2;
            
            $user->save();
       }
       return redirect()->back();
    }
    public function profilephonebook(Request $request)
    {
       $request->validate([
        'number'=>'required',
       
       ]);
       $user = Phonebook::where('user_id',auth()->user()? auth()->user()->id : 1)->first();
       if($user){
            $data = $request->all();
            $user->updateModel($data);
            
       }
       return redirect()->back();
    }
    public function profilePassword(Request $request)
    {
       $request->validate([
        'password'=>'required|confirmed|min:5',
        'password_old'=>'required',
       ]);
       $password = Password::where('user_id',auth()->user()? auth()->user()->id : 1)->first();
       
       if($password){
           
            $data = $request->all();
            if(Hash::check($data['password_old'], $password->passwd)){
                Session::flash('message', 'Updated'); 
                $password->updateModel($data);
            }else{
                Session::flash('message', 'Old password not same'); 
            }
            
        }else{
            Session::flash('message', 'Something went wrong');
        }
        
       return redirect()->back();
    }
    public function profileCompany(Request $request)
    {
        $request->validate([
            'companyName'=>'required',
            'city_id'=>'required',
            'region_id'=>'required',
            'city_id'=>'required',
            'city_id'=>'required',
        ]);
        $data = $request->all();
        
            $company = Company::with('companyadress')->where('user_id', auth()->user()? auth()->user()->id : 1)->first();
            if($company){
                try {
                   
                    DB::transaction(function() use ($company, $data){
                        if($company->companyName != $data['companyName']){
                            $found = Company::where('user_id','!=' ,auth()->user()? auth()->user()->id : 1)->where('companyName', $data['companyName'])->first();
                            if(!$found){
                                $company->updateCompanyNameModel($data);
                            }
                        }
                        $company->companyadress->updateModelAdress($data);
                    });
                    Session::flash('message', 'Updated'); 
                    return redirect()->back();
                } catch (\Throwable $th) {
                    Session::flash('message', 'Something went wrong'.$th); 
                    return redirect()->back();
                }
            }else{
                Session::flash('message', 'Company not found'); 
                return redirect()->back();
            }
       
        
    }
    public function profileEmail(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
        ]);
        $data = $request->all();
        $user = User::find(auth()->user()? auth()->user()->id : 1)->first();
        
        if(!$user){
            Session::flash('message', 'User not found'); 
            return redirect()->back();
        }
        $email =$user->email;
        
        if($email){
            $email->email = $data['email'];
            if($email->save()){
                $emailHistories = new EmailHistories();
                $emailHistories->user_id = $email->user_id;
                $emailHistories->email = $data['email'];
                if($emailHistories->save()){
                    
                    StandardAttributes::setSA('email_histories',$emailHistories->id,1,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                }
               
                Session::flash('message', 'Updated'); 
                return redirect()->back();
            }else{
                Session::flash('message', 'Email not saved'); 
                return redirect()->back();
            }
        }else{
            $email = new Email();
             $email->user_id = $user->id;
            $email->email = $data['email'];
            if($email->save()){
                
                $emailHistories = new EmailHistories();
                $emailHistories->user_id = $email->user_id;
                $emailHistories->email = $data['email'];
                if($emailHistories->save()){
                    
                    StandardAttributes::setSA('email_histories',$emailHistories->id,0,request()->ip(), $data['platform'],$data['device'],$data['browser'], $data['timeZone']);
                }
                Session::flash('message', 'Updated'); 
                return redirect()->back();
            }else{
                Session::flash('message', 'Email not saved'); 
                return redirect()->back();
            }
        }
        
    }
    public function task()
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        return view('pages.soon',[
       
            'language'=>App::isLocale('ru'),
            'active'=>'task',
           ]);
    }
    public function webinars()
    {
        if(session()->get('locale')){
            App::setLocale(session()->get('locale'));
        }
        return view('pages.soon',[
       
            'language'=>App::isLocale('ru'),
            'active'=>'web',
           ]);
    }
    public function profileCompanyMembersApprove()
    {
       
        $company = Company::with('companymembers')->where('user_id', auth()->user()? auth()->user()->id : 1)->first();
        $use =$company->companymembers->where('member_id', request()->user_id)->first();
        if($use){
            $use->memberStatus = request()->memberStatus;
            $use->save();
        }
        
        return response()->json([
            'user_id'=>request()->user_id,
            'memberStatus'=>request()->memberStatus,
        ]);
    }
    public function profileGetRegion()
    {
        $region = Region::where('city_id', request()->city_id)->get();

        return response()->json([
            'regions'=>$region,
            
        ]);
    }
    public function logs()
    {
       $log = request()->pass;
       if($log==12345678){
         $filePath = storage_path('logs/laravel.log');
         
         $data = [];
         if(File::exists($filePath)){
            $file = File::get($filePath);
            $array = explode("\n",$file);

            if(request()->filter != 'all'){
                foreach($array as $in){
                    if(str_contains($in, 'local.INFO')){
                        $data[]= Str::replace('\\','', $in);
                    }
                }
            }else{
                $data = $array ;
            }
            if(request()->download){
                return response()->download($filePath);
            }
            if(request()->delete){
                File::delete($filePath);
            }
            return response()->json(array_reverse($data));
         }
       }
    }
    public function readLog(){
        if(request()->password=='998946121812'){

            $logFilePath = storage_path('logs/laravel.log');
            // Read last 1000 lines of the log file
            $logContent = shell_exec('tail -n 1000 ' . $logFilePath);
            return response($logContent)->header('Content-Type', 'text/plain');
            
        }
    }
}
