<?php
/**
 * 期权基本资料
 * @author      mozarlee
 * @time        2017-03-01 12:45:33
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Helpers\CreditRatingHelper;
use App\Helpers\BuildHashHelper;


class Stock extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'stock';

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

    //关联的表
    public function company_info()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
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


    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
       
        $result = self::where('status','!=',3)
                        ->with('company_info') 
                        ->paginate($this->page);
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
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

    
    //编辑期权信息
    public function editInfo($args){

        $object = self::find($args['id']);

  
        if( empty($object) ){
            return false;
        } else{
            
            if($args['status'] == 3)
            {
                $object->status = $args['status'];
            } else {
                $object->company_id = $args['company_id'];
                $object->publisher_id = $args['publisher_id'];
                $object->stock_name = $args['stock_name'];
                $object->stock_amount = $args['stock_amount'];
                $object->exercise_price = $args['exercise_price'];
                $object->file1 = $args['file1'];
                $object->file2 = $args['file2'];
                $object->file3 = $args['file3'];
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
        }
       

        $object->company_id = $args['company_id'];
        $object->publisher_id = $args['publisher_id'];
        $object->stock_name = $args['stock_name'];
        $object->stock_amount = $args['stock_amount'];
        $object->exercise_price = $args['exercise_price'];
        $object->file1 = $args['file1'];
        $object->file2 = $args['file2'];
        $object->file3 = $args['file3'];
        $object->token_id = $args['token_id'];
        $object->token_name = $args['token_name'];
           
        $object->created_at = date('Y-m-d H:i:s', time());
        $object->publish_time =  time();
        $object->status = 1;

        
        $res = $object->save();
        if($res){
            return $object->id;
        }
        
    }

    
    /**
     * 模糊查询
     * @return [type] [description]
     */
    public function search($key)
    {
        $result = self::where( function( $query ) use ($key) {
                            $query->where('stock_name', 'like', '%'.$key.'%');
                                
                        })

                    ->with('company_info')
                    ->get();
        if (!empty($result))
        {
            $result = $this->dealResults($result);
        }           
       
        return $result;
    }

    //更新状态
    public function updateStatus($args)
    {
        $result = self::where('id','=',$args['id'])
                    ->update(['status'=>$args['status']]);
        
        return $result;

    }
  


}
