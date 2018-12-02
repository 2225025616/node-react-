<?php
/**
 * 用户资金明细
 * 
 * @author                  
 * @time                    2017-09-13 10:57:16
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class UserCapital extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_capital';

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
     * The connection name for the model.
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

    // /**
    //  * 关联算力类型
    //  * @return [type] [description]
    //  */
    // public function hashtype()
    // {
    //     return $this->hasOne('App\Models\ProductHashType', 'id', 'product_hash_type');
    // }

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
        $args->create_time = date('Y-m-d H:i:s', $args->create_time);
        return $args;
    }
    
/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('create_time', 'desc')
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
     * 获取指定类型,指定状态的所有数据
     * @return [type] [description]
     */
    public function getDataByType($type)
    {
        $result = self::where('type', '=', $type)
                    ->orderBy('create_time', 'desc')
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
     * 根据手机号查询
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function search($search, $type)
    {
        $user_object = new User();
        $user_data = $user_object->getMsgByMobile($search);
        if (empty($user_data)) {
            return null;
        }

        $result = self::where('user_id', '=', $user_data['id'])
                    ->where('type', '=', $type)
                    ->orderBy('create_time', 'desc')
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
     * 根据用户id,类型和状态获取资金总额
     * @param  [type] $type   [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function getYenSum($user_id, $type, $status)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->where('type', '=', $type)
                    ->where('status', '=', $status)
                    ->sum('value');
        return $result;
    }

    /**
     * 根据用户id,类型和状态获取btc总额
     * @param  [type] $type   [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function getBtcSum($user_id, $type, $status)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->where('type', '=', $type)
                    ->where('status', '=', $status)
                    ->sum('btc_amount');
        return $result;
    }

    /**
     * 保存提现数据
     * @param  [type] $data_object [description]
     * @return [type]              [description]
     */
    public function withdraw($data_object)
    {
        $object = null;
        if( isset($data_object->id) && !empty($data_object->id) ){
            // 更新
            $object = self::where('type', '=', 2)
                        ->where('order_id', '=', $data_object->id)
                        ->first();

            if( empty($object) ){
                $class_name = get_class();
                $object = new $class_name;
            }
        }else{
            return false;
        }

        $object->create_time = $data_object->apply_time;
        $object->user_id = $data_object->user_id;
        $object->type = 2;
        $object->description = '提现';
        $object->value = '-' . $data_object->amount;
        $object->order_id = $data_object->id;
        $object->remark = '手续费：'. $data_object->fee . '元';
        $object->status = $data_object->status;

        $res = $object->save();
        return $res;
    }

    /**
     * 根据时间范围获取指定类型、指定状态记录
     * @return [type] [description]
     */
    public function getDataByRange($start, $end, $type, $status)
    {
        $result = self::where('type', '=', $type)
                    ->where('status', '=', $status)
                    ->where('create_time', '>=', $start)
                    ->where('create_time', '<', $end)
                    ->with('user')
                    ->paginate($this->paginate);
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据时间范围获取指定类型、指定状态发生的总金额
     * @return [type] [description]
     */
    public function getTotalYenByRange($start, $end, $type, $status)
    {
        $result = self::where('type', '=', $type)
                    ->where('status', '=', $status)
                    ->where('create_time', '>=', $start)
                    ->where('create_time', '<', $end)
                    ->sum('value');
        return $result;
    }

    /**
     * 根据时间范围获取指定类型、指定状态发生的总Btc数量
     * @return [type] [description]
     */
    public function getTotalBtcByRange($start, $end, $type, $status)
    {
        $result = self::where('type', '=', $type)
                    ->where('status', '=', $status)
                    ->where('create_time', '>=', $start)
                    ->where('create_time', '<', $end)
                    ->sum('btc_amount');
        return $result;
    }

    /**
     * 提币审核通过，user_capital修改状态为处理中
     * @param  [type] $hash_income_id [description]
     * @param  [type] $status         [description]
     * @return [type]                 [description]
     */
    public function withdrawBtc($data_object, $status)
    {
        // 新增币类型，根据不同类型的算力操作相应的账户
        $object = self::where('type', '=', 16)
                ->where('product_hash_type', '=', $data_object->product_hash_type)
                ->where('order_id', '=', $data_object->id)
                ->first();
        if (empty($object)) {
            return false;
        }
        $object->status = $status;
        $res = $object->save();
        return $res;
    }

    /**
     * 保存推广所获得的奖励
     * type 为20表示是推广奖励金
     * @return [type] [description]
     */
    public function saveReward($invite_user_id, $data_object, $reward_fee)
    {
        $object = null;
        if( isset($data_object['id']) && !empty($data_object['id']) ){
            // 更新
            $object = self::where('type', '=', 20)
                        ->where('order_id', '=', $data_object['id'])
                        ->first();

            if( empty($object) ){
                $class_name = get_class();
                $object = new $class_name;
            }
        }else{
            return false;
        }

        $object->create_time = time();
        $object->user_id = $invite_user_id;
        $object->type = 20;
        $object->description = '推广奖励';
        $object->btc_amount = $reward_fee;
        $object->order_id = $data_object['id'];
        $object->remark = '来源用户id:'. $data_object['user_id'];
        $object->status = 2;

        $res = $object->save();
        return $res;
    }

    //保存数据
    public function saveData($args){
        
        // 新增
        $class_name = get_class();
        $object = new $class_name;
        $time = time();
        $object->create_time =  $time;
        $object->status = 2;

        $object->user_id = $args['user_id'];
        $object->type = $args['type'];
        $object->description = $args['description'];
        $object->amount = $args['amount'];
        $object->value = $args['value'];
        $object->remark = $args['remark'];
      
        $res = $object->save();
        return $res;
    }
    
}
