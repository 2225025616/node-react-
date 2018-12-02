<?php
/**
 * 消息通知
 * @author      
 * @time        2017-09-24 15:37:17
 * @created by  Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\NotificationHelper;
use App\Models\User;
class Notification extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'notification';

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
    public $timestamps = true;

    /**
     * 模型日期列的存储格式
     *
     * @var string
     */
    // protected $dateFormat = 'U';

    /**
     * 当前model连接数据库名称
     *
     * @var string
     */
    // protected $connection = 'connection-name';

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function notification_class(){
        return $this->hasOne('App\Models\NotificationClass', 'id', 'msg_type_id');
    }

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
        // 处理隐藏字符省略号
        $init_length = 30;
        $dealt_content = rmHtmlTagFromStr($args->msg);
        $length = mb_strlen($dealt_content);
        $args->dealtContent = cutStr(rmHtmlTagFromStr($args->msg), 0, 30);
        if( $length > $init_length ){
            $args->dealtContent .= '...';
        }
        return $args;
    }


    /**
     * 分页获取所有数据
     * @return [type] [description]
     */
    public function getAll($page = 15)
    {
        $result = self::orderBy('is_read', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->with('user')
                    ->with('notification_class')
                    ->paginate($page);
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 分页获取某个用户所有消息信息
     * @return [type] [description]
     */
    public function getDataByUser($user_id, $page = 15)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->orderBy('is_read', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($page);
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 获取某个用户未读消息
     * @return [type] [description]
     */
    public function getNotReadNums($user_id)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->where('is_read', '=', 0)
                    ->count();
        return $result;
    }

    /**
     * 获取某个用户的消息详情
     * @return [type] [description]
     */
    public function getDataById($user_id, $id, $page = 15)
    {
        $result = self::where('user_id', '=', $user_id)
                    ->find($id);
        return $result;
    }

    /**
     * 标记某用户的消息已读
     * @param  [type] $user_id [description]
     * @param  [type] $id      [description]
     * @return [type]          [description]
     */
    public function updateMsgStatus($user_id, $id)
    {
        $object = self::where('user_id', '=', $user_id)
                        ->find($id);
        if( !empty($object) ){
            $object->is_read = 1;
            $object->save();
        }
        return;
    }

    /**
     * 更新某个用户所有消息已读
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function updateAllStatus($user_id)
    {
        $data = array(
            'is_read' => 1
            );
        $res = self::where('user_id', '=', $user_id)->update($data);
        return $res;
    }

    /**
     * 新增单条消息记录
     * @param string $code 信息码
     * @param string $user_id 用户id
     * @param array $args 需要传递到消息当中的参数
     * @return bool 是否成功
     */
    public static function saveData($user_id, $code, $args = array(), $msg_type = 0)
    {
        $data = NotificationHelper::getMsg($code, $args);

        // 新增
        $class_name = get_class();
        $object = new $class_name;
        $time = time();
        $object->created_at = date('Y-m-d H:i:s', $time);

        $object->user_id = $user_id;
        $object->title = $data['title'];
        $object->msg = $data['msg'];

        $object->msg_type = $msg_type;
        $object->is_read = 0;
        $res = $object->save();
        return $res;
    }

    /**
     * 批量新增审核数据
     * @return [type] [description]
     */
    public static function saveDataBatch($user_id_arr, $code, $msg_type = 0)
    {
        $data = NotificationHelper::getMsg($code);

        $insert = array();
        $time = time();
        $datetime = date('Y-m-d H:i:s', $time);
        foreach ($user_id_arr as $key => $value) {
            $insert[] = array(
                'created_at' => $datetime,
                'user_id' => $value,
                'title' => $data['title'],
                'msg' => $data['msg'],
                'msg_type' => $msg_type,
                'is_read' => 0
                );
        }

        $res = self::insert($insert);
        return $res;
    }



/******************************************************前端接口**********************************************************/
     /**
     * 消息列表
     * @return [type] [description]
     */
    public function getMessage($args,$user_id)
    { 
        $limit = 15;
        $offset = ($args['page'] - 1) * $limit;
        $result = self::where('user_id','=',$user_id)
                      ->select('id','title','created_at','is_read')
                      ->orderBy('created_at','desc')
                      ->offset($offset)
                      ->limit($limit)
                      ->get();                      
        if(!empty($result)){
            $result = $this->dealResults($result);
            $result = $result->toArray();
        }     
        $number = self::countMessage($user_id);
        $unread_num = self::countUnRead($user_id);
        $data = array('unread_num'=>$unread_num,'total_num'=>$number,'list'=>$result);
        return $data;
    }

/**
 * 增加信息总条数
 * @Author   张哲
 * @DateTime 2017-10-17
 * @createby SublimeText3
 * @version  1.0
 * @return   [return]
 * @param    [type]       $args [description]
 * @return   [type]             [description]
 */
    public function countMessage($user_id)
    { 
        
        $result = self::where('user_id','=',$user_id)->count('id');
  
        return $result;
    }


   

     /**
     * 根据id查询内容
     * @return [type] [description]
     */
    public function getContent($args)
    {

        $result = self::where('id', '=', $args['message_id'])
                     -> select('id','title','msg','created_at','is_read','user_id')->first();

        if(!empty($result)){
            
            $result = $result->toArray();
            if($result['is_read'] == 0){
                self::where('id', '=', $args['message_id'])
                     -> update(['is_read'=>1]);
            }
         }    

        return $result;

    }


     /**
     * 全部变为已读
     * @return [type] [description]
     */
    public function getchangRead($args,$user_id)
    {

        $result = self::where('user_id', '=', $user_id)
                     -> where('is_read', '=', $args['is_read'])
                     -> update(['is_read'=>1]);
        return $result;

    }

    /**
     * 批量新增消息
     * @return [type] [description]
     */
    public static function addData($params)
    {
        $insert = array();
        $time = time();
        $datetime = date('Y-m-d H:i:s', $time);
        foreach ($params['user_ids'] as $key => $value) {
            $insert[] = array(
                'created_at' => $datetime,
                'user_id' => $value,
                'title' => $params['title'],
                'msg' => $params['msg_content'],
                'msg_type_id' =>$params['product_hash_type'],
                'is_read' => 0
            );
        }
        $res = self::insert($insert);
        return $res;
    }

    //统计未读数量
    public function countUnRead($user_id)
    {

        $result = self::where('user_id', '=', $user_id)
                     -> where('is_read', '=', 0)
                     ->count('id');
        return $result;

    }


}
