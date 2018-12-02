<?php

/**
 * 管理员
 * 
 * @author      mozarlee
 * @time        2017-02-24 09:56:18
 * @created by  Sublime Text 3
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\AdminRoles;
class Admin extends Model
{
    protected $guarded = [];
    protected $table = 'admin';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

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
     * 管理员角色关联
     * @return [type] [description]
     */
    public function adminRoles(){
        return $this->hasMany('App\Models\AdminRoles', 'user_id', 'user_id')
                    ->with('roles');
    }

    /**
     * 一层关联
     * @return [type] [description]
     */
    public function withoutRoleTable(){
        return $this->hasMany('App\Models\AdminRoles', 'user_id', 'user_id');
    }

    /**
     * 管理员登录验证
     * @return [type] [description]
     */
    public function loginCheck($args){
        $res = self::where('user_type', '=', 0)
                ->where('user_name', '=', $args['user_name'])
                ->where('password', '=', md5($args['password']))
                ->where('locked', '=', 0)
                ->with('adminRoles')
                ->first();
        if( empty($res) ){
            return null;
        }
        $res = $this->dealLoginResult($res);
        return $res;
    }

    /**
     * 处理返回参数函数
     * @return [type] [description]
     */
    private function dealLoginResult($object){
        $array = array();
        $data = $object->toArray();
        if( !empty($data['admin_roles']) ){
            // 处理当前登录用户对所有操作的权限
            foreach ($data['admin_roles'] as $key => $value) {
                $privilages = $value['roles']['privilages'];
                $privilages = explode('|', $privilages);

                foreach ($privilages as $k => $val) {
                    $array[$val] = $val;
                }
            }
            $data['admin_roles_dealt'] = $array;
        }

        $result = array(
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'true_name' => $data['true_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'locked' => $data['locked'],
            'admin_roles' => !empty($data['admin_roles_dealt']) ? $data['admin_roles_dealt'] : array(),
            );
        return $result;
    }

    /**
     * 新增、更新管理员
     * @return bool 是否成功
     */
    public function saveData($args)
    {
        $object = null;
        if( isset($args['user_id']) && !empty($args['user_id']) ){
            // 更新
            $object = self::find($args['user_id']);
            if( empty($object) ){
                return false;
            }
        }else{
            // 新增
            $class_name = get_class();
            $object = new $class_name;
        }

        if( $args['password'] != $args['old_password'] ){
            $object->password = md5($args['password']);
        }
        $object->user_name = $args['user_name'];
        $object->true_name = $args['true_name'];        
        $object->email = $args['email'];
        $object->phone = $args['phone'];
        $object->mobile = $args['mobile'];
        $object->locked = 0;

        // 事务处理，先保存管理员信息，获得user_id，删除该用户所有的角色关系，重新新建角色关系，完成
        DB::beginTransaction();
        try{
            $result = $object->save();
            if( !$result ){
                throw new \Exception("Failed");
            }

            $user_id = $object->user_id;

            $role = isset($args['role']) ? $args['role'] : null;
            // 保存管理员和角色的关系
            $admin_roles_object = new AdminRoles();
            $admin_roles_result = $admin_roles_object->saveData($role, $user_id);

            if( !$admin_roles_result ){
                throw new \Exception("Failed");
            }
            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
            echo $e->getMessage();
            die();
            return false;
        }

        return true;
    }

    /**
     * 锁定或解锁某个管理员的账户
     * @param int $status [1锁定0解锁]
     * @return [type] [description]
     */
    public function enableOrNot($args, $status = 0)
    {
        if( empty($args['id']) ){
            return false;
        }
        $object = self::find($args['id']);
        if( empty($object) ){
            return false;
        }

        $object->locked = $status;
        $res = $object->save();
        return $res;
    }

    /**
     * 分页获取管理员数据
     * @return object 管理员数据
     */
    public function getAll()
    {
        $result = self::orderBy('user_id', 'desc')
                    ->with('adminRoles')
                    ->paginate($this->page);

        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据id获取管理员信息
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::with('adminRoles')->find($id);
        if( !empty($result) ){
            $result = $result->toArray();
            $result = $this->dealResultRolesArr($result);
        }
        return $result;
    }

    /**
     * 根据用户名查询
     * @param  [type] $user_name [description]
     * @return [type]            [description]
     */
    public function getDataByUserName($user_name)
    {
        $result = self::where('user_name','=',$user_name)->first();
        if( !empty($result) ){
            $result = $result->toArray();
        }
        return $result;
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
     * 处理结果数据中的角色id为一维数组 单条
     * @return [type] [description]
     */
    private function dealResultRolesArr($args)
    {
        $roles_id_arr = array();
        if( !empty($args['admin_roles']) ){
            foreach ($args['admin_roles'] as $key => $value) {
                $roles_id_arr[] = $value['role_id'];
            }
        }
        $args['roles_id_arr'] = $roles_id_arr;
        return $args;
    }

    /**
     * 处理数据格式 单条
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private function dealResult($args){
        $admin_roles = '';
        $point = '';

        $args_array = $args->toArray();
        if( !empty($args_array['admin_roles']) ){
            foreach ($args_array['admin_roles'] as $key => $value) {
                if( $key > 0 ){
                    $point = '，';
                }
                $admin_roles .= $point.$value['roles']['role_name'];
            }
        }
        $args->admin_roles_val = $admin_roles;
        return $args;
    }

}
