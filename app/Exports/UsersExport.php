<?php

namespace App\Exports;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class UsersExport implements FromView
{
    public function view(): View
    {
        $users = User::with('phonebook')->get();
        
        return view('exports.userinfo',[
            'users'=>$users
            ]);
    }
    
}
