<?php
/**
 * 保存图片到数据库
 * @author      mozarlee
 * @time        2017-03-11 16:18:32
 * @created by  Sublime Text 3
 */
namespace App\Helpers;

use App\Models\Files;
class FilesHelper
{
	private $files_object;

	function __construct(){
		$this->files_object = new Files();
	}

	/**
	 * 保存文件到数据库
	 * @return [type] [description]
	 */
	public function saveFile($url)
	{
		$res = $this->files_object->saveFile($url);
		return $res;
	}

}
