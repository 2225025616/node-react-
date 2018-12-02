<?php
/**
 * 用户资金充值记录
 * 
 * @author                 
 * @time                    2017-09-13 10:56:13
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
class AccountCapitalRachargeLog extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'account_capital_racharge_log';

    /**
     * 主键
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    // protected $dateFormat = 'U';

    /**
     * 当前model连接数据库名称
     *
     * @var string
     */
    // protected $connection = 'connection-name';

    // 分页每页显示数据条数
    private $page = 15;


    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')
                    ->select('id', 'truename', 'mobile', 'email', 'user_verified');
    }

    /**
     * 关联回调信息
     * @return function [description]
     */
    public function callback()
    {
        return $this->hasOne('App\Models\UserCapital', 'request_id', 'request_id');
    }

    /**
     * 处理数据格式 多条
     * @return [type] [description]
     */
    private function dealResults($args)
    {
        foreach ($args as $key => $value) {
            $args[$key] = $this->dealResult($value);
        }
        return $args;
    }

    /**
     * 处理数据格式 单条
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private function dealResult($args)
    {
        $args->success_time = date('Y-m-d H:i:s', $args->success_time);
        $args->pay_time = date('Y-m-d H:i:s', $args->pay_time);

        if (isset($args->user->mobile)) {
            $mobile = $args->user->mobile;
            $args->user->old_mobile = $mobile;
            $args->user->new_mobile = dealStrHidden($mobile);
        }
        return $args;
    }

/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('id', 'desc')
                    ->with('user')
                    ->with('callback')
                    ->paginate($this->page);

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据手机号码查询充值记录
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function search($search)
    {
        $user_object = new User();
        $user_data = $user_object->getMsgByMobile($search);
        if (empty($user_data)) {
            return null;
        }

        $result = self::where('user_id', '=', $user_data['id'])
                    ->orderBy('id', 'desc')
                    ->with('user')
                    ->with('callback')
                    ->paginate($this->page);

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据用户id获取数据
     * @return [type] [description]
     */
    public function getDataByUserId($user_id)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->orderBy('status', 'asc')
                    ->orderBy('success_time', 'desc')
                    ->get();

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

}
