<?php
/**
 *信息披露
 * @time        2017-03-12 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\Industry;

class  PublishClass
{
	private $project_object;

	function __construct(){
		$this->project_object = new Project();
	}

    //信息披露
    public function info(Request $request)
    {
        
        $data = $request->attributes->get('data');


    	$result = $this->project_object->getPublishInfo($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //风险警示
    public function risk(Request $request)
    {
        
        $data = $request->attributes->get('data');

    	$result = $this->project_object->getPublishRisk($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //行业报告
    public function report(Request $request)
    {
        
        $data = $request->attributes->get('data');

        $industry_object = new Industry();
    	$result = $industry_object->getPublishReport($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    
}
