<?php
/**
 * 管理员隶属群组角色关联表
 *
 * @author                  mozarlee
 * @time                    2017-02-23 10:56:40
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoles extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'admin_roles';

    /**
     * 主键
     * 
     * @var string
     */
    protected $primaryKey = 'autoid';

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
     * 获取角色
     * @return [type] [description]
     */
    public function roles(){
        return $this->hasOne('App\Models\Roles', 'role_id', 'role_id');
                    // ->where('locked', '=', 1);// 是否启用
    }

    /**
     * 保存管理员和角色的关系
     * @param  array $args    多个角色的id
     * @param  int   $user_id 用户id
     * @return bool           是否成功
     */
    public function saveData($args, $user_id)
    {
        if( empty($args) ){
            return true;
        }

        $res = null;
        // 先删除该用户的所有权限设定
        $data = self::where('user_id', '=', $user_id)->first();
        if( !empty($data) ){
            $res = self::where('user_id', '=', $user_id)->delete();
            if( !$res ){
                return false;
            }
        }


        // 删除成功
        $insert_data = array();
        // 再创建新的角色关系
        foreach ($args as $key => $value) {
            $insert_data[] = array(
                    'role_id' => intval($value),
                    'user_id' => $user_id
                );
        }

        $res = self::insert($insert_data);
        return $res;
    }

}
