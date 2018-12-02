<?php
/**
 * 用户绑定btc提现地址
 * 
 * @author                  mozarlee
 * @time                    2017-02-23 10:56:02
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBtcBindAddress extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'account_btc_bind_address';

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
     * 获取用户信息
     * @return [type] [description]
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')
                    ->select('id', 'truename', 'mobile', 'email', 'user_verified');
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
        $args->bind_time = date('Y-m-d H:i:s', $args->bind_time);
        return $args;
    }

/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('bind_time', 'desc')
                    ->with('user')
                    ->paginate($this->page);
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据用户id获取数据
     * @return [type] [description]
     */
    public function getDataByUserId($user_id)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->orderBy('bind_time', 'desc')
                    ->get();
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

/******************************************************前端接口**********************************************************/
   /**
     * 存储新增数据
     * @return bool 是否成功
     */
public function saveData($args,$user_id)
    {
        
        //根据算力类型名称去获取对应id
        $product_type = getProductHashType($args['product_hash_type']);
        $args['product_hash_type'] = $product_type['id'];

        $resu = self::where('user_id','=',$user_id)
                    ->where('product_hash_type','=',$args['product_hash_type'])
                    ->where('islock','=',1)
                    ->first();
                  
        if( !empty($resu) ){
        
            $resu->islock = 0;
            $res2 = $resu->save();
           
        }
        $object = null;
        $object =self::where('user_id','=',$user_id)
                        ->where('address','=',$args['address'])
                        ->where('product_hash_type','=',$args['product_hash_type'])
                        ->first();
        if(empty($object)){
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            $object->user_id = $user_id;
            $object->address = $args['address'];
            $object->product_hash_type = $args['product_hash_type'];
            $object->bind_time =time(); 
            $object->islock = 1;
            $res = $object->save();
        }else{
            $object->islock = 1;
            $res = $object->save();
        }
        
        if($res){
            return $object->address;
        }
        return $res;
    }

    /**
     * 展示绑定地址
     * @return [type] [description]
     */
    public function getshowaddress($args)
    {
        $result = self::where('user_id','=',$args['id'])
                        ->where('islock','=',1)
                        ->select('address','product_hash_type','user_id')->get();

        $hash_type_object = new ProductHashType();
        foreach ($result as $key => &$value) {
            $hash_type = $hash_type_object->getDataById($value['product_hash_type']);
            $value['product_hash_type'] = $hash_type['name'];
        }

        if(!empty($result)){
            $result = $result->toArray(); 
        }
        return $result;

    }

    /**
     * 展示唯一绑定地址
     * @return [type] [description]
     */
    public function getAddress($args,$user_id)
    {
        $result = self::where('user_id','=',$user_id)
                        ->where('product_hash_type','=',1)
                        ->where('islock','=',1)
                        ->select('address','product_hash_type','user_id')->first();
       
        if(!empty($result)){
            $result = $result->toArray(); 
        }
     
        return $result;

    }

    //根据地址查找数据
    public function getDataByAddr($args)
    {
        $result = self::where('address','=',$args['address'])
                        ->where('islock','=',1)
                        ->first();
       
        if(!empty($result)){
            $result = $result->toArray(); 
        }
        return $result;
    }
}
