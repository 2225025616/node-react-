<?php
/**
 *项目评级
 * @time        2017-03-12 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Models\Project;

class  ScoreClass
{
	private $project_object;

	function __construct(){
		$this->project_object = new Project();
	}

    //所有评级项目列表
    public function project_list(Request $request)
    {
        
        $data = $request->attributes->get('data');

    	$result = $this->project_object->getList($data);
        foreach ($result as &$value) {
           $value['ico_start_time'] = date('Y/m/d',strtotime($value['ico_start_time']));
           $value['ico_end_time'] = date('Y/m/d',strtotime($value['ico_end_time']));
           
        }
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //搜索
    public function search(Request $request)
    {
        
        $data = $request->attributes->get('data');

        $data['key'] = urldecode($data['key']);

    	$result = $this->project_object->searchData($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }
}
