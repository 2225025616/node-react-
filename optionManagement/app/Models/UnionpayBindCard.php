<?php
/**
 * 银行卡绑定
 * @author      mozarlee
 * @time        2017-07-13 18:38:06
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnionpayBindCard extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'unionpay_bind_card_log';

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
        return $args;
    }
    
/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('status', 'asc')
                    ->orderBy('id', 'desc')
                    ->with('user')
                    ->paginate($this->page);
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据手机号查询
     * @return [type] [description]
     */
    public function search($search)
    {
        $user_object = new User();
        $user_data = $user_object->getMsgByMobile($search);

        if (empty($user_data)) {
            return null;
        }

        $result = self::where('user_id', '=', $user_data['id'])
                    ->orderBy('status', 'asc')
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
     * 更新状态
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function changeStatus($id, $status)
    {
        $object = self::find($id);
        if (empty($object)) {
            return false;
        }

        $object->status = $status;
        return $object->save();
    }

}
