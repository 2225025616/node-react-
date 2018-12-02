<?php

/**
 * 数据统计对账-用户人民币统计
 * @author      mozarlee
 * @time        2017-07-13 11:36:44
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Admin\Statistics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\UserCapital;
use App\Models\User;
class YencashController extends Controller
{
    private $user_object;

    function __construct()
    {
        $this->user_object = new User();
    }

    /**
     * 数据统计对账-用户人民币统计
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        // 获取所有用户
        $user_data = $this->user_object->getAll();

        // 统计用户人民币流水
        $user_data = $this->stat($user_data);

        $result = array(
            'data' => $user_data
            );
        return view('admin.statistics.showYencashList', $result);
    }

    /**
     * 根据姓名或手机号查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $user_data = $this->user_object->search($request->input('search'));
        // 统计用户人民币流水
        $user_data = $this->stat($user_data);

        $result = array(
            'data' => $user_data
            );
        return view('admin.statistics.showYencashList', $result);
    }

    /**
     * 统计用户人民币流水
     * @param  [type] $user_data [description]
     * @return [type]            [description]
     */
    private function stat($user_data)
    {
        // 统计用户人民币流水
        $user_capital_object = new UserCapital();
        foreach ($user_data as $key => $value) {
            // 获取充值总金额
            $user_data[$key]->recharge_total = $user_capital_object->getYenSum($value->id, 1, 2);
            // 获取提现总金额
            $user_data[$key]->withdraw_total = $user_capital_object->getYenSum($value->id, 2, 2);
            // 获取购买产品所花费的总金额
            $user_data[$key]->buy_total = $user_capital_object->getYenSum($value->id, 14, 2);

            // 获取购买转让算力所花费的总金额
            $user_data[$key]->buy_transfer_total = $user_capital_object->getYenSum($value->id, 7, 2);
            // 获取转让算力收入总金额
            $user_data[$key]->transfer_income_total = $user_capital_object->getYenSum($value->id, 6, 2);

            // 获取Btc卖出所得总金额
            $user_data[$key]->btc_income_total = abs($user_capital_object->getYenSum($value->id, 15, 2));

            $user_data[$key]->total_result = round(($user_data[$key]->recharge_total + $user_data[$key]->withdraw_total + $user_data[$key]->buy_total + $user_data[$key]->buy_transfer_total + $user_data[$key]->transfer_income_total + $user_data[$key]->btc_income_total), 2);

        }
        return $user_data;
    }

}
