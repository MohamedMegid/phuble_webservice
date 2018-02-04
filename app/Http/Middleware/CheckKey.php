<?php

namespace App\Http\Middleware;

use Closure;

class CheckKey
{
    public $secret_key='phubleapi';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('secret_key')== $this->secret_key && $request->header('device_token'))
            return $next($request);
        else
        {
           return response()->json(['status'=>403,'message'=>'Not Authorize'], 403); 
        }
            
    }
}
