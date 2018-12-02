<?php

/**
 * 授权资源数据获取
 * 
 * @author		mozarlee
 * @time          2017-02-23 17:25:34
 * @created by	Sublime Text 3
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Modules;
class ModuleClass
{
	private $modules_object;

	function __construct()
	{
		$this->modules_object = new Modules();
	}

	/**
	 * 获取所有授权资源数据
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function getAllModules(Request $request)
    {
    	$result = $this->modules_object->getAll();
    	return response()->json([
                'error' => 0,
                'msg' => $result,
            ]);
    }

}
