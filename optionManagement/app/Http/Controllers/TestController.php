<?php
/**
 * test
 * @author      mozarlee
 * @time        2017-04-06 11:54:15
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\SmsHelper;
class TestController
{

    public function index(Request $request)
    {
        SmsHelper::sendSms('15958042925', '这是一条测试短信');
    }

}
