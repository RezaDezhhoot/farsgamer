<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Livewire\str;

class UserSchedule
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
        $admin = auth()->user();
        if ($admin->hasRole('admin')){
            $day = strtolower(Carbon::now()->locale('en_EN')->dayName);

            if(is_null($admin->schedule) || empty($admin->schedule))
                return $next($request);
            else
                $adminHours = $admin->schedule->{$day};

            $overtime = $admin->overtimes()->whereRaw('? between start_at and end_at', [Carbon::now()])->first();

            if (!is_null($overtime) || (empty($adminHours)))
                return $next($request);

            if ($adminHours == 'close'){
                Auth::logout();
                return redirect()->route('auth');
            } elseif ($adminHours != 0 && !is_null($adminHours)){
                $hour = Carbon::make(now())->format('H.i');
                $adminHours = explode('-',$adminHours);
                if ($hour > (str_replace(':','.',$adminHours[0])) && $hour < str_replace(':','.',$adminHours[1]) ){
                    return $next($request);
                } else {
                    Auth::logout();
                    return redirect()->route('auth');
                }
            }
        }
        return $next($request);
    }
}
