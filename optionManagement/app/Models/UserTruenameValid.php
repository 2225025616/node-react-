<?php
/**
 * 用户实名认证
 * @author      张哲
 * @time        2017-09-29 14:48:33
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTruenameValid extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_truename_valid_log';

    /**
     * 主键
     * 
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    // protected $dateFormat = 'U';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    // protected $connection = 'connection-name';

    // 分页每页显示数据条数
    private $page = 15;

    /**
     * 处理数据格式 多条
     * @return [type] [description]
     */
    private function dealResults($args)
    {
        foreach ($args as $key => $value) {
                 $args[$key] = $this->dealResult($value);
        }
        return $args;
    }

    /**
     * 处理数据格式 单条
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    private function dealResult($args)
    {
        $args->add_time = date('Y-m-d H:i:s', $args->add_time);
        $args->res_time = date('Y-m-d H:i:s', $args->res_time);
        $args->idcard = dealStrHidden($args->idcard, 6, 11);
        $args->old_idcard = $args->idcard;
        return $args;
    }
    
/************************************************************************/

    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::orderBy('id', 'desc')
                    ->paginate($this->page);
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据真实姓名查询
     * @return [type] [description]
     */
    public function search($args)
    {
        $result = self::where('id','>', 0);
        
        if(isset($args['search']) && !empty($args['search'])){
            $result =$result->where('truename', '=', $args['search']);
        }
        if(isset($args['status']) && !empty($args['status'])){
            $result->where('status', '=', $args['status']);

        }

        if(isset($args['start']) && !empty($args['start'])){
            
            $args['start'] = strtotime($args['start']);
            $result =$result->where('add_time', '>=', $args['start']);

        }
        if(isset($args['end']) && !empty($args['end'])){
            $args['end'] = strtotime($args['end']);            
            $result =$result->where('add_time', '<=', $args['end']);
        }
        
        $result = $result->orderBy('add_time', 'desc')
            ->paginate($this->page);
            
        // 处理返回结果数据格式
        if (!empty($result)) {
            // 处理返回结果数据格式
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 修改认证状态
     * @param  [type] $user_id [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function updateStatus($user_id, $status)
    {
        $result = self::find($user_id);
        $result->status = $status;
        return $result->save();
    }

    /**
     * 获取实名认证数据
     * @param  [type] $status [description]
     * @return [type]          [description]
     */
    public function getStatusAll()
    {
        $result = self::whereIn('status',[0])->select('id','user_id','truename','idcard')->get();
        return $result;
    }

    /**
     * 更新接口返回信息
     * @param  [type] $user_id [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function updateApiStatus($id, $status,$ret_code,$ret_msg)
    {
        $result = self::find($id);
        $result->status = $status;
        $result->ret_code = $ret_code;
        $result->ret_msg = $ret_msg;
        return $result->save();
    }


    public function getDataById($args)
    {

        $result = self::where('id','=',$args['id'])
                    ->select('truename','idcard','status','user_id')->first();

        if(!empty($result)){
            $result = $result->toArray(); 
        }

        return $result;
    }
/******************************************************前端接口**********************************************************/

    /**
     * 存储新增数据
     * @return bool 是否成功
     */
    public function saveData($args)
    {
        $args['truename'] = urldecode( $args['truename']);
        $object = self::where('user_id','=',$args['user_id'])->first();
        if(empty($object)){
            // 新增
            $class_name = get_class();
            $object = new $class_name;
        }
    
        $object->user_id = $args['user_id'];
        $object->truename = $args['truename'];
        $object->idcard = $args['idcard'];
        $object->add_time =time(); 
        $object->status = 0; 
        
        $res = $object->save();
        return $res;
    }

    //展示信息
    
    public function getshowmessage($args)
    {

        $result = self::where('user_id','=',$args['id'])
                    ->where('status','=',1)
                    ->select('truename','idcard','status')->first();

        if(!empty($result)){
            $result = $result->toArray();
            $result['idcard'] = dealStrHidden($result['idcard'], 3, 11);
        }

        return $result;
    }


}
