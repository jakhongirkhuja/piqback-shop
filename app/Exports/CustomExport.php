<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\CompanyMembers;
use App\Models\CompanyTeams;
use App\Models\Course\Course;
use App\Models\Course\CourseLog;
use App\Models\Money\Iqc;
use App\Models\Quizzes\QuizLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class CustomExport implements FromView
{
    public function view(): View
    {
        $members = CompanyMembers::select('member_id')->where('company_id', 1275)->get()->toArray();
        $iqc = Iqc::whereIn('user_id', $members)->orderby('amountofIQC','desc')->get();
        return view('exports.custom',[
            'iqcs'=>$iqc,
            ]);
    }

}
