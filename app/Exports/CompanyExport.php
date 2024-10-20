<?php

namespace App\Exports;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class CompanyExport implements FromView
{
    public function view(): View
    {
        $companies = Company::with('companyadress')->get();
        
        return view('exports.excel',[
            'companies'=>$companies
            ]);
    }

}
