<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ErrorHelper;
use App\Models\User;
use Log;

class DockApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $valid_time = 24 * 3600;
    public function handle($request, Closure $next)
    {
        $url = $request->url();
        $ip = $_SERVER["REMOTE_ADDR"]; 
        Log::info("api请求",['time'=>time(),'url'=>$url,'ip'=>$ip]);                    
        header('Access-Control-Allow-Origin: *');
        $res =   $request->all();
        $sign = $res['sign'];
        if(empty($sign)){
            return response()->json( ErrorHelper::getErrorMsg('700001') );
        }
        $sign_key = base64_decode($sign);
        
        $sign_key = explode('&',$sign_key);
        $data = array();
        foreach($sign_key as $key=>$value){
            $arr = explode('=',$value);
            $data += array($arr[0] => $arr[1]);
        }
       
        if(!isset($data['token'])){
            return response()->json( ErrorHelper::getErrorMsg('600001') );
        }
      
        if($data['token'] == '0'){
          
            $request->attributes->add(compact('data'));
            // header('Access-Control-Allow-Origin: *');
            return $next($request);
            
        } else{
            $user_model = new User();
            $user = $user_model->getDataByToken($data['token']);
            if( empty($user) ){
                return response()->json( ErrorHelper::getErrorMsg('600001') );
            } elseif(($user['valid_time'] + $this->valid_time) < time()){
                return response()->json( ErrorHelper::getErrorMsg('100001') );  
            }
          
            $request->attributes->add(compact('data'));
            $request->attributes->add(compact('user'));
            // header('Access-Control-Allow-Origin: *');
            return $next($request);
        }
    }
}
