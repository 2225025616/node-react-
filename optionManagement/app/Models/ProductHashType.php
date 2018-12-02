<?php
/**
 * 算力类型设置
 * @author      mozarlee
 * @time        2017-07-17 15:00:01
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHashType extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_hash_type';

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
        $args->created_time = date('Y-m-d H:i:s', $args->created_time);
        return $args;
    }


/************************************************************************/

    /**
     * 新增、更新
     * @return bool 是否成功
     */
    public function saveData($args)
    {
        $type_info = self::getAllArr();
        $type_name_list = array();
        foreach ($type_info as $val){
            $type_name_list[] = $val['name'];
        }


        $object = null;
        if( isset($args['id']) && !empty($args['id']) ){
            // 更新
            $object = self::find($args['id']);
            if($object->name != $args['name']){
                if(in_array($args['name'],$type_name_list)){
                    return false;
                }
            }
            if( empty($object) ){
                return false;
            }
        }else{
            if(in_array($args['name'],$type_name_list)){
                return false;
            }
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            $object->created_time = time();
        }

        $object->name = $args['name']; // 产品基类名称
        $res = $object->save();
        return $res;
    }

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('id', 'desc')
                    ->paginate($this->page);
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 获取数组数据
     * @return [type] [description]
     */
    public function getAllArr()
    {
        $result = self::orderBy('id', 'asc')
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
        if (!empty($result)) {
            $result = $result->toArray();
        }
        return $result;
    }

     /**
     * 根据id获取数据
     * @return [type] [description]
     */
    public function getDataByName($name)
    {
        $result = self::where('name','=',$name)->first();
        if (!empty($result)) {
            $result = $result->toArray();
        }
        return $result;
    }
    

}
