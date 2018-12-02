<?php
/**
 * 期权基本资料
 * @author      zhangzhe
 * @time        2017-09-12 
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helpers\CreditRatingHelper;
use App\Helpers\BuildHashHelper;
use App\Models\Modules;

class Roles extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * 主键
     * 
     * @var string
     */
    protected $primaryKey = 'role_id';

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
         $privilages = explode('|', $args->privilages);
        
         $moduls_object = new Modules();
         $param = array(
             'module_code' => $privilages
             );
         $modules = $moduls_object->getDataByCode($param);
         $args->modules = $modules;
         return $args;
     }


  /**
     * 新增、更新角色
     * @return bool 是否成功
     */
     public function saveData($args)
     {
         $object = null;
         if( isset($args['role_id']) && !empty($args['role_id']) ){
             // 更新
             $object = self::find($args['role_id']);
             if( empty($object) ){
                 return false;
             }
         }else{
             // 新增
             $class_name = get_class();
             $object = new $class_name;
         }
 
         $object->role_name = $args['role_name'];
         $object->description = $args['description'];
         $object->privilages = $args['privilages'];
         $res = $object->save();
         return $res;
     }
 
     /**
      * 启用或禁用某个角色
      * @param int $status [1启用0禁用]
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
      * 分页获取所有数据
      * @return [type] [description]
      */
     public function getAll()
     {
         $result = self::orderBy('locked', 'asc')
                     ->orderBy('role_id', 'asc')
                     ->paginate($this->page);
         // 处理数据格式
         if (!empty($result)) {
             // 处理返回结果数据格式
             $result = $this->dealResults($result);
         }
         return $result;
     }
 
     /**
      * 仅获取已启用的角色信息，不关联其他数据表
      * @return [type] [description]
      */
     public function getRolesOnly()
     {
         $result = self::select('role_id', 'role_name','description')
                     ->where('locked', '=', 1)
                     ->orderBy('role_id', 'asc')
                     ->get()
                     ->toArray();
         return $result;
     }
 
     /**
      * 根据id获取数据
      * @return [type] [description]
      */
     public function getDataById($id)
     {
         $result = self::find($id);
         if( !empty($result) ){
             $result = $result->toArray();
 
             $moduls_object = new Modules();
             $privilages = explode('|', $result['privilages']);
             $param = array(
                 'module_code' => $privilages
                 );
             $modules = $moduls_object->getDataByCode2($param);
             $result['privilages_dealt'] = $modules;
             // $result['privilages_dealt2'] = $privilages;
         }
         return $result;
     }

}
