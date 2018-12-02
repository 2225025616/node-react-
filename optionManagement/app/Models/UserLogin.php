<?php
/**
 * 用户登录错误表
 * 
 * @author                  mozarlee
 * @time                    2017-02-23 10:57:08
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_login';

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
    
    /**
     * 根据id获取数据
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::where('id','=',$id)->first();
        if( !empty($result) ){
            $result = $result->toArray(); 
        }
        return $result;
    }

    /**
     * 新增、更新
     * @return bool 是否成功
     */
    public function saveData($user_id)
    {
        // 新增
        $class_name = get_class();
        $object = new $class_name;

        $ip = $_SERVER["REMOTE_ADDR"];
        $object->ip = $ip;
        $object->user_id = $user_id;
        $object->dateline = time();
        $res = $object->save();
       
        return $res;
       
        
    }

    /**
     * 根据user_id获取数据
     * @return [type] [description]
     */
    public function getDataByUserId($user_id)
    {
        $result = self::where('user_id','=',$user_id)->get();
        if( !empty($result) ){
            $result = $result->toArray(); 
        }
        return $result;
    }
    
    
}
