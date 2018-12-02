<?php

/**
 * 用户绑定银行卡管理
 * @author      mozarlee
 * @time        2017-07-13 18:36:54
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\UnionpayBindCard;
class UserBindcardController extends Controller
{
    private $unionpay_bindcard_object;

    function __construct()
    {
        $this->unionpay_bindcard_object = new UnionpayBindCard();
    }

    /**
     * 用户绑定银行卡
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function index(Request $request)
    {
        $data = $this->unionpay_bindcard_object->getAll();

        $result = array(
            'data' => $data
            );
        return view('admin.user.showBindCardList', $result);
    }

    /**
     * 根据手机号查询查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $data = $this->unionpay_bindcard_object->search($request->input('search'));

        $result = array(
            'data' => $data,
            'search' => $request->input('search')
            );
        return view('admin.user.showBindCardList', $result);
    }

    /**
     * 验证绑定银行卡
     * @param  Request $request [description]
     * @param  [type]  $status  [description]
     * @return [type]           [description]
     */
    public function check(Request $request, $status)
    {
        $res = $this->unionpay_bindcard_object->changeStatus($request->input('id'), $status);
        if( $res ){
            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '更新失败，请重试。']);
    }
}
