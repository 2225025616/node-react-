<?php
/**
 * 账户表
 * @author      mozarlee
 * @time        2017-03-11 12:11:28
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Account extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'account';

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
    public $timestamps = true;

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

    public function certify(){
        return $this->hasOne('App\Models\AccountCertify', 'user_id', 'user_id')
                    ->where('status', '=', 1)
                    ->orderBy('type', 'asc');
    }

    /**
     * 更新用户信息
     * @return [type] [description]
     */
    public function saveData($args){
        $object = self::where('user_id', '=', $args['user_id'])
                    ->first();

        if( empty($object) ){
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            $time = time();
            $object->created_at = date('Y-m-d H:i:s', $time);
        }

        $object->user_id = $args['user_id'];
        $object->mobile = $args['mobile'];
        $object->head_logo = $args['head_logo'];
        $object->balance = $args['balance'];
        $res = $object->save();
        return $res;
    }

    /**
     * 根据用户id获取信息
     * @return [type] [description]
     */
    public function getDataByUserId($user_id)
    {
        $result = self::where('user_id', '=', $user_id)->first();
        if( !empty($result) ){
            $result = $result->toArray();
            $result['old_mobile'] = $result['mobile'];
            $result['mobile'] = dealStrHidden($result['mobile']);
        }
        return $result;
    }
}
