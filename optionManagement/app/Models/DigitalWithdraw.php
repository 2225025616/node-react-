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

use App\Models\User;

class DigitalWithdraw extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'account_btc_withdraw_log';

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

   
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
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
    public function getAll($status = 1, $page = 1)
    {
        $limit = 15;
        $offest = ($page - 1) * $limit;
        $result = self::offset($offest)
                        ->limit($limit)
                        ->orderBy('create_time','desc')
                        ->with('user') 
                        ->get();


        if( !empty($result) ){
            $result = $this->dealResults($result);
            $result = $result->toArray();
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
     * 禁止用户操作
     * @return [type] [description]
     */
    public function editStatus($args){

        $object = self::find($args['id']);

        if( empty($object) ){
            return false;
        } else{    
            
        $object->status = $args['status'];

        $res = $object->save();
        return $res;
           
        }  
    }



    
     //模糊搜索
     public function search($args)
     {
        $user_object = new User();
        $user = $user_object->fuzzy_search($args['key']);

        $limit = 15;
        $offest = ($args['page'] - 1) * $limit;

        $result = self::whereIn('user_id',$user)
                ->offset($offest)
                ->limit($limit)
                ->with('user')
                ->get();


        if( !empty($result) ){
            $result = $this->dealResults($result);
            $result = $result->toArray();
        }

        return $result;
      
     }

   /**
     * 新增，更新企业信息
     * @return [type] [description]
     */
    public function saveData($args){
        $object = self::where('company_name', '=', $args['company_name'])
                    ->first();

        if( empty($object) ){
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

    //获取用户信息
    public function getUserData($args)
    {

        $object = self::where('mobile','=',$args[0])
                    ->where('truename','=',$args[1])
                    ->where('idcard','=',$args[2])
                    ->first();

        if ( empty($object)){
            return -1;
        } else{
            $user_id = $object->id;
            return $user_id;
        }
    }

}
