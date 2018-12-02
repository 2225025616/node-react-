<?php

namespace App\Http\Middleware;

use Closure;
use View;
use Cookie;
use App\Models\Notification;
class Website
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
        $is_logined = false;
        $user_id = Cookie::get('site');
        if( !empty($user_id) ){
            // 获取是否有新消息
            $notification_object = new Notification();
            $count = $notification_object->getNotReadNums($user_id);
            View::share ('not_read', $count);

            $is_logined = true;
        }

        View::share ('is_logined', $is_logined);
        return $next($request);
    }
}
