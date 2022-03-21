<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->status == User::CONFIRMED)
            return $next($request);

        return response([
            'data'=>[
                'message' => 'نیاز به احراز هویت'
            ],
            'status' => 'error'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
