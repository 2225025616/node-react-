<?php

/**
 * 用户认证资料
 * @author      mozarlee
 * @time        2017-07-11 13:59:32
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\UserTruenameValid;
class UserVerifyController extends Controller
{
	private $user_truename_valid_object;

	function __construct()
	{
		$this->user_truename_valid_object = new UserTruenameValid();
	}

    /**
     * 用户认证资料
     * @return [type] [description]
     */
    public function index(Request $request)
    {
    	$data = $this->user_truename_valid_object->getAll();
    	$result = array(
    		'data' => $data
    		);
    	return view('admin.user.showValidList', $result);
    }

    /**
     * 根据姓名或手机号查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
    	$data = $this->user_truename_valid_object->search($request->input('search'));

    	$result = array(
    		'data' => $data
    		);
    	return view('admin.user.showValidList', $result);
    }

    /**
     * 禁止登陆、解禁
     * @return [type] [description]
     */
    public function updateStatus(Request $request, $status) 
    {
    	$user_id = $request->input('user_id');
    	$res = $this->user_truename_valid_object->updateStatus($user_id, $status);
    	if ($res) {
    		return redirect()->back();
    	}
        return view('admin.error', ['body_style' => 'error-page', 'message' => '更新状态失败，请重试']);
    }

}
