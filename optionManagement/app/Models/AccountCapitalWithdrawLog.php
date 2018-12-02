<?php
/**
 * 用户资金充值记录
 * 
 * @author                  mozarlee
 * @time                    2017-02-23 10:56:17
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\UserCapital;
use DB;

class AccountCapitalWithdrawLog extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'account_capital_withdraw_log';

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
        $args->apply_time = date('Y-m-d H:i:s', $args->apply_time);
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
        $result = self::orderBy('status', 'asc') //升序
                    ->orderBy('apply_time', 'asc')
                    ->orderBy('id', 'desc')//降序
                    ->with('user')
                    ->paginate($this->page);  //分页

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据手机号码查询提现记录
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
        //WHERE 列 运算符 值
        $result = self::where('user_id', '=', $user_data['id'])
                    ->orderBy('status', 'asc')
                    ->orderBy('apply_time', 'asc')
                    ->orderBy('id', 'desc')
                    ->with('user')
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
                    ->orderBy('apply_time', 'desc')
                    ->get();

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 修改状态
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function changeStatus($id, $status)
    {
        $result = self::find($id);
        if (empty($result)) {
            return false;
        }

        $result->status = $status;
        $res = $result->save();
        return $res;
    }

    /**
     * 提现审核通过
     * account_capital_withdraw_log状态修改
     * user_capital状态修改
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function updateWithdrawStatus($id, $status)
    {
        // 事务处理
        DB::beginTransaction();
        try{
            // account_capital_withdraw_log状态修改
            $object = self::find($id);
            if (empty($object)) {
                return false;
            }

            $object->status = $status;
            $res = $object->save();
            if (!$res) {
                throw new \Exception("Failed");
            }

            // user_capital状态修改
            $user_capital_object = new UserCapital();
            $res = $user_capital_object->withdraw($object);
            if (!$res) {
                throw new \Exception("Failed");
            }

            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
            return false;
        }
        return true;
    }

    /**
     * 打款完成
     * 修改提现记录状态，打款人，打款时间
     * 账户资金变动
     * user_captial新增提现记录
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function transferDone($args)
    {
        // 事务处理
        DB::beginTransaction();
        try{

            // 修改提现记录状态，打款人，打款时间
            $object = self::find($args['id']);
            if (empty($object)) {
                throw new \Exception("Failed");
            }

            $object->status = 2;
            $object->pay_username = $args['pay_username'];
            $object->pay_time = $args['pay_time'];
            $res = $object->save();
            if (!$res) {
                throw new \Exception("Failed");
            }

            // 账户资金变动
            $user_object = new User();
            $res = $user_object->withdrawDone($object->user_id, $object->amount);
            if (!$res) {
                throw new \Exception("Failed");
            }

            // user_captial提现记录修改状态
            $user_capital_object = new UserCapital();
            $res = $user_capital_object->withdraw($object);

            if (!$res) {
                throw new \Exception("Failed");
            }

            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
            return false;
        }
        return true;
    }

    /**
     * 取消提现
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function cancel($args)
    {
        // 事务处理
        DB::beginTransaction();
        try{

            // 修改提现记录状态，打款人，打款时间
            $object = self::find($args['id']);
            if (empty($object)) {
                throw new \Exception("Failed");
            }

            $object->status = 3;
            $object->pay_username = $args['pay_username'];
            $object->pay_time = $args['pay_time'];
            $res = $object->save();
            if (!$res) {
                throw new \Exception("Failed");
            }

            // 账户资金变动
            $user_object = new User();
            $res = $user_object->cancelWithdraw($object->user_id, $object->amount);
            if (!$res) {
                throw new \Exception("Failed");
            }

            // user_captial提现记录修改状态
            $user_capital_object = new UserCapital();
            $res = $user_capital_object->withdraw($object);

            if (!$res) {
                throw new \Exception("Failed");
            }

            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
            return false;
        }
        return true;
    }

}
