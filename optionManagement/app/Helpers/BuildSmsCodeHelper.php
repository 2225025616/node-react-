<?php
/**
* 生成短信验证码，验证短信验证码
*/
namespace App\Helpers;
use App\Models\SmsTpl;
use App\Models\SmsLimit;
class BuildSmsCodeHelper
{
		
	/**
	 * 生成通用验证码
	 * @return [type] [description]
	 */
	public static function newCode($replacement = array())
	{
		
        $code = rand(100000,999999);
		$sms_tpl_object = new SmsTpl();
		$res = $sms_tpl_object->getDataByUniqueid();
		$replacement = array('recode'=>$code);
		$content = $res['content'];
	    foreach ($replacement as $search => $replace) {
	        $content = str_replace("{" . $search . "}", $replace, $content);
		}
		$content = "【算力网】".$content;
		$data = array("content"=>$content,"code"=>$code);
       	return $data;
	}


	/**
	 * 生成通用验证码
	 * @return [type] [description]
	 */
	public static function messageCode($uniqueid = 'reset_password')
	{
		
		$sms_tpl_object = new SmsTpl();
		$res = $sms_tpl_object->getDataByUniqueid($uniqueid);
	
		$content = $res['content'];
		$content = "【算力网】".$content;
		$data = array("content"=>$content);
       	return $data;
	}
	//验证验证码是否正确

	public static function valid_code($args)
	{
		$sms_limit_object = new SmsLimit();

		$res = $sms_limit_object->getDataBycode($args);
		return $res;
	}

}