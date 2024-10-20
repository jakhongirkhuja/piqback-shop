<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Response;

class QuizDashService{

    public function showResponse(){


        $quizez = [];
        $courses = \App\Models\Course\Course::with('getinfo','lessons.quizes.questions')->get();
        $courseAttempt = [];
        foreach($courses as $course){
            $quizes = $course->lessons[0]->quizes;
            $quizez[]=$quizes->id;
            
            $tag['course'] = json_decode($course->getinfo->courseTitleName)->ru;
            $tag['quiz_id'] = $quizes->id;
            $tag['quizRightAnswer'] = $quizes->numberRightAnswersToPass;
            $tag['quizQuestionsCount'] = $quizes->questions->count();
            $tag['courseQuizAttampq1'] = 0;
            $tag['courseQuizparticipantsq2'] = 0;
            $tag['courseQuizRestartq3'] = 0;
            $courseAttempt[]=$tag;
            
        }
        // dd($courseAttempt);
        
        // $courses = \App\Models\Course\Course::with('lessons.quizes.questions')->where('courseTypeByAccess',0)->get();
        
        // q-1
            $userExistAllQuiz = 0;
            
        $quizesGet =  \App\Models\Quizzes\QuizLog::whereIn('quiz_id',$quizez)->get();
        $quizeslogs = $quizesGet->groupby('user_id');
            
        // foreach($quizeslogs as $k=>$quizlog){
            
        //     if(count($quizlog) == count($quizez)){
        //         // $userExistAllQuiz++;
        //     }
            
        // }
        $userExistAllQuiz = $quizesGet->sum('quizAttempt');
        //q-2
        $quizParticipants = 0;
        
        //q-3
        $quizesRestart = \App\Models\Quizzes\QuizLog::whereIn('quiz_id',$quizez)->where('quizAttempt','>',1)->sum('quizAttempt');
        
        //q-4
        $totalRIghtAnswer = 0;
        $totalRIghtAnswerCount = 0;
        $totalFalseAnswer = 0;
        $totalFalseAnswerCount = 0;
        // Dash variables 
        $userCount =  User::count() ;
        $atp1 = 0;
        $user1 = [];
        $atp2 = 0;
        $user2 = [];
        $atp3 = 0;
        $user3 = [];
        $atp4 = 0;
        $user4 = [];
        $atp5 = 0;
        $user5 = [];
        $atp5plus = 0;
        $user5plus = [];
        $atpZero = 0;
        //
        foreach($quizesGet as $quizeGet){
            
            foreach($courseAttempt as $couat){
                if($couat['quiz_id']==$quizeGet->quiz_id){
                        
                    if($quizeGet->numberOfRightAnswers>=$couat['quizRightAnswer']){
                        $totalRIghtAnswer += $couat['quizQuestionsCount']!=0? $quizeGet->numberOfRightAnswers *100/$couat['quizQuestionsCount'] : 0;
                        $totalRIghtAnswerCount +=1;
                    }else{
                        $totalFalseAnswer+= $couat['quizQuestionsCount']!=0? $quizeGet->numberOfRightAnswers *100/$couat['quizQuestionsCount'] : 0;
                        $totalFalseAnswerCount +=1;
                    }
                }
                
            }
            if($quizeGet->quizAttempt==1){
                $quizParticipants +=1;
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id &&  $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer']){
                        if(!in_array($quizeGet->user_id,$user1)){
                            $atp1 +=1;
                            $user1[]= $quizeGet->user_id;
                        }
                            
                    }
                    
                }
            }
            if($quizeGet->quizAttempt==2){
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id &&  $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer'] ){
                            
                            if(!in_array($quizeGet->user_id,$user2)){
                                $atp2 +=1;
                                $user2[]= $quizeGet->user_id;
                            }
                    }
                    
                }
            }
            if($quizeGet->quizAttempt==3){
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id && $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer']){
                            if(!in_array($quizeGet->user_id,$user3)){
                            $atp3 +=1;
                            $user3[]= $quizeGet->user_id;
                        }
                    }
                    
                }
            }
            if($quizeGet->quizAttempt==4){
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id && $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer']){
                            if(!in_array($quizeGet->user_id,$user4)){
                                $atp4 +=1;
                                $user4[]= $quizeGet->user_id;
                            }
                    }
                    
                }
            }
            if($quizeGet->quizAttempt==5){
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id && $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer']){
                        if(!in_array($quizeGet->user_id,$user5)){
                            $atp5 +=1;
                            $user5[]= $quizeGet->user_id;
                        }
                    }
                    
                }
            }
            if($quizeGet->quizAttempt>5){
                foreach($courseAttempt as $couat){
                    if($couat['quiz_id']==$quizeGet->quiz_id && $quizeGet->numberOfRightAnswers==$couat['quizRightAnswer'] ){
                            
                            if(!in_array($quizeGet->user_id,$user5plus)){
                                $atp5plus +=1;
                                $user5plus[]= $quizeGet->user_id;
                            }
                    }
                    
                }
            }
        }
        
        $avarageRightAnswer= $totalRIghtAnswerCount!=0? (float)number_format($totalRIghtAnswer/$totalRIghtAnswerCount, 1,'.') : 0;
        $avarageFalseAnswer= $totalFalseAnswerCount!=0? (float)number_format($totalFalseAnswer/$totalFalseAnswerCount, 1,'.') : 0;
        
        //CoursePart questions each
        foreach($courseAttempt as $k=>$courseAt){
            $countAttamp = 0;
            $countQuizRestart = 0;
            $sumrightAnswers = 0;
            $quizls =  \App\Models\Quizzes\QuizLog::where('quiz_id', $courseAt['quiz_id'])->get();
            $quizl_count = $quizls->count();
            $courseParticipant = 0;
            $courseAttemptCount = 0;
            $totalRIghtAnswerQ = 0;
            $totalRIghtAnswerCountQ = 0;
            $totalFalseAnswerQ = 0;
            $totalFalseAnswerCountQ  = 0;
            if($quizl_count>0){
                
                foreach($quizls as $quizl){
                    $courseAttemptCount  +=  $quizl->quizAttempt;
                    if($quizl->quizAttempt==1){
                        $courseParticipant += $quizl->quizAttempt;
                    }
                    if($quizl->numberOfRightAnswers==$courseAt['quizRightAnswer']){
                        // $countAttamp +=1;
                        // dd($countAttamp);
                    }
                    if($quizl->quizAttempt>1){
                        $countQuizRestart += $quizl->quizAttempt;
                    }
                    foreach($courseAttempt as $couat){
                        if($couat['quiz_id']==$quizl->quiz_id ){
                               if($quizl->numberOfRightAnswers>=$couat['quizRightAnswer'] ){
                                    $totalRIghtAnswerQ += $couat['quizQuestionsCount']!=0? $quizl->numberOfRightAnswers *100/$couat['quizQuestionsCount'] : 0;
                                    $totalRIghtAnswerCountQ +=1;
                               }else{
                                    $totalFalseAnswerQ += $couat['quizQuestionsCount']!=0? $quizl->numberOfRightAnswers *100/$couat['quizQuestionsCount'] : 0;
                                    $totalFalseAnswerCountQ +=1;
                               }
                        }
                        
                    }
                    // $sumrightAnswers +=$quizl->numberOfRightAnswers;
                }
            }
            $courseAttempt[$k]['courseQuizAttampq1'] = $courseAttemptCount;
            $courseAttempt[$k]['courseQuizparticipantsq2'] = $courseParticipant;
            $courseAttempt[$k]['courseQuizRestartq3'] = $countQuizRestart;
            $courseAttempt[$k]['courseQuizAverageRightq4'] = $totalRIghtAnswerCountQ!=0? (float)number_format($totalRIghtAnswerQ/$totalRIghtAnswerCountQ, 1,'.') : 0;
            $courseAttempt[$k]['courseQuizAverageFalseq4'] = $totalFalseAnswerCountQ!=0? (float)number_format($totalFalseAnswerQ/$totalFalseAnswerCountQ, 1,'.') : 0;
        }
        
        // dd($atp1, $atp2, $atp3, $atp4, $atp5, $atp5plus, $atpZero);
        // Quiz Dash Part
        // $atp1 = (float)number_format($atp1*100/$userCount, 1,'.') ;
        // $atp2 = (float)number_format($atp2*100/$userCount, 1,'.') ;
        // $atp3 = (float)number_format($atp3*100/$userCount, 1,'.') ;
        // $atp4 =(float)number_format($atp4*100/$userCount, 1,'.') ;
        // $atp5 =(float)number_format($atp5*100/$userCount, 1,'.') ;
        // $atp5plus =  (float)number_format($atp5plus*100/$userCount, 1,'.') ;
        // $atpZero = (float)number_format(count($quizeslogs)*100/$userCount, 1,'.') ;
        
        // $atp1 = (float)number_format($atp1*100/$userCount, 1,'.') ;
        // $atp2 = (float)number_format($atp2*100/$userCount, 1,'.') ;
        // $atp3 = (float)number_format($atp3*100/$userCount, 1,'.') ;
        // $atp4 =(float)number_format($atp4*100/$userCount, 1,'.') ;
        // $atp5 =(float)number_format($atp5*100/$userCount, 1,'.') ;
        // $atp5plus =  (float)number_format($atp5plus*100/$userCount, 1,'.') ;
        // $atpZero = (float)number_format(count($quizeslogs)*100/$userCount, 1,'.') ;


        
        $responseArr['attemptQuizzes'] = $userExistAllQuiz;
        $responseArr['participantQuizzes'] = $quizParticipants;
        $responseArr['restartQuizzes'] = $quizesRestart;
        $responseArr['averateQuizzes'] = $avarageRightAnswer;
        $responseArr['averateFalseQuizzes'] = $avarageFalseAnswer;
        
        $responseArr['courseQuiz'] = array_reverse($courseAttempt);
        $responseArr['passingone'] = $atp1;
        $responseArr['passingtwo'] = $atp2;
        $responseArr['passingthree'] = $atp3;
        $responseArr['passingfour'] = $atp4;
        $responseArr['passingfive'] = $atp5;
        $responseArr['passingfiveplus'] = $atp5plus;
        $responseArr['passingzero'] = count($quizeslogs);
        $responseArr['userCount'] = $userCount;
        return response()->json($responseArr, Response::HTTP_OK);
    }
}