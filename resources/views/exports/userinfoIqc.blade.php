<table>
    <thead>
    <tr>
        <th>Phone Number </th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Role</th>
        <th>Company Name</th>
        <th>Company Address</th>
        <th>Total IQC</th>
        <th>From Quests</th>
        <th>From Quizzes</th>
        <th>From Referral</th>
        <th>From Promocode</th>
        <th>Registered</th>
    </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      
      @php
        $quests =0;
        $quizz = 0;
        $referral = 0;
        $promocode = 0;
        $phonebook = false;
        $exist = false;
        $mail = false;
        $phonebook = $user->phonebook;
        if($phonebook){
          $exist = true;
        }
        $city =null;
            $region =null;
            $street= null;
         $companyName = false;
         if($user->role=='Employee'){
           
            $companyMember = \App\Models\CompanyMembers::where('member_id', $user->id)->first();
            if($companyMember){
               $company = \App\Models\Company::with('companyadress')->find($companyMember->company_id);
               
               if($company){
                    $companyName = $company->companyName;
                    $companyAdress = $company->companyadress;
                    if($companyAdress){
                        $city = \App\Models\City::find($company->companyadress->city_id);
                        $region = \App\Models\Region::find($company->companyadress->region_id);
                        $street = $company->companyadress->addressline1;
                    }
                 }
            }
             
         }elseif($user->role=='Company Owner'){
            $company = \App\Models\Company::with('companyadress')->where('user_id', $user->id)->first();
           
            if($company){
                $companyName = $company->companyName;
                $companyAdress = $company->companyadress;
                
                if($companyAdress){
                    $city = \App\Models\City::find($company->companyadress->city_id);
                    $region = \App\Models\Region::find($company->companyadress->region_id);
                    $street = $company->companyadress->addressline1;
                }
             }
             
         }
         
        $iqcUser = \App\Models\Money\Iqc::where('user_id',$user->id)->first();
        $iqcTransactions =  \App\Models\Money\IqcTransaction::where('user_id',$user->id)
        ->where(function ($query) {
                $query
                ->where('serviceName','quiz')
                ->orwhere('serviceName','ref link')
                ->orwhere('serviceName','quest')
                ->orwhere('serviceName','promoCode');
        })
        ->get();
        // $quizz = \App\Models\Money\IqcTransaction::where('user_id',$user->id)->where('serviceName','quiz')->sum('value');
        // $referral = \App\Models\Money\IqcTransaction::where('user_id',$user->id)->where('serviceName','ref link')->sum('value');
        // $quests = \App\Models\Money\IqcTransaction::where('user_id',$user->id)->where('serviceName','quest')->sum('value');
        // $promocode = \App\Models\Money\IqcTransaction::where('user_id',$user->id)->where('serviceName','promoCode')->sum('value');
        
        if($iqcTransactions->count()>0){
            foreach ($iqcTransactions as $key => $iqcTransaction) {
                if($iqcTransaction->serviceName=='ref link'){
                    $referral +=$iqcTransaction->value;
                }elseif ($iqcTransaction->serviceName=='quiz') {
                    $quizz +=$iqcTransaction->value;
                }elseif ($iqcTransaction->serviceName=='quest') {
                    $quests +=$iqcTransaction->value;
                }elseif ($iqcTransaction->serviceName=='promoCode') {
                    $promocode +=$iqcTransaction->value;
                }
            }
        }
        @endphp
        
      
        @if($exist)
        <tr>
            <td>{{$phonebook->phoneNumber}}</td>
            
           <td>{{$user->firstName}}</td>
           
            <td>{{$user->lastName}}</td>
            
           
           <td>{{$user->role}}</td>
           
            <td>{{$companyName}}</td>
            <td>{{ $city?->name_ru }},
                 {{ $region?->name_ru }}, {{ $street }}</td>
            <td>{{$iqcUser? $iqcUser->amountofIQC : 0}}</td>
            <td>{{$quests}}</td>
            <td>{{$quizz}}</td>
            <td>{{$referral}}</td>
            <td>{{$promocode}}</td>
            <td>{{$user->created_at}}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>
