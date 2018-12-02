<?php
/**
 * 公司列表
 * @author      mozarlee
 * @time        2017-04-10 15:29:50
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Admin\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Excel;
use App\Helpers\AliOssHelper;
use App\Models\Stock;
use App\Models\Company;
use App\Models\ExerciseConfig;
use App\Models\UserStock;
use App\Models\Token;




class StockController extends Controller
{
	protected $company_object;

	function __construct()
	{
		$this->stock_object = new Stock();
	}

	/**
	 * 查看期权列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function showStockList(Request $request)
    {
        
    	$result = $this->stock_object->getAll();
        $data = array(
            'data' => $result,
            );

        return view('admin.stock.show_stock_list', $data);
        
    }

    /**
     * 创建、更新项目
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function showSave(Request $request)
    {
        $id = $request->input("id");
        $data = array();
        $company = array();
        
        $company_object = new Company();
        $token_object = new Token();

        if (!empty($id)) {
            // 更新
            $data = $this->stock_object->getDataById($id);
        }
        $company = $company_object->getList(2);
        $token_name = $token_object->getList(0);
        $result = array(
            "data" => $data,
            "company"=>$company,
            "token_name"=>$token_name
            );

        return view('admin.stock.save_stock', $result);
    }

    /**
     * 保存项目
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function save(Request $request)
    {
        $site = session('blockchain_rz_admin_info');
        $ali_oss_helper = new AliOssHelper();
        $id = $request->input("id");
  
        $files1 = $request->file('file1');
        $url1 = "";
        if( !empty($files1) ){
            $file1 = array(
                'name' => $files1->getClientOriginalName(),
                'realPath' => $files1->getRealPath(),
                'entension' => $files1->getClientOriginalExtension()
                );

            //取文件后缀
            $type = strtolower(substr(strrchr("sfsl@#$*^*&ldfls;pprt[f.pdf","."),1)); 
            $namestr = substr($file1['name'],strlen($file1['name'])- 3,3);

            if($type == $namestr){ 
                $url1 = $ali_oss_helper->uploadFilePublic($file1);
                if( empty($url1) ){

                    // 上传文件失败
                    return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file1['name'].'】上传出现错误，请重试']);
                }
            }else{ 
                return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file1['name'].'】必须上传PDF格式文件，请重试']);
            } 

        } else if(!empty($request->input('old_file1'))) {
                $url1 = $request->input('old_file1');
        } 


        $files2 = $request->file('file1');
        $url2 = "";
        if( !empty($files2) ){
            $file2 = array(
                'name' => $files2->getClientOriginalName(),
                'realPath' => $files2->getRealPath(),
                'entension' => $files2->getClientOriginalExtension()
                );

            //取文件后缀
            $type = strtolower(substr(strrchr("sfsl@#$*^*&ldfls;pprt[f.pdf","."),1)); 
            $namestr = substr($file2['name'],strlen($file2['name'])- 3,3);

            if($type == $namestr){ 
                $url2 = $ali_oss_helper->uploadFilePublic($file2);
                if( empty($url2) ){

                    // 上传文件失败
                    return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file2['name'].'】上传出现错误，请重试']);
                }
            }else{ 
                return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file2['name'].'】必须上传PDF格式文件，请重试']);
            } 

        } else if(!empty($request->input('old_file2'))) {
                $url2 = $request->input('old_file2');
        } 

        $files3 = $request->file('file1');
        $url3 = "";
        if( !empty($files3) ){
            $file3 = array(
                'name' => $files3->getClientOriginalName(),
                'realPath' => $files3->getRealPath(),
                'entension' => $files3->getClientOriginalExtension()
                );

            //取文件后缀
            $type = strtolower(substr(strrchr("sfsl@#$*^*&ldfls;pprt[f.pdf","."),1)); 
            $namestr = substr($file3['name'],strlen($file3['name'])- 3,3);

            if($type == $namestr){ 
                $url3 = $ali_oss_helper->uploadFilePublic($file3);
                if( empty($url3) ){

                    // 上传文件失败
                    return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file3['name'].'】上传出现错误，请重试']);
                }
            }else{ 
                return view('admin.error', ['body_style' => 'error-page', 'message' => '文件【'.$file3['name'].'】必须上传PDF格式文件，请重试']);
            } 

        } else if(!empty($request->input('old_file3'))) {
                $url3 = $request->input('old_file3');
        } 


        $params = $request->all();
        $params["file1"] = $url1;
        $params["file2"] = $url2;
        $params["file3"] = $url3;
        $params['publisher_id'] = $site['user_id'];
        //格式话数据
        $token_object = new Token();
        $token_list = $token_object->getDataById($params['token_id']);
        $params['token_name'] = $token_list['name'];
        $stock_id = $this->stock_object->saveData($params);

        if ($stock_id) {
            //更新token表为已绑定，期权
            
            $token_object->blind_stock($params['token_id'],$stock_id);

            return redirect('admin/stock/list');
        }
    }

  
    /**
    * 行权分配
    * @param  Request $request [description]
    * @return [type]           [description]
    */
    public function showExercise(Request $request)
    {
        $exercise_config_object = new ExerciseConfig();
        $id = $request->input("id");
        $data = array();

        if (!empty($id)) {
            // 更新
            $data = $exercise_config_object->getExerciseList($id);
        }
        $result = array(
            "data" => $data
            );

        return view('admin.stock.save_exercise_config', $result);
    }

     /**
     * 保存分配
     * @param  Request $request [description]
     * @return [type]           [description]
     */
     public function doExercise(Request $request)
     {
        $params = $request->all();

        //生成评级记录
        $exercise_config_object = new ExerciseConfig();

        $res = $exercise_config_object->saveData($params);
        if ($res) {

            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '写入失败，请重试']);
     }

    /**
    * 发行
    * @param  Request $request [description]
    * @return [type]           [description]
    */
    public function showPublish(Request $request)
    {
        $user_stock_object = new UserStock();
        $id = $request->input("id");
        $data = array();

        if (!empty($id)) {
            // 更新
            $data = $user_stock_object->getPublishList($id);
        }

        $result = array(
            "data" => $data
            );
       
        return view('admin.stock.save_publish', $result);
    }

     /**
     * 保存分配
     * @param  Request $request [description]
     * @return [type]           [description]
     */
     public function doPublish(Request $request)
     {
        
        $params = $request->all();
        $file = $_FILES;  

        $excel_file_path = $file['file']['tmp_name']; 
      
        $res = [];    
        Excel::load($excel_file_path, function($reader) use( &$res ) {    
           $reader = $reader->getSheet(0);    
           $res = $reader->toArray();    
        });

        $user_stock_object = new UserStock();
       
        $result = $user_stock_object->saveData($res,$params['id']);
       
        if ($result) {

            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '写入失败，请重试']);
     }

     //确认发放
     public function publish(Request $request)
     {
        $params = $request->all(); 
        
        $user_stock_object = new UserStock();
        
        $result = $user_stock_object->publish($params );

        $arr = array('id'=>$params['id'],'status'=>2);
        $res = $this->stock_object->updateStatus($arr);
        if ($result&&$res) {

            return redirect()->back();
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '发放失败，请重试']);
     }

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request) 
    {
        $id = $request->input('id');
        $res = array("status"=>3,"id"=>$id);
        if (!empty($id)) {
            $res = $this->stock_object->editInfo($res);
            if (!$res) {
                return view('admin.error', ['body_style' => 'error-page', 'message' => '删除失败，请重试']);
            }
        }
        return redirect()->back();
    }

    //删除行权分配
    public function deleteExercise(Request $request)
    {
        $exercise_id = $request->input('id');
        $exercise_config_object = new ExerciseConfig();

        $res = array("status"=>1,"id"=>$exercise_id);
        if (!empty($exercise_id)) {
            $res = $exercise_config_object->editInfo($res);
            if (!$res) {
                return view('admin.error', ['body_style' => 'error-page', 'message' => '删除失败，请重试']);
            }
        }
        return redirect()->back();
    }
    

}
