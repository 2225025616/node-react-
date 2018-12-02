<?php
    /**
     * 绑定地址接口
     * @Author   张哲
     * @DateTime 2017-10-09
     * @createby SublimeText3
     * @version  1.0
     * @param    Request      $request [description]
     * @return   [type]                [description]
     */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Helpers\BuildSmsCodeHelper;
use App\Models\AccountBtcBindAddress;

class  BindAddressClass
{
    private $bindclass_object;

    function __construct(){
        $this->bindclass_object = new AccountBtcBindAddress();
        $this->sms_code_helper = new BuildSmsCodeHelper();
    }

    //绑定地址
    public function bindAddress(Request $request)
    {
        
        $data = $request->attributes->get('data');
        $user = $request->attributes->get('user');

        //验证验证码
        $res = $this->sms_code_helper->valid_code($data); 
        if(!$res){
            return response()->json( ErrorHelper::getErrorMsg('2000', '验证码错误') );                        
        }
        //检验地址是否绑定
        $res1 = $this->bindclass_object->getDataByAddr($data);
        if(!empty($res1)){
            return response()->json( ErrorHelper::getErrorMsg('2000', '地址已存在') );
        }
        $result = $this->bindclass_object->saveData($data,$user['id']);
       
        return response()->json( ErrorHelper::getErrorMsg('1000', $result) );
    }

    //展示地址
    public function show_Address(Request $request)
    {
        
        $data = $request->attributes->get('data');
        $user = $request->attributes->get('user');

        $result = $this->bindclass_object->getshowaddress($user);
       
        return response()->json( ErrorHelper::getErrorMsg('1000', $result) );
    }


    
   
}
