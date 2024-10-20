<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>PhoneNumber</th>
        <th>Team</th>
        <th>IQC</th>
        <th>Quiz</th>
        <th>Course</th>
    </tr>
    </thead>
    <tbody>

    @foreach($iqcs as $iqc)
        @php
        $user = \App\Models\User::with('phonebook')->find($iqc->user_id);
        $exist = true;
        $companyTeam = \App\Models\CompanyTeamLists::with('companyTeam')->where('teamMember', $iqc->user_id)->first();
        $average = \App\Models\Quizzes\QuizLog::where('user_id', $iqc->user_id)->get()->pluck('quizAttempt')->avg();
        $course = \App\Models\Course\Course::select('id')->where('courseForGroup',1)->get()->toArray();
        $courseLogs = App\Models\Course\CourseLog::whereIn('course_id',$course)->where('user_id',$iqc->user_id)->where('status', true)->get();
        
        @endphp
        @if($exist)
        <tr>
            <td>{{$user->firstName.' '.$user->lastName}}</td>
            
            <td>{{$user->phonebook->phoneNumber}}</td>
           
            <td>@if($companyTeam) {{ $companyTeam->companyTeam? $companyTeam->companyTeam->teamName : 'no team' }} @else no team @endif</td>
            <td>{{ $iqc->amountofIQC }}</td>
            <td>{{ceil($average)}}</td>
            <td>{{ count($course)!=0? ceil(count($courseLogs)*100/count($course)) : '0'}}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>

