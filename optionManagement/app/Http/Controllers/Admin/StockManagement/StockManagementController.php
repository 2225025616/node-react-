<?php

/**
 * 股票列表
 * @author      zhangzhe
 * @time        2017-09-13 15:09:35
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\StockManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\UserCapital;
class StockManagementController extends Controller
{
    private $user_capital_object;

    function __construct()
    {
        $this->user_capital_object = new UserCapital();
    }

    /**
     * 股权列表
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        $status = !empty($request->input('status')) ? $request->input('status') : 5;

        $today = strtotime(date('Y-m-d', time()));
        $tomorrow = $today + 86400;
        // 默认获取当天的记录
        $data = $this->user_capital_object->getDataByRange($today, $tomorrow, $status, 2);

        $total_yen = $this->user_capital_object->getTotalYenByRange($today, $tomorrow, $status, 2);

        $result = array(
            'data' => $data,
            'total_yen' => $total_yen,
            'status' => $status
            );
        return view('admin.stockmanagement.showStockManagementList', $result);
    }

    /**
     * 查询
     * @param  Request $request [description]
     * @param  [type]  $status  [description]
     * @return [type]           [description]
     */
    public function search(Request $request, $status)
    {
        $today = strtotime($request->input('start'));
        $tomorrow = strtotime($request->input('end'));
        if ($tomorrow <= $today) {
            return view('admin.error', ['body_style' => 'error-page', 'message' => '截止时间必须大于起始时间']);
        }

        // 
        $data = $this->user_capital_object->getDataByRange($today, $tomorrow, $status, 2);

        $total_yen = $this->user_capital_object->getTotalYenByRange($today, $tomorrow, $status, 2);

        $result = array(
            'data' => $data,
            'total_yen' => $total_yen,
            'status' => $status,
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            );
        return view('admin.stockmanagement.showStockManagementList', $result);
    }

}
