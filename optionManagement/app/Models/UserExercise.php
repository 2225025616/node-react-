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
use App\Models\UserCapital;

class UserExercise extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_exercise';

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
        return $args;
    }


    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll($page = 1)
    {
    
        $stock_object = new Stock();

        $res = $stock_object->getAll();

        foreach ($res as &$value) {
            $sum = self::where('stock_id','=',$value['id'])
                                    ->where('status','=','2')
                                    ->sum('stock_amount');

  
            $value['percentage'] = round(($sum/$value['stock_amount']),8);

        }
        
        return $res;
    }

    /**
    * 分页,根据状态获取所有数据
    * @return [type] [description]
    */
    public function getList($status,$stock_id)
    {
        $result = self::where('status','=',$status)
                    ->where('stock_id','=',$stock_id)
                    ->with('user')
                    ->get(); 
        
        if( !empty($result) ){
            $result = $this->dealResults($result);
          
        }

        return $result;
    }

    /**
     * 模糊查询
     * @return [type] [description]
     */
    public function search($search)
    {
        $stock_object = new Stock();

        $res = $stock_object->search($search);

        foreach ($res as &$value) {
            $sum = self::where('stock_id','=',$value['id'])
                                    ->where('status','=','2')
                                    ->sum('stock_amount');

  
            $value['percentage'] = round(($sum/$value['stock_amount']),8);

        }

        return $res;
    }

    /**
     * 根据id获取数据
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::find($id)
                ->with('stock_info')
                ->with('user')
                ->first();
                
        if( !empty($result) ){
            $result = $this->dealResult($result);
        }
    
        $result = $result->toArray();
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
                    $object->status = 1; //未认领
                    
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

    //行权
    public function doExercise($args)
    {
        //查看可行权信息
        $user_exercise_object = self::find($args['id']);

        //查看用户账户信息
        $user_object = new User();
        $user = $user_object->getDataById($args['user_id']);
        //计算行权价格，判断是否够支付，
        $stock_object = new Stock();
        $stock = $stock_object->getDataById($user_exercise_object->stock_id);
        $fee = $stock->exercise_price * $user_exercise_object->stock_amount;

        if($user['balance_account'] < $fee){
            return -1;
        }elseif(time() > $user_exercise_object->end_time){//判断是否在行权时间内
            return -2;
        }elseif($user['forbid_exercise'] == 1){ //判断用户是否有行权权限
            return -3;
        }elseif($user_exercise_object->status == 2){
            return -4;
        }
        
        $user_capital_object = new UserCapital();
        $insert = array( 
                            'user_id'=>$args['user_id'],
                            'type'=>5,
                            'description'=>"行权获得",
                            'amount'=>$user_exercise_object->stock_amount,
                            'value'=>$fee,
                            'remark'=>$stock->stock_name
                        );

        $insert2 = array( 
                            'user_id'=>$args['user_id'],
                            'type'=>15,
                            'description'=>"行权获得",
                            'amount'=>$user_exercise_object->stock_amount,
                            'value'=>-$fee,
                            'remark'=>$stock->stock_name
                        );
        
       
       
        DB::beginTransaction();
        try{
            
            //增加行权记录
            $res1 = $user_capital_object->saveData($insert); 
            //增加资金记录
            $res2 = $user_capital_object->saveData($insert2);
            //扣除金额
            $res3 = $user_object->updateBalance($args['user_id'],-$fee);

            //改变状态
            $user_exercise_object->status = 2;
            $res4 = $user_exercise_object->save();

            DB::commit();
            return 1;
        } catch (\Exception $e){
           
            DB::rollback();//事务回滚
            return false;
            
        }
        
    }

}
