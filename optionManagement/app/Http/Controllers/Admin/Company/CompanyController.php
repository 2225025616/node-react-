<?php
/**
 * 公司列表
 * @author      mozarlee
 * @time        2017-04-10 15:29:50
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Admin\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\AliOssHelper;
use App\Models\Company;


class CompanyController extends Controller
{
	protected $company_object;

	function __construct()
	{
		$this->company_object = new Company();
	}

    /**
     * 公司列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function showCompanyList(Request $request)
    {
        $result = $this->company_object->getAll();
  
        $data = array(
            'data' => $result,
            );

        return view('admin.company.show_company_list', $data);
    }

    /**
     * 创建、更新
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function showSave(Request $request)
    {
        $id = $request->input("id");
        $data = array();

        if (!empty($id)) {
            // 更新
            $data = $this->company_object->getDataById($id);
        }

        $result = array(
            "data" => $data
            );

        return view('admin.company.save_company', $result);
    }

    /**
     * 保存
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function save(Request $request)
    {
        $ali_oss_helper = new AliOssHelper();
        $id = $request->input("id");
  
        $files = $request->file('business_license_img');

        $url = "";
        if( !empty($files) ){
            $file = array(
                'name' => $files->getClientOriginalName(),
                'realPath' => $files->getRealPath(),
                'entension' => $files->getClientOriginalExtension()
                );

            $url = $ali_oss_helper->uploadFilePublic($file);

            if( empty($url) ){
                // 上传图片失败
                return view('admin.error', ['body_style' => 'error-page', 'message' => '图片【'.$file['name'].'】上传出现错误，请重试']);
            }
        } else if(!empty($request->input('old_image'))) {
            $url = $request->input('old_image');
        }

        $params = $request->all();
        $params["business_license_img"] = $url;
        //格式话数据

        $res = $this->company_object->saveData($params);

        
        if ($res) {

            return redirect('admin/company/category');
        }
        return view('admin.error', ['body_style' => 'error-page', 'message' => '写入失败，请重试']);
    }

  

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request) 
    {
        $id = $request->input('id');
        if (!empty($id)) {
            $res = $this->company_object->deleteProject($id);
            if (!$res) {
                return view('admin.error', ['body_style' => 'error-page', 'message' => '删除失败，请重试']);
            }
        }
        return redirect()->back();
    }
}
