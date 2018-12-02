<?php
/**
 * 行权列表
 * @author      张哲
 * @time        2017-09-14 15:29:50
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Admin\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserExercise;
use App\Models\User;




class ExerciseController extends Controller
{
    protected $exercise_object;

    function __construct()
    {
        $this->user_exercise_object = new UserExercise();
    }

    /**
     * 行权列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function showExerciseList(Request $request)
    {
        $result = $this->user_exercise_object->getAll();

        $data = array(
            'data' => $result,
            );

        return view('admin.stock.show_exercise_list', $data);
    }

   /**
     * 根据期权名称查询
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function search(Request $request)
    {
        $data = $this->user_exercise_object->search($request->input('search'));

        $result = array(
            'data' => $data
            );
        return view('admin.stock.show_exercise_list', $result);
    }



    /**
     * 查看明细
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function showDetails(Request $request)
    {
        $id = $request->input("id");
        $data = array();
      
        $data = $this->user_exercise_object->getList(2,$id);
     
        $result = array(
            "data" => $data
            );

        return view('admin.stock.show_exercisedetail_list', $result);
    }




}






