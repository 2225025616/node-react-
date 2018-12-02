<?php
/**
 * 用户管理
 * 
 * @author                  
 * @time                    2017-09-23 10:57:12
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\ProductHashType;
use App\Models\UserExt;
use App\Models\AccountCapitalRachargeLog;



class User extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user';

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
     * 关联地址表
     * @return [type] [description]
     */
    public function bind_address()
    {
        return $this->hasMany('App\Models\AccountBtcBindAddress', 'user_id', 'id');
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
        $args->register_time = date('Y-m-d H:i:s', $args->register_time);
        $args->last_login_time = date('Y-m-d H:i:s', $args->last_login_time);
        // $args->mobile = dealStrHidden($args->mobile);
        $args->old_mobile = $args->mobile;
        $args->idcard = dealStrHidden($args->idcard, 6, 11);
        return $args;
    }
    
/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        //orderBy('last_login_time', 'desc')
        $result = self::orderBy('register_time', 'desc')->get();
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAllList()
    {
        //orderBy('last_login_time', 'desc')
        $result = self::orderBy('register_time', 'desc')->paginate($this->page);
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
        $result = self::orderBy('id', 'desc')
            ->get();

        return $result;
    }
    /**
     * 运维系统截取数据
     * @return [type] [description]
     */
    public function getAllArrOp($read)
    {
        if($read == 1){
            //全部数据
            return self::get()->toArray();
        }elseif ($read == 2){
            //未对接数据 read=0
            $result = self::where('read','=','0')->get()->toArray();
           // DB::update('UPDATE b_product SET `read`=:read WHERE `read` = 0',['read' => 2]);
        } elseif ($read == 3){
            //已对接未确认数据
            $result = self::where('read','=','2')->get()->toArray();
        }else{
            //已对接数据
            $result = self::where('read','=','1')->get()->toArray();
        }


        return $result;
    }

    public function read_product(){
        DB::update('UPDATE b_user SET `read`=:read WHERE `read` = 0',['read' => 1]);
    }


    /**
     * 根据手机号或者真实姓名，时间查询
     * @return [type] [description]
     */
    public function search($args)
    {

        $result = self::where('id','>', 0);
        
        if(isset($args['search']) && !empty($args['search'])){
            $result =$result->where('truename', '=', $args['search'])->orwhere('mobile', '=', $args['search']);
        }

        if(isset($args['start']) && !empty($args['start'])){
            
            $args['start'] = strtotime($args['start']);
            $result =$result->where('register_time', '>=', $args['start']);

        }
        if(isset($args['end']) && !empty($args['end'])){
            $args['end'] = strtotime($args['end']);            
            $result =$result->where('register_time', '<=', $args['end']);
        }
        //->orderBy('last_login_time', 'desc')  搜索加的这句话
        $result = $result->orderBy('register_time', 'desc')
                    ->paginate($this->page);
                    
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);

        }
        return $result;
    }

    public function getList(){
        $arr =  self::getAll()->toArray();
        foreach ($arr as &$val){
            $val['default'] = false;
        }
        return $arr;

    }

    /**
     * 根据手机号或者真实姓名
     * @return [type] [description]
     */
    public function search_info($args){

        $search = trim($args['search']);
        $search =  isset($search) ?$search : '';
        $ids = isset($args['ids']) ? $args['ids'] : '';

        $data = array();
        if(!empty($ids)){
            $ids = explode(',',$ids);
            foreach ($ids as $key=>$val){
                $data[] = self::find($val)->toArray();
            }
            foreach ($data as &$val){
                $val['default'] = true;
            }
        }

        if(!empty($search)){
            $result = self::where('truename','=', $search)->get()->toArray();

            if(!empty($result)){
                if($result){
                    $result[0]['default'] = false;
                    return $this->array_merge($data,$result[0],$ids);
                }
            }
            $result = self::where('mobile','=', $search)->get()->toArray();
            if($result){
                $result[0]['default'] = false;
                return $this->array_merge($data,$result[0],$ids);
            }
            return $this->array_merge($data,array(),$ids);


        }else{
            if(!empty($ids)){
                $result = $data;
            }else{
                $result = self::get()->toArray();
                foreach ($result as &$val){
                    $val['default'] = false;
                }
            }
            return $result;

        }

    }

    /**
     *
     * @param   $arr1 [description]
     * @param   $arr2 [description]
     * @return array
     */
    public function array_merge($arr1,$arr2,$ids=array())
    {
        $arr = array();
        $num = count($arr1);
        if(empty($arr1) && empty($arr2)){
           return array();
        }elseif(empty($arr1) && !empty($arr2)){
            return $arr2;
        }elseif(!empty($arr1) && empty($arr2)){
            return $arr1;
        }else{
            if(in_array($arr2['id'],$ids)){
                return $arr1;
            }
            for($i=0;$i<$num+1;$i++){
                if($i==0){
                    $arr[$i] = $arr2;
                }else{
                    $arr[$i] = $arr1[$i-1];
                }
            }
            return $arr;
        }
    }

    /**
     * 根据id查询
     * @param  [type] $mobile [description]
     * @return [type]         [description]
     */
    public function getMsgById($id)
    {
        $result = self::find($id);
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * 根据手机号码查询
     * @param  [type] $mobile [description]
     * @return [type]         [description]
     */
    public function getMsgByMobile($mobile)
    {
        $result = self::where("mobile", "=", $mobile)
                    ->first();
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * 提现成功，删除冻结资金
     * @return [type] [description]
     */
    public function withdrawDone($id, $amount)
    {
        $object = self::find($id);
        if (empty($object)) {
            return false;
        }

        if ($object->freeze_account - $amount < 0) {
            return false;
        }
        
        $object->freeze_account = $object->freeze_account - $amount;
        return $object->save();
    }

    /**
     * 取消提现
     * @param  [type] $id     [description]
     * @param  [type] $amount [description]
     * @return [type]         [description]
     */
    public function cancelWithdraw($id, $amount)
    {
        $object = self::find($id);
        if (empty($object)) {
            return false;
        }

        if ($object->freeze_account - $amount < 0) {
            return false;
        }
        
        $object->freeze_account = $object->freeze_account - $amount;
        $object->balance_account = $object->balance_account + $amount;
        return $object->save();
    }

    /**
     * 根据主键更新btc余额
     * @return [type] [description]
     */
    // public function updateBtcBalanceById($id, $amount)
    // {
    //     $object = self::find($id);
    //     if (empty($object)) {
    //         return false;
    //     }

    //     $object->btc_account = $object->btc_account + $amount;
    //     return $object->save();
    // }

    /**
     * 提币成功，删除冻结资金
     * @return [type] [description]
     */
    // public function withdrawBtcDone($id, $amount)
    // {
    //     $object = self::find($id);
    //     if (empty($object)) {
    //         return false;
    //     }

    //     if ($object->freeze_btc_withdraw_account - $amount < 0) {
    //         return false;
    //     }
        
    //     $object->freeze_btc_withdraw_account = $object->freeze_btc_withdraw_account - $amount;
    //     return $object->save();
    // }

    /**
     * 取消打币
     * @return [type] [description]
     */
    // public function cancelWithdrawBtc($id, $amount)
    // {
    //     $object = self::find($id);
    //     if (empty($object)) {
    //         return false;
    //     }

    //     if ($object->freeze_btc_withdraw_account - $amount < 0) {
    //         return false;
    //     }
        
    //     $object->freeze_btc_withdraw_account = $object->freeze_btc_withdraw_account - $amount;
    //     $object->btc_account = $object->btc_account + $amount;
    //     return $object->save();
    // }

    /**
     * 登陆禁止，解禁
     * @param  [type] $user_id [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function forbidLogin($user_id, $status)
    {
        $result = self::find($user_id);
        $result->forbid_login = $status;
        return $result->save();
    }

    /**
     * 提现禁止，解禁
     * @param  [type] $user_id [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function forbidWithdraw($user_id, $status)
    {
        $result = self::find($user_id);
        $result->forbid_withdraw = $status;
        return $result->save();
    }

    /**
     * 交易禁止，解禁
     * @param  [type] $user_id [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function forbidTrade($user_id, $status)
    {
        $result = self::find($user_id);
        $result->forbid_trade = $status;
        return $result->save();
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

     /**
     * 根据id获取数据，跳转详情页
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::with('bind_address')
                      ->find($id);
        if (!empty($result)) {
            $result = $result->toArray();
        }
        //初始化model
        $user_racharge_model = new AccountCapitalRachargeLog();
        //定义一个变量，接受充值总和
        $result['racharge'] = $user_racharge_model->getSum($id);
        return $result;

    }





/********************************************************前端接口************************************************************************************/
    //检验账户是否存在
    public function checkMobile($args){
        $res = self::where('mobile','=',$args['mobile'])

                    ->first();
        return $res;
      
    }

    //注册保存用户
    public function saveData($args)
    {
        DB::beginTransaction();
        try{

            // 新增
            $class_name = get_class();
            $object = new $class_name;

            $ip = $_SERVER["REMOTE_ADDR"];
        
            $object->mobile = $args['mobile'];
            $object->password = my_password_hash($args['password']);
            $object->register_ip = $ip;
            $object->register_time = time();
            $object->typeid = 0;
            $object->api_token = str_random(60); 
            $object->valid_time = time();                       
            $res = $object->save();
            if(!$res){
                throw new \Exception('数据保存异常，请重新添加');
            }
            $product_hash_type_object = new ProductHashType();
            $arr = $product_hash_type_object->getAllArr();
            $user_ext_object = new UserExt();
            foreach ($arr as $key => $value) {
                $res1 = $user_ext_object->saveData($value['id'],$object->id);
            }
            DB::commit();
        } catch (\Exception $e){
           
            DB::rollback();//事务回滚
            return false;
            
        }
        $arr = array('user_id'=>$object->id,'token'=>$object->api_token);
        return $arr;
    }


    //登录检查
    public function checkUser($args)
    {
        $flag = self::updateToken($args);
        if($flag){

            $res = self::where('mobile','=',$args['mobile'])
                    ->where('forbid_login' ,'=',0)
                    ->where('password','=',my_password_hash($args['password']))
                    ->first();
            $arr = array();
            if(!empty($res)){   
                $arr = array('user_id'=>$res['id'],'token'=>$res['api_token']);
            }
            return $arr;
        }else{
            return false;
        }
        
       
    }

    //生成token
    public function updateToken($args)
    {

        $token = str_random(60);
        $ip = $_SERVER["REMOTE_ADDR"];
   
        $res = self::where('mobile','=',$args['mobile'])
                    ->where('password','=',my_password_hash($args['password']))
                    ->update(['api_token'=>$token,'valid_time'=>time(),'last_login_time'=>time(),'last_login_ip'=>$ip]);

        return $res;
    }

    /**
    * 根据token获取信息
    * @return [type] [description]
    */
    public function getDataByToken($token)
    {
        $result = self::where('api_token','=',$token)->first();
        if( !empty($result) ){
            $result = $result->toArray();
        }
        return $result;
    }

    /**
    * 根据mobile获取信息
    * @return [type] [description]
    */
    public function getDataByMobile($mobile)
    {
        $result = self::where('mobile','=',$mobile)->first();
        if( !empty($result) ){
            $result = $result->toArray();
        }
        return $result;
    }

    //更新密码
    public function updatePwd($args,$user_id)
    {   
        $res = self::where('id','=',$user_id)
                ->update(['password'=>my_password_hash($args['password'])]);

        return $res;
    }
   


/*****************************************************前端接口**********************************************************/

    /**
     * 存储新增数据
     * @return bool 是否成功
     */
    public function savescore($args,$user_id)
    {
        $args['risk_type'] = urldecode( $args['risk_type']);
        
        $object = self::where('id','=',$user_id)
                    ->first();
        $object->user_risk_score = $args['user_risk_score'];//风险测评分数
        $object->risk_type = $args['risk_type'];//风险类型
        
        $res = $object->save();
        return $res;
    }


      //展示是否测评
    public function getshowmessage($args)
    {

        $result = self::where('id','=',$args['id'])
                      ->select('user_risk_score','risk_type')->first();
       

        if(!empty($result)){
            $result = $result->toArray(); 
        }

     
        return $result;
    }

    /**
     * 认证成功更新用户名和身份证号
     * @return [type] [description]
     */
    public function updateUserInfo($id, $name,$card,$status)
    {
        $result = self::find($id);
        $result->truename = $name;
        $result->idcard = $card;
        $result->user_verified = $status;
        
        return $result->save();
    }


    //根据主键更新用户余额
    public function updateUserBalance($id1,$id2,$balance)
    {
        DB::beginTransaction();
        try{
            // 新增
            $object1 = self::find($id1);
            $object2 = self::find($id2);
            if (empty($object1) || empty($object2)) {
                return false;
            }

            $balance = abs($balance);
            if ($object1->balance_account < $balance ) {
                return false;
            }

            $object1->balance_account = $object1->balance_account - $balance;
            $object2->balance_account = $object2->balance_account + $balance;

            $res1 = $object1->save();
            $res2 = $object2->save();
            if( $res1 && $res2){
                DB::commit();
            }
        } catch (\Exception $e){

            DB::rollback();//事务回滚
            return false;

        }
        return $res1;
    }

    //根据主键更新用户余额
    public function updateUserBalanceOne($id,$balance)
    {
        // 事务处理
        DB::beginTransaction();
        try{
            $object = self::where('id','=',$id)->first();

            if(empty($object)){
                throw new \Exception("Failed");
            }
        
            $object->balance_account = $object->balance_account + $balance ;

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

    //提现冻结账户资金
    public function updateAccount($args)
    {
        // 事务处理
        DB::beginTransaction();
        try{
            $object = self::where('id','=',$args['user_id'])->first();

            if(empty($object)){
                throw new \Exception("Failed");
            }
           
            $object->balance_account = $object->balance_account - $args['amount'];//扣除账户余额
          
            $object->freeze_account = $object->freeze_account + $args['amount'];//加入冻结金额
        
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


    //阿里大屏，展示最近7个月用户注册数
    public function getRegisterNum()
    {
        for ($i=0; $i <=6 ; $i++) { 

            $firstday = date("Y-m-01",time());
            $firstday = date("Y-m-01",strtotime("$firstday -$i month"));
            $lastday = date("Y-m-d",strtotime("$firstday +1 month -1 day"));
            
            $start_time = strtotime($firstday);
            $end_time = strtotime($lastday);
           
            $result = self::where('register_time','>=',$start_time)
                        ->where('register_time','<=',$end_time)               
                        ->count('id');
            $data[] = array('x'=>date('m',$end_time).'月','y'=>$result,'z'=>$result-100);
        }
        //倒序
        $data = array_reverse($data);
       
        return $data;                   
    }
    public function getUserNum()
    {
        $result = self::count('id');
     
        return $result;                   
    }

    //获取注册用户分布图数据 
    public function getUserCountAddr()
    {
        $limit =3000;
        $user_wei_byte_json = base_path().'/bootstrap/cache/user_wei.json';
        if(file_exists($user_wei_byte_json)){
            $income_content = file_get_contents($user_wei_byte_json);
            $btcfans = json_decode($income_content,true);
            $offset = $btcfans['num'];
           
            $result = self::where('register_ip','!=',null)
                            ->offset($offset)
                            ->limit($limit)                            
                            ->get();
            if(empty($result)){
                return 0;
            }
            if(!empty($result)){
                $result = $result->toArray(); 
            }
            $btcfans['num'] = $offset + count($result);
            
            foreach ($result as $key => $value) {
                if($value['register_ip'] != '::1' || $value['register_ip'] != '127.0.0.1'){
                    $addr = getLatLngByIp($value['register_ip']);
                    if($addr != 1){
                        $btcfans['list'][] = $addr['addr'];
                        if(!isset($arr[$addr['addr']])){
                            $arr[$addr['addr']][] = $addr;
                        }
                    }
                }
              
            }
            file_put_contents($user_wei_byte_json, json_encode($btcfans));      
            // $res = array_count_values($arr);
            return 1;    
        }else{
            $page = 0;
            $offset = $page * $limit;
            $result = self::where('register_ip','!=',null)
                            ->offset($offset)
                            ->limit($limit)
                            ->get();
            
            if(!empty($result)){
                $result = $result->toArray(); 
            } 
            $num = count($result);
            $arr = array();
            foreach ($result as $key => $value) {
                $addr = getLatLngByIp($value['register_ip']);
                if($addr != 1){
                    $btcfans[] = $addr['addr'];
                    if(!isset($arr[$addr['addr']])){
                        $arr[$addr['addr']] = $addr;
                    }
                
                }
            }
            $data = array('list'=>$btcfans,'num'=>$num,'addr'=>$arr);
            file_put_contents($user_wei_byte_json, json_encode($data));      
            // $res = array_count_values($arr);
            return 1;    
        }
       
        
    }

    public function getUserId($dep_tel)
    {
        $user = self::where('mobile','=',$dep_tel)
            ->first();
        if($user){
            $user_id = $user->id;
            return $user_id;
        }else{
            return false;
        }
    }

}
