<?php

namespace App\Http\Middleware;

use Closure;
use Route;

use App\Models\ScanQrCodeRecord;
class ScanQrCode
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
        $url = Route::getCurrentRoute()->uri();
        $param = null; // 定义请求参数

        // 处理url获取请求参数
        $exp = explode('{', $url);
        if (count($exp) > 1) {
            $param = str_replace('}', '', $exp[1]);
        }

        $url = str_replace('{'.$param.'}', $request->$param, $url);
        $scan_qrcode_obj = new ScanQrCodeRecord();
        $scan_qrcode_obj->saveData($url);

        return $next($request);
    }
}
