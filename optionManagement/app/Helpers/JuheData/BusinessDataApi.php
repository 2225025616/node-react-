<?php
namespace App\Helpers\JuheData;
/**
 * 聚合数据api
 * @author      mozarlee
 * @time        2017-03-21 17:40:46
 * @created by  Sublime Text 3
 */

class BusinessDataApi
{
    private $test_url = 'http://v.juhe.cn/youshu/test';

    private $key = '6568bad599941791f9de7b1b1fbc875d'; // 企业工商数据appkey
    private $query_url = 'http://v.juhe.cn/youshu/query';

    /**
     * 测试接口
     * @return [type] [description]
     */
    public function test($args)
    {
        $url = $this->test_url.'?key='.$this->key.'&name='.urlencode($args);
        $res = curlGet($url);
        if( $res['error_code'] == 0 ){
            return $res['result'];
        }

        if( $res['error_code'] == 219201 ){
            exit("聚合数据平台账户余额不足，请求次数不足，需要充值才可使用。");
        }
        return array();
    }


    /**
     * 根据企业名称
     * @param  [type] $com_name [description]
     * @return [type]           [description]
     */
    public function getDetails($args)
    {
        $args = trim($args);
        // return $this->test($args);
        $url = $this->query_url.'?key='.$this->key.'&name='.urlencode($args);
        $res = curlGet($url);

        $result = array();
        switch ($res['error_code']) {
            case 0:
                $result = $res['result'];
                break;
            case 10012:
                exit("聚合数据平台账户余额不足，请求次数不足，需要充值才可使用。");
                break;
            case 10003:
                exit("请求KEY过期。");
                break;
            case 10011:
                exit('当前IP请求超过限制');
                break;
            case 10020:
                exit('接口维护');
                break;
            case 10021:
                exit('接口停用');
                break;
            case 220502:
                break;
            case 220503:
                exit($res['reason']);
                break;
            case 220504:
                exit('查询的企业名称或工商注册号不能为空');
                break;
            default:
                break;
        }
        
        return $result;
    }
}