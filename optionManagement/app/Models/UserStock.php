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

use App\Models\User;
use App\Models\Stock;
class UserStock extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_stock';

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
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function stock_info()
    {
        return $this->hasOne('App\Models\Stock', 'id', 'stock_id');
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
        $args['user_stock_id'] = $args['id'];
        $args['end_time'] = date('Y-m-d H:i:s',$args['end_time']);
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

        $result = self::offset($offest)
                        ->limit($limit)
                        ->with('user') 
                        ->get(); 
          
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
    
        $result = $result->toArray();
        return $result;
    }


    /**
    * 分页,根据状态获取所有数据
    * @return [type] [description]
    */
    public function getList($page = 1,$status = 1,$user_id)
    {
        $limit = 15;
        $offest = ($page - 1) * $limit;

        $result = self::where('status','=',$status)
                    ->where('user_id','=',$user_id)
                    ->offset($offest)
                    ->limit($limit)
                    ->with('stock_info')
                    ->orderBy('created_at','desc') 
                    ->get(); 
        
        if( !empty($result) ){
            $result = $this->dealResults($result);
            $result = $result->toArray();
        }

        return $result;
    }

    /**
    *  获取当前的分配列表
    * @return [type] [description]
    */
    public function getPublishList($stock_id)
    {
        $result = self::where('stock_id','=',$stock_id)
                    ->where('status','=',0)
                    ->with('user') 
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
        $result = self::with('stock_info')->find($id);
        if( !empty($result) ){
            $result = $this->dealResult($result);
           
        }
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

    //编辑状态
    public function editStatus($args)
    {
        $object = self::find($args['id']);
            
        if( empty($object) ){
            return false;
        } else{
            
            //statua = 2 已认领
            $object->status = $args['status'];
           
            $res = $object->save();
            return $res;
            
        }  
    }

    //确认发放
    public function publish($args)
    {
        $object = self::where('stock_id','=',$args['id'])
                    ->update(['status'=>1]);
        return $object;
    } 

   /**
     * 新增，更新
     * @return [type] [description]
     */
    public function saveData($args,$stock_id){

        DB::beginTransaction();
        try{
            $user_object = new User();
            for($i=1;$i<count($args);$i++){

                $user_id = $user_object->getUserData($args[$i]);
                if ($user_id < 0){
                    throw new \Exception("第".$i."条数据有误，请仔细核对，请确保用户已经注册且实名认证");
                } else{
                    $stock_amount = $args[$i][3]; 

                    $object = self::where('stock_id', '=', $stock_id)
                                    ->where('user_id','=',$user_id)
                                    ->first();

                    if( empty($object) ){
                        // 新增
                        $class_name = get_class();
                        $object = new $class_name;
                       
                    }
 
                    $object->user_id = $user_id;
                    $object->stock_id = $stock_id; 
                    $object->stock_amount = $stock_amount;

                    $time = time(); 
                    $object->created_at = date('Y-m-d H:i:s', $time);
                    $object->publish_time =  $time;
                    $object->end_time = $time + 86400*7; 
                    $object->status = 0; //已上传
                    
                    $res = $object->save();

                    if($res){
                        $data[] = array('id'=>$object->id);     
                    } else {
                        throw new \Exception('数据保存异常，请重新添加');
                    }

                }
            }   

            DB::commit();
        } catch (\Exception $e){
           
            DB::rollback();//事务回滚
            return false;
            
        }
        return $data;
   
    }

    /**********************************************************前端接口**************************************************************/

    public function getListByUserid($user_id)
    {
        $result = self::where('user_id','=',$user_id)
                        ->with('stock_info')
                        ->get();
                        
        if( !empty($result) ){
            $result = $this->dealResults($result);
            $result = $result->toArray();
           
        }
        return $result;
    }


}
