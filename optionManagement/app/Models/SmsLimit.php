<?php
/**
 * 短信模板表
 * 
 * @author                  mozarlee
 * @time                    2017-02-23 10:57:08
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLimit extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'sms_limit';

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
     * 当前model连接数据库名称
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
       
        return $args;
    }
    
    /**
     * 根据id获取数据
     * @return [type] [description]
     */
    public function getDataById($id)
    {
        $result = self::where('id','=',$id)->first();
        if( !empty($result) ){
            $result = $result->toArray(); 
        }
        return $result;
    }

    /**
     * 新增、更新
     * @return bool 是否成功
     */
    public function saveData($args,$recode)
    {

        $object = null;
        if( isset($args['id']) && !empty($args['id']) ){
            // 更新
            $object = self::find($args['id']);
            if( empty($object) ){
                return false;
            }
        }else{
            // 新增
            $class_name = get_class();
            $object = new $class_name;
        }
  
        $ip = $_SERVER["REMOTE_ADDR"];
        $object->ip = $ip;
        $object->mobile = $args['mobile'];
        $object->recode = $recode;
       
        $object->send_time = time();
        $res = $object->save();
       
        return $res;
       
        
    }

    /**
     * 验证
     * @return [type] [description]
     */
    public function getDataBycode($args)
    {
        $flag = self::updateSmsCode($args);
        if($flag){
            $result = self::select('id','valid_code')
                            ->where('mobile','=',$args['mobile'])
                            ->where('recode','=',$args['code'])
                            ->where('send_time','>=',time()-60*5)
                            ->orderBy('send_time','desc')                        
                            ->first();
            if( !empty($result) ){
                $result = $result->toArray();
            
            }
            return $result;
        }else{
            return false;
        }
        
    }

    //更新
    public function updateSmsCode($args)
    {
        $valid_code = str_random(60);
        $res = self::where('mobile','=',$args['mobile'])
                        ->where('recode','=',$args['code'])
                        ->where('valid_status','=',0)
                        ->where('send_time','>=',time()-60*5)                        
                        ->update(['valid_status'=>1,'valid_code'=>$valid_code,'valid_time'=>time()]); 
        return $res;
    }

    //根据valid_code获取数据
    
}
