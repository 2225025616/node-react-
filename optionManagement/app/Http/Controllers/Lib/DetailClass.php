<?php
/**
 *项目详情
 * @time        2017-03-12 12:26:57
 * @created by  Sublime Text 3
 */
namespace App\Http\Controllers\Lib;

use Illuminate\Http\Request;
use App\Helpers\ErrorHelper;
use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\News;
use App\Models\Comment;

class  DetailClass
{
	private $project_object;

	function __construct(){
		$this->project_object = new Project();
	}

    //项目详情，头部
    public function project_head(Request $request)
    {
        
        $data = $request->attributes->get('data');


    	$result = $this->project_object->getDataHead($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //项目主页
    public function project_home(Request $request)
    {
        
        $data = $request->attributes->get('data');


    	$result = $this->project_object->getProjectHome($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //项目评级
    public function project_score(Request $request)
    {
        
        $data = $request->attributes->get('data');

        $project_rating_object = new ProjectRating();
    	$result = $project_rating_object->getProjectScore($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //项目报道
    public function project_news(Request $request)
    {
        
        $data = $request->attributes->get('data');

        $news_object = new News();
    	$result = $news_object->getProjectNews($data);
        
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //项目评论
    public function project_comment(Request $request)
    {
        
        $data = $request->attributes->get('data');

        $comment_object = new Comment();
    	$result = $comment_object->getProjectComment($data);
        return response()->json( ErrorHelper::getErrorMsg('200', $result) );
    }

    //保存评论
    public function save_comment(Request $request)
    {
        
        $data = $request->attributes->get('data');
        $data['content'] = urldecode($data['content']);
        $data['project_name'] = urldecode($data['project_name']);
        
        $comment_object = new Comment();
    	$result = $comment_object->saveComment($data);
        if($result){
            return response()->json( ErrorHelper::getErrorMsg('200', $result) );
        } else{
            return response()->json( ErrorHelper::getErrorMsg('200001') );
        }
        
    }
}
