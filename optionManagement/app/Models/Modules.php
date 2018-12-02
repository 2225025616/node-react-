<?php
/**
 * 授权资源项目
 * 
 * @author        mozarlee
 * @time          2017-02-23 14:34:35
 * @created by    Sublime Text 3
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * 主键
     * 
     * @var string
     */
    protected $primaryKey = 'module_id';

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
    private $page = 2;

    /**
     * 查询子操作
     * @return [type] [description]
     */
    public function children(){
        return $this->hasMany('App\Models\Modules', 'parent_id', 'module_id')
                    ->select('module_id', 'module_name', 'parent_id', 'module_code')
                    ->orderBy('sort_id', 'desc');
    }

    /**
     * 查询父操作
     * @return [type] [description]
     */
    public function parents(){
        return $this->hasOne('App\Models\Modules', 'module_id', 'parent_id')
                    ->orderBy('sort_id', 'desc');
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

        $children = array();
        if( !empty($args['children']) ){
            foreach ($args['children'] as $k => $val) {
                $children[$k] = array(
                    'id' => $val['module_id'],
                    'name' => $val['module_name'],
                    'parentId' => $val['parent_id'],
                    'module_code' => $val['module_code']
                    );
            }
        }

        $result = array(
            'id' => $args['module_id'],
            'name' => $args['module_name'],
            'parentId' => $args['parent_id'],
            'module_code' => $args['module_code'],
            'children' => $children,
            );
        return $result;
    }

    /**
     * 处理数据格式，由子操作查询到父操作（子操作一维，父操作二维），将父操作提取到一维
     * @return [type] [description]
     */
    private function dealChildWithParent($args){
        $parents_arr = array();

        foreach ($args as $key => $value) {
            $parent_id = $value['parent_id'];

            if( !empty($value['parents']) ){
                if( !isset($parents_arr[$parent_id]) ){
                    $parents_arr[$parent_id] = $value['parents'];
                }

                unset($value['parents']);
                $parents_arr[$parent_id]['child'][] = $value;
            }
        }
        return $parents_arr;
    }
    
/************************************************************************/

    /**
     * 获取所有数据
     * @return [type] [description]
     */
    public function getAll()
    {
        $result = self::where('parent_id', '<', 0)
                        ->orderBy('sort_id', 'desc')
                        ->with('children')
                        ->get()
                        ->toArray();
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 根据module_code查询数据
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function getDataByCode($args){
        $result = self::whereIn('module_code', $args['module_code'])
                    ->orderBy('sort_id', 'desc')
                    ->orderBy('parent_id', 'asc')
                    ->with('parents')
                    ->get()
                    ->toArray();

        if( !empty($result) ){
            $result = $this->dealChildWithParent($result);
        }
        return $result;
    }

    /**
     * 根据module_code查询数据, 获取已勾选的数据
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function getDataByCode2($args)
    {
        $result = self::whereIn('module_code', $args['module_code'])
                    ->orderBy('sort_id', 'desc')
                    ->orderBy('parent_id', 'asc')
                    ->get()
                    ->toArray();
        if( !empty($result) ){
            $result = $this->dealResults($result);
        }
        return $result;
    }

    /**
     * 查询
     * @return [type] [description]
     */
    public function searchData()
    {}

}
