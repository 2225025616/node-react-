<?php
/**
* 验证用户是否实名，是否风险测评，是否绑定银行卡，是否提币地址
*/
namespace App\Helpers;
use App\Models\UserTruenameValid;
use App\Models\UnionpayBindCard;
use App\Models\User;
use App\Models\AccountBtcBindAddress;
use App\Models\TradePassword;
use App\Models\Notification;

class ValidHelper
{
		
	/**
	 * 实名检测
	 * @return [type] [description]
	 */
	public static function valid_truename($args)
	{
        $user_truename_valid_model = new UserTruenameValid();
        $result = $user_truename_valid_model->getshowmessage($args);
        if(!empty($result) && $result['status'] == 1){
            return 1;
        }else{
            return 0;
        }
	}

	/**
	 * 风险测评检测
	 * @return [type] [description]
	 */
	public static function valid_risk_score($args)
	{
        $user_model = new User();
        $result = $user_model->getshowmessage($args);
        if(!empty($result) && $result['user_risk_score'] > 0){
            return 1;
        }else{
            return 0;
        }
    }
    
    /**
	 * 绑定银行卡检测
	 * @return [type] [description]
	 */
	public static function valid_bind_card($args)
	{
		
        $user_bind_card_model = new UnionpayBindCard();
        $result = $user_bind_card_model->getDataByUser($args['id']);
        
        if(!empty($result) && $result['status'] == 2){
            return 1;
        }else{
            return 0;
        }
    }
    
    /**
	 * 绑定提币地址检测
	 * @return [type] [description]
	 */
	public static function valid_bind_address($args,$user_id)
	{
	
        $user_bind_address_model = new AccountBtcBindAddress();
        $result = $user_bind_address_model->getAddress($args,$user_id);
        if(!empty($result)){
            return 1;
        }else{
            return 0;
        }
    }
    
    /**
	 * 是否设置交易密码检测
	 * @return [type] [description]
	 */
	public static function valid_set_trade_password($args)
	{
		
        $user_trade_code_model = new TradePassword();
        $result = $user_trade_code_model->getPassword($args);

        if(!empty($result)){
            return 1;
        }else{
            return 0;
        }
	}

    /**
	* 验证交易密码是否正确
	* @return [type] [description]
	*/
	public static function valid_trade_code($args)
	{
		
        $user_trade_code_model = new TradePassword();
        $result = $user_trade_code_model->checkPassword($args);
 
        if(!empty($result)){
            return 1;
        }else{
            return 0;
        }
	}

    /**
     * 发送消息
     * @return [type] [description]
     */
    public static function sms_notification($data)
    {
        $NotificationObj = new Notification();
        $buy_notification = array(
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'created_at' => date('Y-m-d H:i:s',time()),
            'msg_type_id' => $data['msg_type_id'],
            'msg' => $data['msg']
        );

        //系统消息记录
        $NotificationObj->insert($buy_notification);
    }

}