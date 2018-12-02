<?php

/**
 * 充值管理
 * @author     
 * @time        2017-09-13 19:21:16
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\Yen;

use Illuminate\Http\Request;  //接参数
use App\Http\Controllers\Controller;
use App\Models\AccountCapitalRachargeLog;
class RechargeController extends Controller
{
    private $reacharge_log_object; 
    //初始化
    function __construct()
    {
        $this->reacharge_log_object = new AccountCapitalRachargeLog();
    }

    /**
     * 充值列表
     * @return [type] $request 是 Request类的实例，作为函数index的输入参数
     */
    public function index(Request $request)
    {
    	$data = $this->reacharge_log_object->getAll();

    	$result = array(
    		'data' => $data
    		);
    	return view('admin.yen.showRechargeList', $result);
    }

    /**
     * 根据手机号码查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $data = $this->reacharge_log_object->search($request->input('search'));
        $result = array(
            'data' => $data,
            'search' => $request->input('search')
            );
        return view('admin.yen.showRechargeList', $result);
    }

}
