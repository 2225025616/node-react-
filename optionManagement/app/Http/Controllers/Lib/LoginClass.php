<?php
/**
 * 用户登录
 * @author      mozarlee
 * @time        2017-03-12 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use Cookie;

use App\Helpers\SignHelper;
use App\Helpers\ErrorHelper;
use App\Models\Account;
class LoginClass
{
	private $account;

	function __construct(){
		$this->account = new Account();
	}

	/**
	 * 接收已登录用户信息并保存到数据库
	 * @return [type] [description]
	 */
	public function saveLoginedUser(Request $request)
	{
		if( empty($request->input('data')) ){
			return response()->json( ErrorHelper::getErrorMsg('200', true) );
		}

		// 1、验证签名
		$validate_res = SignHelper::decodeSign($request->input('data'));
		if( !$validate_res ){
			// 签名验证失败
			return response()->json( ErrorHelper::getErrorMsg('500001') );
		}

		$data = $request->input('data');
	
		$args = array(
			'user_id' => isset($data['id']) ? $data['id'] : null,
			'mobile' => $data['mobile'],
			'head_logo' => $data['avatar'],
			'balance' => $data['balance']
			);
		// 2、保存用户信息到数据库，存在则更新数据
		$save_res = $this->account->saveData($args);

		// 3、保存用户登录信息到session或者cookie
		return response()->json( ErrorHelper::getErrorMsg('200', $save_res) );
	}

	/**
	 * 退出登录
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function loginOut(Request $request)
	{
		if( empty($request->input('user_id')) ){
			return response()->json( ErrorHelper::getErrorMsg('200', true) );
		}
		$user_id = $request->input('user_id');
		// 修改token
		$save_res = $this->account->updateToken($user_id);
		
		return response()->json( ErrorHelper::getErrorMsg('200', true) );
	}

	/**
	 * 获取用户登录信息
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public static function getLoginedData(Request $request)
	{
		$user_id = Cookie::get('site');
		return $user_id;
	}

}
