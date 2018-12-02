<?php
/**
 *短信
 * @time        2017-09-26 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Helpers\ErrorHelper;
use App\Helpers\SmsHelper;
use App\Helpers\BuildSmsCodeHelper;

use App\Models\SmsLimit;
use Log;

class  SmsClass
{
	private $user_object;

	function __construct(){
		$this->sms_limit_object = new SmsLimit();
	}

    //发送验证码
    public function send_code(Request $request)
    {
        
        $data = $request->attributes->get('data');

        //生成验证码
        $build_sms_code_helper = new BuildSmsCodeHelper();
        $res = $build_sms_code_helper->newCode();
        //存入数据库
        $sms_flag = $this->sms_limit_object->saveData($data,$res['code']);
        //发送验证码
        $sms_helper = new SmsHelper();
        if ($sms_flag) {
            //文件锁
            $file_lock = "storage/vcode_lock";
            if(file_exists($file_lock)) {
                die($file_lock."文件存在");
            }else {
                file_put_contents($file_lock, Date("Y-m-d H:i:s", time()));
            }
            $result = $sms_helper->sendSMS($data['mobile'],$res['content']);
            if(!is_null(json_decode($result))){
                
                $output = json_decode($result,true);
                if(isset($output['code'])  && $output['code'] == '0'){
                    unlink($file_lock);
                    Log::info("短信发送成功",['time'=>time(),'mobile'=>$data['mobile'],'code'=>$res['code']]);                    
                    return response()->json( ErrorHelper::getErrorMsg('1000', true) );
                }else{
                    unlink($file_lock);
                    Log::info("短信发送失败",['time'=>time(),'mobile'=>$data['mobile'],'code'=>$res['code']]);                    
                    return response()->json( ErrorHelper::getErrorMsg('2000', $output['errorMsg']) );
                }
            }else{
                unlink($file_lock);
                Log::info("短信发送失败",['time'=>time(),'mobile'=>$data['mobile'],'code'=>$res['code']]);                       
                return response()->json( ErrorHelper::getErrorMsg('2000', '发送失败') );                
            }
        }else{
            Log::info("短信发送失败",['time'=>time(),'mobile'=>$data['mobile'],'code'=>$res['code']]);             
            return response()->json( ErrorHelper::getErrorMsg('2000', '发送失败') );                            
        }
  
    }

    //验证短信
    public function valid_code(Request $request)
    {
        $data = $request->attributes->get('data');
        //生成验证码
        $build_sms_code_helper = new BuildSmsCodeHelper();

        $res = $build_sms_code_helper->valid_code($data);

        if($res){
            
            return response()->json( ErrorHelper::getErrorMsg('1000', $res) );                        
        }else {
            return response()->json( ErrorHelper::getErrorMsg('2000', '验证失败') );                        
            
        }
    }
   
}
