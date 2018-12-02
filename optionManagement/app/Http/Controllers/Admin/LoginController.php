<?php
/**
 * 后台登录
 * 
 * @author      
 * @time        
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'admin/company/category';

    /**
     * 登录页面
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request)
    {
        return view('admin.login');
    }

    /**
     * 登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function doLogin(Request $request)
    {

        $request->session()->forget('blockchain_rz_admin_info');

        $admin_model = new Admin();

        $res = $admin_model->loginCheck($request->all());
        if( empty($res) ){
            // 登录失败
            $request->session()->flash('error_msg', '登录失败，用户名密码不匹配！');
            return redirect()->back();
        }

        $request->session()->put('blockchain_rz_admin_info', $res);
        // 登录成功
        return redirect($this->redirectTo);
    }

    /**
     * 退出登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function loginOut(Request $request){
        $request->session()->forget('blockchain_rz_admin_info');
        return redirect('admin/login');
    }
}
