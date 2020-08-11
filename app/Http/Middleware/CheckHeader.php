<?php

namespace App\Http\Middleware;
use Closure;

class CheckHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $country_code = $request->header('X-Country-Code')?$request->header('X-Country-Code'):'';

        $valid_country_codes = array('BD','MM','PH','PHL','MY','NE','LK');
        if (!in_array($country_code, $valid_country_codes)){
             return response()->json('Invalid country in header');
        }

        $client_name = $request->header('X-Client-Name')?$request->header('X-Client-Name'):'';

        $valid_clients = array('ANDROID','IOS','WEBSITE','CRM','CH_ADMIN','CH_FB_BOT','CH_EMR');
        if (!in_array($client_name, $valid_clients)){
            return response()->json('Invalid client in header');
        }

        $request_id = $request->header('X-Request-ID')?$request->header('X-Request-ID'):'';
        if(!$request_id){
            return response()->json('request id not avaialable in header');
        }

        $client_version = $request->header('X-Client-Version')?$request->header('X-Client-Version'):'';
        if(!$client_version){
            return response()->json('client version not avaialable in header');
        }

        return $next($request);
    }
}