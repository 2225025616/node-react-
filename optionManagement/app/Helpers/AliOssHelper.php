<?php

/**
 * 阿里oss
 * @author      mozarlee
 * @time        2017-03-03 11:18:27
 * @created by  Sublime Text 3
 */
namespace App\Helpers;

use OSS\OssClient;
use OSS\OssException;
use OSS\Http\RequestCore;
use OSS\Http\ResponseCore;

class AliOssHelper
{
    private $access_id; // OSS获得的AccessKeyId>
    private $access_secret; // OSS获得的AccessKeySecret

    private $end_point = 'oss-cn-hangzhou.aliyuncs.com'; // 选定的OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com> 私有
    private $bucket = 'chengxin'; // 私有

    private $public_end_point = 'oss-cn-shanghai.aliyuncs.com'; // 选定的OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com> 公共读
    private $public_bucket = 'chengxin-public';// 公共读

    function __construct()
    {
        $this->access_id = config('app.ali_access_key_id');
        $this->access_secret = config('app.ali_access_key_secret');

        // 初始化私有
        $this->oss_client = new OssClient($this->access_id, $this->access_secret, $this->end_point);
        // 初始化公共读
        $this->public_oss_client = new OssClient($this->access_id, $this->access_secret, $this->public_end_point);
    }

    /**
     * 上传图片
     * @return [type] [description]
     */
    public function upload($file)
    {
        $url = null;
        if( !empty( $file ) && $file->isValid() ){
            $upload = array(
                'name' => $file->getClientOriginalName(),
                'realPath' => $file->getRealPath(),
                'entension' => $file->getClientOriginalExtension()
                );
            $url = $this->uploadFilePublic($upload);
        }
        return $url;
    }

    /**
     * 上传公共读
     * @return [type] [description]
     */
    public function uploadFilePublic($file){
        // 上传公共读
        $img_name = time().mt_rand(1,20000).md5(time()).md5(sha1(time().$file['name'].'.'.$file['entension'])).'.'.$file['entension'];
        $object = 'chengxin_rz/'.$img_name;

        try{
            // 本地的example.jpg上传到指定$public_bucket, 命名为$object
            $res = $this->public_oss_client->uploadFile($this->public_bucket, $object, $file['realPath']);
            if( isset($res['info']['http_code']) && $res['info']['http_code'] == 200 ){
                return $res['info']['url'];
            }
        } catch(OssException $e) {
            // printf(__FUNCTION__ . ": FAILED\n");
            // printf($e->getMessage() . "\n");
            return null;
        }
    }

    /**
     * 生成GetObject的签名url,主要用于私有权限下的读访问控制
     *
     * @param $ossClient OssClient OssClient实例
     * @param $bucket string 存储空间名称
     * @return null
     */
    public function getSignedUrlForGettingObject($object)
    {
        $ossClient = $this->oss_client; //私有图片
        $bucket = $this->bucket;
        $timeout = 3600;
        try {
            $signedUrl = $ossClient->signUrl($bucket, $object, $timeout);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $signedUrl;
        // print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");
        // /**
        //  * 可以类似的代码来访问签名的URL，也可以输入到浏览器中去访问
        //  */
        // $request = new RequestCore($signedUrl);
        // $request->set_method('GET');
        // $request->add_header('Content-Type', '');
        // $request->send_request();
        // $res = new ResponseCore($request->get_response_header(), $request->get_response_body(), $request->get_response_code());
        // if ($res->isOK()) {
        //     print(__FUNCTION__ . ": OK" . "\n");
        // } else {
        //     print(__FUNCTION__ . ": FAILED" . "\n");
        // };
    }


    /**
     * 上传私有
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public function uploadFilePrivate($file){
        // 上传私有
        $img_name = time().mt_rand(1,20000).md5($file['name']).'.png';
        $object = 'chengxin_rz/'.$img_name;

        try{
            // 本地的example.jpg上传到指定$public_bucket, 命名为$object
            $res = $this->oss_client->uploadFile($this->bucket, $object, $file['realPath']);
            if( isset($res['info']['http_code']) && $res['info']['http_code'] == 200 ){
                return array(
                    'url' => $res['info']['url'],
                    'image_name' => $object
                    );
            }
        } catch(OssException $e) {
            // printf(__FUNCTION__ . ": FAILED\n");
            // printf($e->getMessage() . "\n");
            return null;
        }
    }



}
