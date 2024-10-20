<?php

namespace App\Console\Commands;

use App\Models\Course\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class ExportExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to export excel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $data = [];
        $smssendss['exportExcel'] = 'Started Command: '.Carbon::now();
        Log::info(json_encode($smssendss));
        try {
            $response = Http::delete('https://sheetdb.io/api/v1/xnfyzx99ojp7d/all');
            if($response->ok()){

                $courses = Course::with('infos','lessons.quizes')->get();
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
                $smssendss['exportExcel'] = 'Create items: '.Carbon::now();
                Log::info(json_encode($smssendss));
            }else{
                $smssendss['exportExcel'] = 'Error response : '.$response->body();
                Log::info(json_encode($smssendss));
            }

        } catch (\Throwable $th) {
            $smssendss['exportExcel'] = 'Error: '.$th;
        }
        $smssendss['exportExcel'] = 'Started'.Carbon::now();
        Log::info(json_encode($smssendss));
    }
}
