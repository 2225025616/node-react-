<?php
/**
* 二维码生成辅助类
* @author      mozarlee
* @time        2017-03-14 09:30:00
* @created by  Sublime Text 3
*/
namespace App\Helpers;

use QrCode;
class QrCodeHelper
{
	
	/**
	 * 生成二维码
	 * @return [type] [description]
	 */
	public static function build($data, $size)
	{
		$file_path = 'qrcodes/'.time().rand(0, 100).'.png';
		QrCode::format('png')->size($size)->backgroundColor(255, 255, 255)->margin(0)->generate($data, public_path($file_path));
		return $file_path;
	}

    /**
     * 生成二维码并上传到阿里云
     * @return [type] [description]
     */
	public static function upload($qrcode_url){
        $path = self::build($qrcode_url, 200);

        $file_path =  public_path($path);
        // 把图片上传到阿里云
        $now_time = time();
        $imageName = date('Ymd', $now_time).$now_time.'.png';

        $file_info = array(
            'name' => $imageName,
            'realPath' => $file_path,
            'entension' => 'png'
        );

        // 上传到阿里云
        $ali_oss_helper = new AliOssHelper();
        $url = $ali_oss_helper->uploadFilePublic($file_info);

        unlink($file_path);
        return $url;
    }
}