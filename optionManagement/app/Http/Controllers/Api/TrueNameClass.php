<?php
    /**
     * 前台实名
     * @Author   张哲
     * @DateTime 2017-09-28
     * @createby SublimeText3
     * @version  1.0
     * @return   [return]
     * @param    Request      $request [description]
     * @return   [type]                [description]
     */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\BuildSmsCodeHelper;

use App\Helpers\ErrorHelper;
use App\Models\UserTruenameValid;

class  TrueNameClass
{
    private $trunname_object;

    function __construct(){
        $this->trunname_object = new UserTruenameValid();
        $this->sms_code_helper = new BuildSmsCodeHelper();
    }

    
    //收集实名信息
    public function user_truename(Request $request)
    {   
        $data = $request->attributes->get('data');
        $user = $request->attributes->get('user');
        $data['user_id'] = $user['id'];
        //验证验证码
        $res = $this->sms_code_helper->valid_code($data);

        if(!$res){   
            return response()->json( ErrorHelper::getErrorMsg('2000', '验证码错误') );                        
        }

        $result = $this->trunname_object->saveData($data);
        
        return response()->json( ErrorHelper::getErrorMsg('1000', $result) );
    }


    
        //实名信息
    public function show_user_truename(Request $request)
    {
        
        $data = $request->attributes->get('data');
        $user = $request->attributes->get('user'); 
           
        $result = $this->trunname_object->getshowmessage($user);
        if($result){
            return response()->json( ErrorHelper::getErrorMsg('1000', $result) );            
        }

        return response()->json( ErrorHelper::getErrorMsg('2000', '认证不一致') );            
       
    }
}
