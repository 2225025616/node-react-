<?php
/**
 * 企业基本资料
 * @author      mozarlee
 * @time        2017-03-01 12:45:33
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helpers\CreditRatingHelper;
use App\Helpers\BuildHashHelper;


class Company extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'company';

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
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
      
        $result = self::where('status', '=', 2)
                        ->orderBy('status', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($this->page);
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }   
        return $result;
    }

    /**
     * 获取所有数据不分页
     * @return [type] [description]
     */
    public function getList($status = 1)
    {
        
        $result = self::where('status', '=', $status)
                        ->get();


        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
       
        return $result;
    }


    /**
     * 根据id获取数据
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::find($id);
        return $result;
    }

    

    /**
     * 编辑公司信息
     * @return [type] [description]
     */
    public function editInfo($args){

        $object = self::find($args['id']);
        if( empty($object) ){
            return false;
        } else{
            
            if($args['status'] == 4)
            {
                $object->status = $args['status'];
            } else {
                $object->business_license_img = $args['business_license_img'];
                $object->company_name = $args['company_name'];
                $object->stock_num = $args['stock_num'];
            }
            $res = $object->save();
            return $res;
           
        }  
    }


   /**
     * 新增，更新企业信息
     * @return [type] [description]
     */
    public function saveData($args){
        $object = null;
        if( isset($args['id']) && !empty($args['id']) ){
            // 更新
            $object = self::find($args['id']);
            if( empty($object) ){
                return false;
            }
        }else{
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            $time = time();
            $object->created_at = date('Y-m-d H:i:s', $time);
            $object->apply_time =  $time;
            $object->status = 2;
        }

        $object->business_license_img = $args['business_license_img'];
        $object->company_name = $args['company_name'];
        $object->stock_num = $args['stock_num'];
       
        $res = $object->save();
        return $res;
    }



}
