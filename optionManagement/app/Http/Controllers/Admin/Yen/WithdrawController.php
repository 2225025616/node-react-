<?php

/**
 * 提现管理
 * @author      mozarlee
 * @time        2017-07-12 20:39:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\Yen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\AccountCapitalWithdrawLog;
class WithdrawController extends Controller
{
    private $withdraw_log_object;

    function __construct()
    {
        $this->withdraw_log_object = new AccountCapitalWithdrawLog();
    }

    /**
     * 提现列表
     * @return [type] [description]
     */
    public function index(Request $request)
    {
    	$data = $this->withdraw_log_object->getAll();

    	$result = array(
    		'data' => $data
    		);
    	return view('admin.yen.showWithdrawList', $result);
    }

    /**
     * 根据手机号码查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $data = $this->withdraw_log_object->search($request->input('search'));
        $result = array(
            'data' => $data,
            'search' => $request->input('search')
            );
        return view('admin.yen.showWithdrawList', $result);
    }

    /**
     * 提现审核通过
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function check(Request $request, $status)
    {
        $res = $this->withdraw_log_object->updateWithdrawStatus($request->input('id'), $status);
        if ($res) {
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '更新状态失败，请重试']);
    }

    /**
     * 打款完成
     * 修改提现记录状态，打款人，打款时间
     * 账户资金变动
     * user_captial新增提现记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function transferDone(Request $request)
    {
        $params['id'] = $request->input('id');
        $params['pay_username'] = session('admin_info')['true_name'];
        $params['pay_time'] = time();

        $res = $this->withdraw_log_object->transferDone($params);
        if ($res) {
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '操作失败，请重试']);
    }

    /**
     * 取消提现
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function cancel(Request $request)
    {
        $params['id'] = $request->input('id');
        $params['pay_username'] = session('admin_info')['true_name'];
        $params['pay_time'] = time();

        $res = $this->withdraw_log_object->cancel($params);
        if ($res) {
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '操作失败，请重试']);
    }

}
