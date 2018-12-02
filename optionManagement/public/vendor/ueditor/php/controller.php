<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

/**
* 自定义上传图片实现类
*/
class MyUpload
{
    
    /**
     * curl请求上传图片
     * @param  [type] $url  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function curlPost($url, $data){
        $post = array(
            'data' => $data,
            );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt ( $curl, CURLOPT_POST, 1);
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $post );

        $response = curl_exec($ch);
        curl_close($ch);

        var_dump($response);
        die();
        return json_decode($response, true);
    }

    /**
     * 接收请求参数
     * @param  [type] $url  [description]
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public function doUploadImage($url, $file){
        $res = $this->curlPost($url, $file);
        var_dump($res);
        die();
    }
}

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];
switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/uploadImg';
        $my_upload = new MyUpload();
        $res = $my_upload->doUploadImage($url, $_FILES);

        $result = json_encode(array(
            'state' => 'SUCCESS',
            'url' => "http://ytmpp4p5h8jichui-10007535.file.myqcloud.com/673b9dbddf0d95110147.?imageView/2/w/100/h/100/q/100/format/png",
            'title' => 'title',
            'original' => 'original',
        ));
        break;
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    // case 'uploadfile':
    //     $result = include("action_upload.php");
    //     break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}