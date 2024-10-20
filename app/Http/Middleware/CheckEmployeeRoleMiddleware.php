<?php

namespace App\Http\Middleware;

use App\Models\CompanyMembers;
use Closure;
use Illuminate\Http\Request;

class CheckEmployeeRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(auth()->user() && auth()->user()->role == 'Employee'){
            $companyMember = CompanyMembers::where('member_id', auth()->user()->id)->first();
            if($companyMember && $companyMember->memberStatus){
                return $next($request);
            }else{
                return redirect()->route('notapproved');
            }
        }
        return $next($request);
    }
}
