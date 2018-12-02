<?php
/**
 *前台首页
 * @time        2017-03-12 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Models\Project;

class  HomeClass
{
	private $project_object;

	function __construct(){
		$this->project_object = new Project();
	}

    //首页ico指数
    public function ico_num(Request $request)
    {
        
        $data = $request->attributes->get('data');


    	$result = $this->project_object->getIcoList();
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //热点评级
    public function hot_list(Request $request)
    {
        
        $data = $request->attributes->get('data');


        $result = $this->project_object->getHotScore();
        
        foreach ($result as &$value) {
           $value['ico_start_time'] = date('Y/m/d',strtotime($value['ico_start_time']));
           $value['ico_end_time'] = date('Y/m/d',strtotime($value['ico_end_time']));
           
        }
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }
}
