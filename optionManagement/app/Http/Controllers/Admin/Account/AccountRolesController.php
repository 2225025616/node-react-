<?php

/**
 * 角色权限管理
 * @author		mozarlee
 * @time		2017-02-23 14:08:10
 * @created by	Sublime Text 3
 */


namespace App\Http\Controllers\Admin\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Roles;
class AccountRolesController extends Controller
{
	private $roles_object;

	function __construct()
	{
    	$this->roles_object = new Roles();
	}

    /**
     * 角色权限管理首页
     * @return [type] [description]
     */

    public function index(Request $request)
    {
    	$result = $this->roles_object->getAll();

    	$data = array(
    		'data' => $result,
            );
            
    
        return view('admin.account.admin_roles', $data);
    }

    /**
     * 新增
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function create(Request $request)
    {
    	return view('admin.account.show_roles');
    }

    /**
     * 新增写入
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function doCreate(Request $request)
    {
        $res = $this->roles_object->saveData($request->all());
        if( $res ){
            return redirect('admin_roles');
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '新增角色失败，请重试']);
    }

    /**
     * 更新
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	$id = $request->input('id');
    	$result = $this->roles_object->getDataById($id);

    	$data = array(
    		'data' => $result
    		);
    	return view('admin.account.show_roles', $data);
    }

    /**
     * 更新写入
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function doUpdate(Request $request)
    {
    	$res = $this->roles_object->saveData($request->all());
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '更新角色失败，请重试']);
    }

    /**
     * 启用角色可用
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function enableRole(Request $request)
    {
        $res = $this->roles_object->enableOrNot($request->all(), 1);
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '启用角色失败，请重试']);
    }

    /**
     * 禁用角色不可用
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function disableRole(Request $request)
    {
        $res = $this->roles_object->enableOrNot($request->all(), 0);
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '禁用角色失败，请重试']);
    }

}
