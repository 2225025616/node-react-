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


class ExerciseConfig extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'exercise_config';

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
    public function getAll($page = 1)
    {
        $limit = 15;
        $offest = ($page - 1) * $limit;

        $result = self::where('status','=','0')
                        ->where('stock_id','=','1')
                        ->offset($offest)
                        ->limit($limit)
                        ->get(); 
          
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
    
        $result = $result->toArray();
        return $result;
    }



    /**
     * 模糊查询
     * @return [type] [description]
     */
    public function search($key, $status = 0, $page = 15)
    {
        $result = self::where('status', '=', $status)
                    ->where( function( $query ) use ($key) {
                            $query->where('com_name', 'like', '%'.$key.'%')
                                ->orWhere('site_name', 'like', '%'.$key.'%')
                                ->orWhere('site_domain', 'like', '%'.$key.'%');
                        })
                    ->orderBy('created_at', 'desc')
                    ->with('category')
                    ->with('user')
                    ->paginate($page);
        $result = $this->dealResults($result);
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
            
            if($args['status'] == 1)
            {
                $object->status = $args['status'];
            } else {
                $object->stock_id = $args['stock_id'];
                $object->start_time = $args['start_time'];
                $object->end_time = $args['end_time'];
                $object->exercise_percentage = $args['exercise_percentage'];
                $object->duration = $args['duration'];
               
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
       
        $object = self::where('stock_id', '=', $args['id'])
                    ->first();

        if( empty($object) ){
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            $time = time();
            $object->created_at = date('Y-m-d H:i:s', $time);
            $object->status = 0;
           
        }
        $object->stock_id = $args['id'];
        $object->start_time = $args['start_time'];
        $object->end_time = $args['end_time'];
        $object->exercise_percentage = $args['exercise_percentage'];
        $object->duration = $args['duration'];

        $res = $object->save();
        return $res;
        
    }

    //获取行权分配列表
    public function getExerciseList($stock_id)
    {
       
        $result = self::where('status','=','0')
                        ->where('stock_id','=',$stock_id)
                        ->get(); 
          
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
    
        return $result;
    }
  


}
