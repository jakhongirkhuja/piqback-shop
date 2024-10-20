<table>
    <thead>
    <tr>
        <th>PhoneNumber </th>
        <th>FirstName</th>
        <th>LastName</th>
        <th>Role</th>
        <th>CompanyName</th>
        <th>Registered</th>
    </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      
      @php
        
        $phonebook = false;
        $exist = false;
        $mail = false;
        $phonebook = $user->phonebook;
        if($phonebook){
          $exist = true;
        }
        
         $companyName = false;
         if($user->role=='Employee'){
            $companyMember = \App\Models\CompanyMembers::where('member_id', $user->id)->first();
            if($companyMember){
               $company = \App\Models\Company::find($companyMember->company_id);
               if($company){
                    $companyName = $company->companyName;
                 }
            }
             
         }elseif($user->role=='Company Owner'){
            $company = \App\Models\Company::where('user_id', $user->id)->first();
             if($company){
                $companyName = $company->companyName;
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
           
            <td>{{$user->created_at}}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>
