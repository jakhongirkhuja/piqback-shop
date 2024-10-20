<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>City</th>
        <th>Region</th>
        <th>Street</th>
        <th>Owner</th>
        <th>PhoneNumber</th>
        <th>Email</th>
        <th>Members</th>
        <th>Members Count</th>
    </tr>
    </thead>
    <tbody>
    @foreach($companies as $company)
        @php
        $user = \App\Models\User::find($company->user_id);
        $phonebook = false;
        $exist = false;
        $mail = false;
         if($user){
            $phonebook = $user->phonebook;
            if($phonebook){
              $exist = true;
            }
            $email = $user->email;
         }
         $companyName = $company->companyName;
         $companyAdress = $company->companyadress;
         $city =false;
         $region =false;
         $street= false;
         if($companyAdress){
            $city = \App\Models\City::find($company->companyadress->city_id);
            $region = \App\Models\Region::find($company->companyadress->region_id);
            $street = $company->companyadress->addressline1;
         }
        
        @endphp
        @if($exist)
        <tr>
            <td>{{ $companyName}}</td>
            
             <td>{{ isset($city?->name_ru)? $city?->name_ru : '' }}</td>
           
            <td>{{ isset($region?->name_ru)? $region?->name_ru : '' }}</td>
            
           
            <td>{{ $street }}</td>
           
            <td>{{$user->firstName.' '.$user->lastName}}</td>
           
            <td>{{$phonebook->phoneNumber}}</td>
            <td>{{$email?->email}}</td>
            <td> @php $count = 0; @endphp
                @forelse($company->companymembers as $members)
                    @php $userPhone = \App\Models\User::with('phonebook')->where('id',$members->member_id)->first();
                    if($userPhone?->phonebook){
                    $count ++;
                           echo $userPhone?->phonebook->phoneNumber.', '; 
                    }
                    @endphp
                @empty
                @endforelse
            </td>
            <td>( {{$count}} )</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>
