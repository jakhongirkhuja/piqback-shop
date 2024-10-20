<?php

namespace App\Http\Controllers;

use App\Models\Course\Course;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Service\Sheets;
class GoogleSheetsController extends Controller
{
    
    public function index()
    {
        $courses = Course::with('infos','lessons.quizes')->get();
        $data = [];
        try {
            foreach ($courses as $key => $course) {
                $info = $course->infos->toArray();
                if($info){
                    $iqc= 0;
                    $lesson= $course->lessons;
                    if($lesson->count()>0){
                        $quiz = $lesson[0]->quizes;
                        if($quiz){
                            $iqc= $quiz->prizeIQC;
                        }
                    }
                    $data[] = [
                        "id" => $info["course_id"],
                        "Title" => $info["courseTitleName"],
                        "Body" => $info["courseInfo"],
                        "Prize"=>$iqc,
                        "Total pass" => $course->totalpass,
                        "Start Date" => $course->startDate
                    ];
                }
                
            }
            $response = Http::post('https://sheetdb.io/api/v1/xnfyzx99ojp7d',  $data);
            dd('good job');
        } catch (\Throwable $th) {
            dd('eroor'.$th);
        }

        return view('google_sheets.index', compact('data'));
    }

    public function update(Request $request)
    {
        
        return redirect('/google-sheets');
    }
}
