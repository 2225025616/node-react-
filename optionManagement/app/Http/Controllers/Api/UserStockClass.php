<?php

/**
 * 期权列表
 * 
 * @author		hmf
 * @time        2018-05-24
 * @created by	Sublime Text 3
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\UserStock;
use App\Models\Token;
use App\Models\AccountBtcBindAddress;

use App\Helpers\ErrorHelper;
use App\Helpers\ValidHelper;


class UserStockClass
{
	private $user_stock_object;

	function __construct()
	{
		$this->user_stock_object = new UserStock();
	}

	/**
	 * 用户期权列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function getList(Request $request)
    {
    	$data = $request->attributes->get('data');
        $user = $request->attributes->get('user');

        $res = $this->user_stock_object->getListByUserid($user['id']);
        foreach ($res as $key => $value) {
        	$result[] = array(
    				'id'=>$value['id'],
    				'stock_name'=>$value['stock_info']['stock_name'],
    				'end_time'=>$value['end_time'],
    				'stock_amount'=>$value['stock_amount'],
    				'created_at'=>$value['created_at'],
    				'status'=>$value['status'],


        		);
        }
        
    	return response()->json( ErrorHelper::getErrorMsg('1000',$result) );
    }


    /**
	 * 用户行权
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function doExercise(Request $request)
    {
    	$data = $request->attributes->get('data');
        $user = $request->attributes->get('user');

        if (!isset($data['user_stock_id']) && empty($data['user_stock_id'])) {
	    	return response()->json( ErrorHelper::getErrorMsg('2000','参数缺失') );	
        }
       
       	//判断用户是否实名
       	if($user['user_verified'] !== 1){
            return response()->json( ErrorHelper::getErrorMsg('200003') );
        }
       	//判断用户是否绑定地址
       	$user_bind_address_model = new AccountBtcBindAddress();
        $user_address = $user_bind_address_model->getAddress($data,$user['id']);
       	if(empty($user_address)){
            return response()->json( ErrorHelper::getErrorMsg('400004') );
        }
       	//获取期权信息
       	$res = $this->user_stock_object->getDataByid($data['user_stock_id'])->toArray();
       	//获取token信息
       	$token_object = new Token();
       	$token_info = $token_object->getDataByid($res['stock_info']['token_id']);
       
       	//调取接口发币
       	$contractAddress = $token_info['address'];
       	$address = '["'.$user_address['address'].'"]';
       	$amount = $res['stock_amount'];

       	$url = 'http://192.168.3.108:3000/option/distributeOption?contractAddress='.$contractAddress.'&addresses='.$address.'&amount='.$amount;
       	
       	$res = curlGet($url);
       	//改变user_stock状态
       	if ($res['status']  === 0) {//成功
       		$data['status'] = 2;
       		$this->user_stock_object->editStatus($data);
	    	return response()->json( ErrorHelper::getErrorMsg('1000','操作成功') );

       	} else{
	    	return response()->json( ErrorHelper::getErrorMsg('2000','操作失败') );
       	}
    }

}
