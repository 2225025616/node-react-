<?php

/**
 * 账户管理基本信息
 * 
 * @author      mozarlee
 * @time        2017-02-24 09:22:45
 * @created by  Sublime Text 3
 */


namespace App\Http\Controllers\Admin\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin;
use App\Models\Roles;
class AccountController extends Controller
{
	private $admin_object;

	function __construct(){
		$this->admin_object = new Admin();
	}

    /**
     * 账户管理基本信息首页
     * @return [type] [description]
     */
    public function index(Request $request)
    {
    	$result = $this->admin_object->getAll();

    	$data = array(
    		'data' => $result
    		);
        return view('admin.account.admin_account', $data);
    }

    /**
     * 新增
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function create(Request $request)
    {
    	$roles_object = new Roles();
    	$roles = $roles_object->getRolesOnly();
    	$data = array(
    		'roles' => $roles
    		);
    	return view('admin.account.show_account', $data);
    }

    /**
     * 新增写入
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function doCreate(Request $request)
    {
        $res = $this->admin_object->saveData($request->all());
        if( $res ){
            return redirect('admin_account');
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '新增账户信息失败，请重试']);
    }

    /**
     * 更新
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	$id = $request->input('id');
    	$result = $this->admin_object->getDataById($id);

    	$roles_object = new Roles();
    	$roles = $roles_object->getRolesOnly();

    	$data = array(
    		'roles' => $roles,
    		'data' => $result
    		);
    	return view('admin.account.show_account', $data);
    }

    /**
     * 更新写入
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function doUpdate(Request $request)
    {
    	$res = $this->admin_object->saveData($request->all());
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '更新账户信息失败，请重试']);
    }

    /**
     * 解除管理员锁定状体
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function enableRole(Request $request)
    {
        $res = $this->admin_object->enableOrNot($request->all(), 0);
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '解除账户锁定失败，请重试']);
    }

    /**
     * 锁定管理员账号不可用
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function disableRole(Request $request)
    {
        $res = $this->admin_object->enableOrNot($request->all(), 1);
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '锁定账户失败，请重试']);
    }

}
