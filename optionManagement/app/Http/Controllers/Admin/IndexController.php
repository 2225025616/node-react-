<?php
/**
 * 后台首页转发
 * 
 * @author     
 * @time        2017-09-12 09:01:44
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
	/**
	 * 登录成功转发
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request){
        return redirect('comapany/category');
    }

}
