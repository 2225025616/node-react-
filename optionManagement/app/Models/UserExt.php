<?php
/**
 * 用户扩展表
 * @author      mozarlee
 * @time        2017-07-17 17:52:15
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserHashIncomeDetails;
use DB;
use App\Models\ProductHashType;
use App\Helpers\CacheDataHelper;

class UserExt extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_ext';

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
     * The connection name for the model.
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
    
/************************************************************************/


    /**
     * 根据主键更新余额
     * @return [type] [description]
     */
    public function updateBtcBalanceById($id, $amount, $type = 1)
    {
        // 新增币类型，给不同类型的账户打币
        $object = self::where('user_id', '=', $id)
                        ->where('type', '=', $type)
                        ->first();

        if (empty($object)) {
            // 新增
            $class_name = get_class();
            $object = new $class_name;
            
            $object->coin_account = 0.00000000;
            $object->freeze_coin_account = 0.00000000;
            $object->freeze_coin_withdraw_account = 0.00000000;
            $object->type = $type;
            $object->user_id = $id;
        }

        $object->coin_account = $object->coin_account + $amount;
        return $object->save();
    }

    /**
     * 提币成功，删除冻结资金
     * @return [type] [description]
     */
    public function withdrawBtcDone($id, $amount, $type = 1)
    {
        // 新增币类型，根据不同类型的算力操作相应的账户
        $object = self::where('user_id', '=', $id)
                        ->where('type', '=', $type)
                        ->first();

        if (empty($object)) {
            return false;
        }

        if ($object->freeze_coin_withdraw_account - $amount < 0) {
            return false;
        }
        
        $object->freeze_coin_withdraw_account = $object->freeze_coin_withdraw_account - $amount;
        return $object->save();
    }

    /**
     * 取消打币
     * @return [type] [description]
     */
    public function cancelWithdrawBtc($id, $amount, $type = 1)
    {
        // 新增币类型，根据不同类型的算力操作相应的账户

        $object = self::where('user_id', '=', $id)
                        ->where('type', '=', $type)
                        ->first();
        if (empty($object)) {
            return false;
        }

        if ($object->freeze_coin_withdraw_account - $amount < 0) {
            return false;
        }
        
        $object->freeze_coin_withdraw_account = $object->freeze_coin_withdraw_account - $amount;
        $object->coin_account = $object->coin_account + $amount;
        return $object->save();
    }




    //注册完生成账户
    public function saveData($type,$user_id)
    {
        $object = self::where('user_id','=',$user_id)->where('type','=',$type)->first();
        if( empty($object) ){
           // 新增
           $class_name = get_class();
           $object = new $class_name;
        }
        $object->type = $type;
        $object->user_id = $user_id;
       
        $res = $object->save();
        return $res;
    }


    //根据用户id查看算力账户信息
    public function getDataByUser($args,$user_id)
    {

        $result = self::where('user_id','=',$user_id)
                        ->where('type','=',$args['product_hash_type'])
                        ->first();
        if(!empty($result)){
            $result = $result->toArray();
        }else{
            //查无账户信息，添加信息
            $res = self::saveData($args['product_hash_type'],$user_id);
            if($res){
                $result = self::where('user_id','=',$user_id)
                                ->where('type','=',$args['product_hash_type'])
                                ->first();
                $result = $result->toArray();
            }
        }

        //累积获得收益
        $user_hash_income_details_model = new UserHashIncomeDetails();
        $total_hash = $user_hash_income_details_model->getTotal($user_id,$args['product_hash_type']);
        
        //今日获得收益
        $today_hash = $user_hash_income_details_model->getTotalIncome($user_id,$args['product_hash_type']);
        
       
        $data = array(
                    'today_hash'=>$today_hash,
                    'balance_account'=>$result['coin_account'],
                    'total_hash'=>$total_hash,
                    // 'hash_balance_account'=>$hash_balance_account,
                    // 'coin_price'=>$coin_price,
                    // 'output'=>$output,
                    'freeze_coin_withdraw_account'=>$result['freeze_coin_withdraw_account'],   
                );
        return $data;
    }

    //根据用户id查看算力账户信息
    public function getUserCoin($args,$user_id)
    {

        $result = self::where('user_id','=',$user_id)
                        ->where('type','=',$args['product_hash_type'])
                        ->first();
        if(!empty($result)){
            $result = $result->toArray();
        }

        
        return $result;
    }


    
    //提币冻结账户资金
    public function updateAccount($args)
    {
        // 事务处理
        DB::beginTransaction();
        try{
            $object = self::where('user_id','=',$args['user_id'])
                            ->where('type','=',$args['product_hash_type'])
                            ->first();

            if(empty($object)){
                throw new \Exception("Failed");
            }
           
            $object->coin_account = $object->coin_account - $args['amount'];//扣除账户余额
          
            $object->freeze_coin_withdraw_account = $object->freeze_coin_withdraw_account + $args['amount'];//加入冻结金额
        
            $result = $object->save();
           
            if( !$result ){

                throw new \Exception("Failed");
            }
           
            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
            return false;
        }
        return true;
    }
    
    //获得用户拥有的数字货币
    public function getUserHave($user_id)
    {

        $result = self::where('user_id','=',$user_id)
                        ->where(function ($query){
                            $query->where('coin_account', '>', 0)->orWhere('freeze_coin_withdraw_account', '>', 0);
                        })
                        ->get()->toArray();
        
        $product_hash_type_model = new ProductHashType();
        $cacheData = new CacheDataHelper();
        $res = array();
        if(!empty($result)){
            // $result = $result->toArray();
            foreach ($result as $key => &$value) {
                //获得资产收益
                $product_hash_type = $product_hash_type_model->getDataById($value['type']);
                $coin_data = $cacheData->caiyun_api();
                
                //现货资产
                if ($product_hash_type['name'] == 'BTC') {
                    $hash_balance_account = $value['coin_account'] * $coin_data[0]['price']; 
                    $coin_price = $coin_data[0]['price'];
                    $output = $coin_data[0]['output'];
                } elseif($product_hash_type['name'] == 'DASH'){
                    $hash_balance_account = $value['coin_account'] * $coin_data[1]['price'];
                    $coin_price = $coin_data[1]['price'];
                    $output = $coin_data[1]['output'];
                        
                } elseif($product_hash_type['name'] == 'LTC'){
                    $hash_balance_account = $value['coin_account'] * $coin_data[2]['price'];    
                    $coin_price = $coin_data[2]['price']; 
                    $output = $coin_data[2]['output'];
                            
                } elseif($product_hash_type['name'] == 'ETC'){
                    $hash_balance_account = $value['coin_account'] * $coin_data[3]['price'];
                    $coin_price = $coin_data[3]['price']; 
                    $output = $coin_data[3]['output'];
                                
                } elseif($product_hash_type['name'] == 'ZEC'){
                    $hash_balance_account = $value['coin_account'] * $coin_data[4]['price']; 
                    $coin_price = $coin_data[4]['price']; 
                    $output = $coin_data[4]['output'];
                                
                } elseif($product_hash_type['name'] == 'ETH'){
                    $hash_balance_account = $value['coin_account'] * $coin_data[5]['price']; 
                    $coin_price = $coin_data[5]['price']; 
                    $output = $coin_data[5]['output'];
                                
                } else{
                    $hash_balance_account = 0.00; 
                    $coin_price = 0.00;
                    $output = 0;
   
                } 

                $res[] = array(
                            'hash_balance_account'=>sprintf("%.2f",$hash_balance_account),
                            'balance_account'=>$value['coin_account'],
                            'freeze_withdraw_account'=>$value['freeze_coin_withdraw_account'],   
                            'coin_price'=>$coin_price,
                            'output'=>$output,
                            'product_hash_type_name'=>$product_hash_type['name'],
                            'product_hash_type'=>$product_hash_type['id']
                        );
            }
        } else{
            $res = array();
        }
   
        return $res;
    }
}
