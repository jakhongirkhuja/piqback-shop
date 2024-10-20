<?php

namespace App\Http\Controllers\Api\v1;

use App\Helper\ErrorHelperResponse;
use App\Http\Controllers\Controller;
use App\Models\Course\Category;
use App\Models\Course\CategoryInfo;
use App\Models\Course\Course;
use App\Models\Course\CourseLog;
use App\Models\Phonebook;
use App\Models\StoreLatest\StoreProduct;
use App\Models\StoreLatest\StoreProductCode;
use App\Models\Temporary\RegisterPhone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function index()
    {
        return auth()->user();
    }
    public function getCourseInfo($course_id)
    {
        $course = Course::with('infos','lessons.quizes.questions')->where('id', $course_id)->first();
        if($course){
            $lessons = $course->lessons;
            $countLesson = $lessons->count();
            $countLength = 0;
            $countQuestions= 0;
            
            $students = CourseLog::where('course_id', $course_id)->count()*7;
            if($countLesson>0){
                
                foreach ($lessons as $key => $lesson) {
                    $videoLengthJson  = json_decode($lesson->videoLength,true);
                    if(isset($videoLengthJson['ru'])){
                        $countLength+=(int)$videoLengthJson['ru'];
                    }
                    if($lesson->quizes->count()>0){
                        $countQuestions += $lesson->quizes->questions->count();
                    }
                }
                
            }
        }
        
        
        
        $array = [
            'videoLength'=>$countLength,
            'lessons'=>$countLesson,
            'questions'=>$countQuestions,
            'students'=>$students,
        ];
        return response()->json($array,200);
    }
    public function categoryList()
    {
       
        $category = Category::paginate(10);
        return response()->json($category, Response::HTTP_OK);
    }
    public function courseList($category_id=null)
    {
        if($category_id){
            $courses = Course::with('infos','lessons')->where('category_id', $category_id)->paginate(10);
        }else{
            $courses = Course::with('infos','lessons')->paginate(10);
        }
        return response()->json($courses, Response::HTTP_OK);
    }
    public function courseEach($course_id)
    {
        if($course_id){
            $courses = Course::with(['lessons','lessons.quizes','getinfo' => function ($query) {
                $query->select('course_id','language as courseTitleName','courseBanner','courseInfoPage as body');
            }])
            ->select('*')->where('id', $course_id)->get();
        }else{
            $courses = Course::with(['lessons','lessons.quizes','getinfo' => function ($query) {
                $query->select('course_id','language as courseTitleName','courseBanner','courseInfoPage as body');
            }])
            ->select('*')->get();
        }
        
        return response()->json($courses, Response::HTTP_OK);
    }
    public function scout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scout_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['scout_id'])->where('role', 'Scout')->first();
        if($user){
            $responseArr['scout_id']=$data['scout_id'];
            $responseArr['response_ip']=request()->ip();
            $responseArr['message'] = 'Success';
            return response()->json($responseArr, Response::HTTP_OK);
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'Scout with given id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
    }
    public function reluser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rel_id'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $user = User::where('hrid',$data['rel_id'])->first();
        if($user){
            $responseArr['rel_id']=$data['rel_id'];
            $responseArr['response_ip']=request()->ip();
            $responseArr['message'] = 'Success';
            return response()->json($responseArr, Response::HTTP_OK);
        }else{
            $responseArr['error']=true;
            $responseArr['message'] = 'User with given id not found';
            return response()->json($responseArr, Response::HTTP_NOT_FOUND);
        }
    }
    public function formPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contactNumber'=>'required',
            'pageID'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $contactNumber = $data['contactNumber'];
        $pageID = $data['pageID'];
        $json  =  [
            'messages'=>[
                [
                    'recipient'=>'998909539988',
                    'message-id'=>'abc000000001',
                    'sms'=>[
                        'originator'=>'3700',
                        'content'=>[
                            'text'=>"Request to call back from page  ".$pageID." number: +998".$contactNumber
                        ]
                    ]
                ]
            ]
            ];
            $response =   Http::withHeaders([
                    'Accept'=>'application/json',
                    'Content-Type'=>'application/json',
                    'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
                ])->post('http://91.204.239.44/broker-api/send',$json);
            if($response->ok()){
                $responseArr['form']=$data;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            }else{
                $responseArr['error']=true;
                $responseArr['message'] = 'SMS  not sent';
                return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        

    }
    public function formDetailed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contactNumber'=>'required',
            'pageID'=>'required',
        ]);
        if($validator->fails()) {
            return ErrorHelperResponse::returnError($validator->errors(),Response::HTTP_BAD_REQUEST);
        }
        $data = $request->all();
        $contactNumber = $data['contactNumber'];
        $requesterName = $data['requesterName'];
        $requestSubject = $data['requestSubject'];
        $message = $data['message'];
        $pageID = $data['pageID'];
        $responseArr['form']=$data;
        $responseArr['message'] = 'Success';
        $json  =  [
            'messages'=>[
                [
                    'recipient'=>'998909539988',
                    'message-id'=>'abc000000001',
                    'sms'=>[
                        'originator'=>'3700',
                        'content'=>[
                            'text'=>"Request from pagee ".$pageID." number: +998".$contactNumber.", Message text exist on board"
                        ]
                    ]
                ]
            ]
            ];
            $response =   Http::withHeaders([
                    'Accept'=>'application/json',
                    'Content-Type'=>'application/json',
                    'Authorization'=>'Basic Z29yZ2VvdXM6Z214OEpSN0MzOQ==',
                ])->post('http://91.204.239.44/broker-api/send',$json);
            if($response->ok()){
                $responseArr['form']=$data;
                $responseArr['message'] = 'Success';
                return response()->json($responseArr, Response::HTTP_OK);
            }else{
                $responseArr['error']=true;
                $responseArr['message'] = 'SMS  not sent';
                return response()->json($responseArr, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        return response()->json($responseArr, Response::HTTP_OK);

    }
    public function phoneNumberStatus(Request $request)
    {
       
       $phoneNumber = $request->phone_number;
       $status = $request->status!=''? $request->status : 'No-status';
       $status_date = $request->status_date!=''? $request->status_date : 'No-time';
       $from = request()->mobile? 'MobileApp' : 'DesktopApp';
       $phoneNums = Phonebook::where('phoneNumber', $phoneNumber)->first();
        
        if($phoneNums){
            $phoneNum = $phoneNums->random;
        }else{
            $phoneNum = '';
        }
        if(request()->register){
            $phoneNumberReg = RegisterPhone::where('phoneNumber',$phoneNumber)->latest()->first();
            if($phoneNumberReg){
                $phoneNum = $phoneNumberReg->confirm_code;
            }else{
                $phoneNum = '';
            }
            
        }
       $apiToken = "5742929322:AAEIYasWSlEjDK38bB_MaKpTNFRixyAGh5g";

        $data = [
            'chat_id' => '-1001672584663',
            'text' => 'SMS-Eskiz'.PHP_EOL.''.PHP_EOL.'PhoneNumber: '.$phoneNumber.''.PHP_EOL.'Status: '.$status.''.PHP_EOL.'From: '.$from.''.PHP_EOL.'Code: '.$phoneNum.''.PHP_EOL.'Time: '.$status_date
        ];

      $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
       return;
    }
    public function storeProducts()
    {
        if(request()->id){
            $storeProduct = StoreProduct::with('store.category')->find(request()->id);
        }
        else{
            $storeProduct =  StoreProduct::with('store')->where('productAmount', '!=',0)->orderby('productCost','desc')->get();
        }
        return response()->json($storeProduct, Response::HTTP_OK);
    }
    
}
