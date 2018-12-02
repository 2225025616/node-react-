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

class UserAccountStock extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_stock_account';

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



    //获取用户账户信息
    public function getUserAccount($user_id)
    {

        $result = self::where('user_id','=',$user_id)
                    ->get();

        if ( empty($result)){
            return -1;
        } else{
            $result = $this->dealResults($result);
            $result = $result->toArray();
        }

        return $result;
    }

    //更新
    public function editUserStock($args)
    {
        $object = self::where('user_id','=',$args['user_id'])
                        ->where('stock_num','=',$args['stock_num'])
                        ->first();        
        if( empty($object) ){
            return -1;
        } else{    
            
        $object->amount = $object->amount + $args['amount'];
    
        // $object->email = $args['email'];
        // $object->forbid_trade = $args['status'];
        // $object->forbid_exercise = $args['status'];     
        $res = $object->save();
        return $res;
            
        }
    }


    //获取用户指定股票的数量
    public function getUserStock($user_id,$stock_num)
    {

        $result = self::where('user_id','=',$user_id)
                    ->where('stock_num','=',$stock_num)
                    ->first();

        if ( empty($result)){
            return -1;
        } else{
            $result = $this->dealResults($result);
            $result = $result->toArray();
        }

        return $result;
    }

    //根据id获取信息

    public function getDataById($id)
    {
        $result = self::find($id);
        return $result;
    }

}
