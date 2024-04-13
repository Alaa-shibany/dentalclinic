<?php

namespace App\Http\Middleware;

use App\Models\Doctor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MakeSureMobileIsVerified
{
    use \App\Helpers\Response;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=request()->user();
        if($user instanceof Doctor && !$user->hasVerifiedMobile()){
            self::error(401,__('custom.mobile_not_verified'));
        }
        return $next($request);
    }
}
