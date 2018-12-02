<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Storage;
use File;

use App\Helpers\AliOssHelper;
class UploadImgController extends Controller
{

	/**
	 * 上传文件 get
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function mainGet(Request $request)
	{
		$config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents('public/vendor/ueditor/php/config.json')), true);

		$action = $_GET['action'];
		switch ($action) {
		    case 'config':
		        $result = json_encode($config);
		        break;
		    /* 上传图片 */
		    case 'uploadimage':
		    	$result = $this->uploadImage($request);
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
	}

	/**
	 * post
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function mainPost(Request $request){
		$config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents('public/vendor/ueditor/php/config.json')), true);

		$action = $request->input('action');
		switch ($action) {
		    case 'config':
		        $result = json_encode($config);
		        break;

		    /* 上传图片 */
		    case 'uploadimage':
		    	$result = $this->uploadImage($request);
		        break;
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    // case 'uploadfile':
		    //     $result = include('public/vendor/ueditor/php/action_upload.php');
		    //     break;
		    /* 列出图片 */
		    case 'listimage':
		        $result = include('public/vendor/ueditor/php/action_list.php');
		        break;
		    /* 列出文件 */
		    case 'listfile':
		        $result = include('public/vendor/ueditor/php/action_list.php');
		        break;
		    /* 抓取远程文件 */
		    case 'catchimage':
		        $result = include('public/vendor/ueditor/php/action_crawler.php');
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
	}


	/**
	 * 自定义上传图片
	 * @return [type] [description]
	 */
	public function uploadImage($request){
		$files = $request->file('upfile');

		if( !empty($files) ){
			// 本地
			// $time = time();
			// $path = 'public/storage/upload/';
			// $imageName = date('Ymd', $time).$time.'.'.$file->getClientOriginalExtension();
		 //    $res = $file->move(base_path().'/'.$path,$imageName);
		 //    $url = $path.'/'.$imageName;

		 //    if( empty($res) ){
		 //    	return null;
		 //    }
		 	
		 	// 阿里oss
	        $ali_oss_helper = new AliOssHelper();
	        $file = array(
                'name' => $files->getClientOriginalName(),
                'realPath' => $files->getRealPath(),
                'entension' => $files->getClientOriginalExtension()
                );

            $url = $ali_oss_helper->uploadFilePublic($file);
		    
		    if( !empty($url) ){
		    	$result = json_encode(array(
		            'state' => 'SUCCESS',
		            'url' => $url,
		            'title' => $file['name'],
		            'original' => $file['name'],
		        ));
		        return $result;
		    }
		}
		return null;
	}

}
