<?php
/**
 * 短信模板表
 * 
 * @author                  
 * @time                    2017-09-27 10:57:08
 * @created by              Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTpl extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'sms_tpl';

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
    public function getDataByUniqueid($uniqueid = 'general')
    {
        $result = self::where('uniqueid','=',$uniqueid)->first();
        if( !empty($result) ){
            $result = $result->toArray(); 
        }
        return $result;
    }
}
