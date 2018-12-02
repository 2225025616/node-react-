<?php
/**
 *用户
 * @time        2017-09-26 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Helpers\BuildSmsCodeHelper;
use App\Helpers\ValidHelper;
use Log;
use App\Models\User;
use App\Models\SmsLimit;
use App\Models\UserTruenameValid;
use App\Models\UnionpayBindCard;
use App\Models\AccountBtcBindAddress;
use App\Models\Notification;
use App\Models\UserLoginFailed;
use App\Models\UserLogin;

class  UserClass
{
	private $user_object;
	private $sms_code_helper;

	function __construct(){
        $this->user_object = new User();
        $this->sms_code_helper = new BuildSmsCodeHelper();
        $this->bankcard_object = new UnionpayBindCard();
        $this->trunname_object = new UserTruenameValid();
        $this->bind_address_object = new AccountBtcBindAddress();
        $this->message_object = new Notification();
       
        
	}

    public function login(Request $request)
    {
        $data = $request->attributes->get('data');
        
        //检测账户是否存在
        $result1 = $this->user_object->checkMobile($data);
    
        $temp = $result1->toArray();
        if($temp['forbid_login']){
            Log::info("用户登录失败",['mobile'=>$data['mobile'],'time'=>time(),'msg'=>'用户禁止登录']);            
            return response()->json( ErrorHelper::getErrorMsg('2000', '对不起，您已被禁止登录，请联系管理员！') );
        }
    
        if(empty($result1)){
            Log::info("用户登录失败",['mobile'=>$data['mobile'],'time'=>time(),'msg'=>'暂无此用户']);            
            return response()->json( ErrorHelper::getErrorMsg('2000', '暂无此用户') );
        }
        //判断登录失败次数
        $user_login_failed_object = new UserLoginFailed();
        $faild = $user_login_failed_object->getDataByUserId($result1['id']);
        if(!empty($faild)){
            if($faild['times'] >= 5){
                Log::info("用户登录失败",['user_id'=>$result1['user_id'],'time'=>time(),'msg'=>'账户登录错误次数超过限制']);             
                return response()->json( ErrorHelper::getErrorMsg('2000', '账户登录错误次数超过限制，请24小时后重试！') );
            }
        }
        $result = $this->user_object->checkUser($data);
       
        if($result){
            if($faild['times'] > 0){
                $user_login_failed_object->updateTimes($result1['id']); 
            }
            //记录登录记录
            $user_login_object =  new UserLogin();
            Log::info("用户登录成功",['user_id'=>$result['user_id'],'time'=>time()]);
            $user_login_object->saveData($result1['id']);            
            return response()->json( ErrorHelper::getErrorMsg('1000', $result) );
        }else{
            $user_login_failed_object->saveData($result1['id']);
            Log::info("用户登录失败",['mobile'=>$data['mobile'],'time'=>time(),'msg'=>'账户名或者密码错误']);           
            return response()->json( ErrorHelper::getErrorMsg('2000', '账户名或者密码错误,您还可以尝试'.(4 - $faild['times']).'次，如果失败超过5次，请在24小时后重试！') );
        }
    }

    //注册
    public function register(Request $request)
    {

        $data = $request->attributes->get('data');
        //验证用户是否存在
        $user = $this->user_object->checkMobile($data);
        if(!empty($user)){
            Log::info("用户注册失败",['time'=>time(),'msg'=>'注册失败，用户已存在']);            
            return response()->json( ErrorHelper::getErrorMsg('2000', '注册失败，用户已存在') );
        }
        //两次密码是否一致
        if($data['password'] != $data['password1']){
            Log::info("用户注册失败",['time'=>time(),'msg'=>'注册失败，两次密码不一致']);      
            return response()->json( ErrorHelper::getErrorMsg('2000', '注册失败，两次密码不一致') );
        }
        //验证验证码
        $res = $this->sms_code_helper->valid_code($data);


        if(!$res){
            Log::info("用户注册失败",['time'=>time(),'msg'=>'验证码错误']);            
            return response()->json( ErrorHelper::getErrorMsg('2000', '验证码错误') );
        }

    	$result = $this->user_object->saveData($data);
        if($result){
            Log::info("用户注册成功",['user_id'=>$result['user_id'],'time'=>time()]);                        
            return response()->json( ErrorHelper::getErrorMsg('1000', $result) );
        } else{
            Log::info("用户注册失败",['time'=>time(),'msg'=>'注册失败']);                        
            return response()->json( ErrorHelper::getErrorMsg('2000', '注册失败') );
        }

    }


    //忘记密码
    public function forgitPwd(Request $request)
    {
        $data = $request->attributes->get('data');

        //根据code_id找到数据对比
        $sms_limit_model = new SmsLimit();
        $sms = $sms_limit_model->getDataById($data['code_id']);

        if($data['password'] != $data['password1']){
            Log::info("用户修改密码失败",['time'=>time(),'mobile'=>$data['mobile'],'msg'=>'修改失败，两次密码不一致']);                              
            return response()->json( ErrorHelper::getErrorMsg('2000', '修改失败，两次密码不一致') );
        }

        if($sms['mobile'] == $data['mobile'] && $sms['send_time'] > time()-5*60 && $sms['valid_status'] == 1 && $sms['valid_code'] == $data['valid_code'])
        {
            $user = $this->user_object->getDataByMobile($data['mobile']);
            
            $result = $this->user_object->updatePwd($data,$user['id']);
            if($result){
                Log::info("用户修改密码成功",['time'=>time(),'mobile'=>$data['mobile'],'msg'=>'修改成功']);        
                return response()->json( ErrorHelper::getErrorMsg('1000', '修改成功') );
            }else{
                Log::info("用户修改密码失败",['time'=>time(),'mobile'=>$data['mobile'],'msg'=>'修改失败，密码与原始密码一致']);
                return response()->json( ErrorHelper::getErrorMsg('2002', '修改失败，密码与原始密码一致') );

            }
        } else{
            Log::info("用户修改密码失败",['time'=>time(),'mobile'=>$data['mobile'],'msg'=>'修改失败，无权限修改']);
            return response()->json( ErrorHelper::getErrorMsg('2001', '修改失败，无权限修改') );
        }


    }

   //显示用户信息所有
   public function getAll(Request $request)
   {
       $data = $request->attributes->get('data');
       $user = $request->attributes->get('user');
    //    $risk = $this->user_object->getshowmessage($user); //风险评分
       $true_name = $this->trunname_object->getshowmessage($user);//实名认证
    //    $bank_card = $this->bankcard_object->getshowmessage($user);//绑定银行卡
       $address = $this->bind_address_object->getshowaddress($user);//绑定地址
    //    $unread_num = $this->message_object->countUnRead($user['id']);//消息中心未读数量
    //    $trade_password = ValidHelper::valid_set_trade_password($user);//交易密码
    //    $scode =  $this->s_code_object->getDataUser($user['id']);//是否绑定s码
       $result = array(
               // 'user'=>$user,
            //    'risk'=>$risk,
               'true_name'=>$true_name,
            //    'bank_card'=>$bank_card,
               'address'=>$address,
            //    'unread_num'=>$unread_num,
            //    'trade_password'=>$trade_password,
            //    'scode'=>$scode,
            //    'last_login_time'=>$user['last_login_time'],
            //    'balance'=>$user['balance_account'],                  
           );
       return response()->json( ErrorHelper::getErrorMsg('1000', $result) );

   }


    //验证手机号是否存在
    public function checkMobile(Request $request)
    {
        $data = $request->attributes->get('data');
        //检测账户是否存在
        $result = $this->user_object->checkMobile($data);
        if($result){           
            return response()->json( ErrorHelper::getErrorMsg('2000', '此用户存在') );
        }
        return response()->json( ErrorHelper::getErrorMsg('1000', true) );
    }

}
