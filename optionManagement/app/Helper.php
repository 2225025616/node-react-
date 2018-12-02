<?php
use Mail as SendMail;

    /**
     * curl请求GET
     * @return [type] [description]
     */
    function curlGet($url){
        $time = time();
        // $LC_Sign = md5($time.config('app.Tencent_appKey'));
        // $LC_Sign = md5($time.config('app.Tencent_masterKey'));

        // $header = array(
        //     'X-LC-Id: ' . config('app.Tencent_appId'),
        //     'X-LC-Sign: ' . $LC_Sign.','.$time.',master',

        //     // 'X-LC-Sign: ' . $LC_Sign.','.$time,
        //     // 'Content-Type: application/json',
        //     );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    /**
     * curl请求POST
     * @return [type] [description]
     */
    function curlPost($url, $post){
        // $time = time();
        // $LC_Sign = md5($time.config('app.Tencent_appKey'));
        // $LC_Sign = md5($time.config('app.Tencent_masterKey'));

        // $header = array(
        //     'X-LC-Id: ' . config('app.Tencent_appId'),
        //     'X-LC-Sign: ' . $LC_Sign.','.$time.',master',

        //     // 'X-LC-Sign: ' . $LC_Sign.','.$time,
        //     // 'Content-Type: application/json',
        //     );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt ( $ch, CURLOPT_POST, 1);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    //密码加密
    function my_password_hash($password)
    {
        // $step1 = md5($password);
        $step1 = $password;        
        $step2 = substr($step1, 0, 10);
        return md5($step1 . $step2);
    }

    /**
     * 发送邮件
     * @return [type] [description]
     */
    function sendMail($toUser, $text){
        SendMail::raw($text, function ($message) use ($toUser) {
            $message ->to($toUser)->subject('系统通知');
        });
    }

    /**
     * 获得访客浏览器类型
     */
    function getBrowser(){
        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            $br = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i',$br)) {    
              $br = 'MSIE';
            }elseif (preg_match('/Firefox/i',$br)) {
                $br = 'Firefox';
            }elseif (preg_match('/Chrome/i',$br)) {
                $br = 'Chrome';
            }elseif (preg_match('/Safari/i',$br)) {
                $br = 'Safari';
            }elseif (preg_match('/Opera/i',$br)) {
                $br = 'Opera';
            }else {
               $br = 'Other';
            }
            return $br;
        }else{
            return "获取浏览器信息失败！";
        }
    }

    /**
     * 获得访客浏览器语言
     */
    function getLang(){
        if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
            $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $lang = substr($lang,0,5);
            if(preg_match("/zh-cn/i",$lang)){
                $lang = "简体中文";
            }elseif(preg_match("/zh/i",$lang)){
                $lang = "繁体中文";
            }else{
                $lang = "English";
            }
            return $lang;
        }else{
            return "获取浏览器语言失败！";
        }
    }

    /**
     * 获取访客操作系统
     * @return [type] [description]
     */
    function getOs(){
        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            $OS = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i',$OS)) {
                $OS = 'Windows';
            }elseif (preg_match('/mac/i',$OS)) {
                $OS = 'MAC';
            }elseif (preg_match('/linux/i',$OS)) {
                $OS = 'Linux';
            }elseif (preg_match('/unix/i',$OS)) {
                $OS = 'Unix';
            }elseif (preg_match('/bsd/i',$OS)) {
                $OS = 'BSD';
            }else {
                $OS = 'Other';
            }
            return $OS;  
        }else{
            return "获取访客操作系统信息失败！";
        }   
    }

    /**
     * 获得本地真实IP
     * @return [type] [description]
     */
    function getOnlineip() {
        $mip = file_get_contents("http://www.ip138.com/ip2city.asp");
        if($mip){
            preg_match("/\[.*\]/",$mip,$sip);
            $p = array("/\[/","/\]/");
            return preg_replace($p,"",$sip[0]);
        }else{
            return '';
        }
    }

    /**
     * 获得访客真实ip
     * @return [type] [description]
     */
    function getRealIp(){
        $realip = '';
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
                $realip=$_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip=$_SERVER['REMOTE_ADDR'];
            }
        }else{
            if(getenv('HTTP_X_FORWARDED_FOR')){
                $realip=getenv('HTTP_X_FORWARDED_FOR');
            }else if(getenv('HTTP_CLIENT_IP')){
                $realip=getenv('HTTP_CLIENT_IP');
            }else{
                $realip=getenv('REMOTE_ADDR');
            }
        }

        if( $realip == '127.0.0.1' || $realip == '::1' ){
            $realip = getOnlineip();
        }
        return $realip;
    }

    /**
     * 根据ip获得访客所在地地名
     * @param string $ip [description]
     */
    function getAddressByIp($ip=''){
        if(empty($ip)){
            $ip = getRealIp();
            return $ip;
        }

        $ipadd = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=".$ip);//根据新浪api接口获取

        if($ipadd){
            $charset = iconv("gbk","utf-8",$ipadd);   
            preg_match_all("/[\x{4e00}-\x{9fa5}]+/u",$charset,$ipadds);

            if( count($ipadds) == 1 ){
                $str = '';
                foreach ($ipadds[0] as $key => $value) {
                    $str .= $value;
                }
                return $str;
            }

            return json_encode( $ipadds );
        }else{
            return "addree is none";
        }
    } 

    /** 
     * 截取指定长度字符串
     * @return [type] [description]
     */
    function cutStr($str, $start = 0, $length = 90, $encode = 'utf-8'){
        return mb_substr($str, $start, $length, $encode);
    }

    /**
     * 格式化html5 输入框类型为date-local类型的日期加时间
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    function dealDateLocalFormat($time){
        return str_replace('T', ' ', $time);
    }

    /**
     * 格式化html5 输入框类型为date-local类型的日期
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    function dealDateLocalFormat2($time){
        $time = str_replace('T', ' ', $time) . ':00';
        $time = date('Y-m-d', strtotime($time));
        return $time;
    }

    /**
     * 格式化日期
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    function dealDateLocalFormat3($time){
        $time = date('Y-m-d', strtotime($time));
        return $time;
    }

    function dealDateLocalFormat4($time){
        $time = str_replace('T', ' ', $time). ':00';
        return ($time);
    }

    /**
     * 处理日期格式为highcharts图表日期
     * @param  [type] $time [description]
     * @return [type]       [description]
     */
    function dealTime($time){
        $time = str_replace('-', ',', $time);
        return $time;
    }

    /**
     * 从html字符串中去掉html标签
     * @return [type] [description]
     */
    function rmHtmlTagFromStr($str){
        $str = strip_tags($str);
        $str = str_replace('&nbsp;', '', $str);
        return $str;
    }

    /**
     * 处理字符串中间N位隐藏 默认手机隐藏格式
     * @param  [type] $str [description]
     * @return [type]         [description]
     */
    function dealStrHidden($str, $start = 3, $length = 4){
        $tab = '';
        for ($i = 0; $i < $length ; $i++) { 
            $tab .= '*';
        }

        $str = substr_replace($str, $tab, $start, $length);
        return $str;
    }

    /**
     * 生成唯一编号
     * @return [type] [description]
     */
    function createUnique()
    {
        $no = 'BQ'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        return $no;
    }

    /**
     * 生成发财鱼发送短信交易号
     * @return [type] [description]
     */
    function createSmsOrder()
    {
        $no = date('Ymdhis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 6);
        return $no;
    }

    /**
     * 以sha256方式生成hash值
     * @return [type] [description]
     */
    function hash256($string)
    {
        $hash = hash('sha256', $string);
        return $hash;
    }

    /**
     * 从阿里OSS 文件url中获取到文件后缀名
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    function getFileExtFromUrl($url)
    {
        // 判断是否是文本内容
        $text_arr = explode('http://chengxin-public.oss-cn-shanghai.aliyuncs.com', $url);
        if( count($text_arr) <= 1 ){
            return 'text';
        }

        $str = str_replace('http://chengxin-public.oss-cn-shanghai.aliyuncs.com', '', $url);
        $arr = explode('.', $str);
        if( count($arr) > 1 ){
            return $arr[1];
        }
        return 'img';
    }

    /**
     * 验证手机号码格式
     * @param  [type] $mobile [description]
     * @return [type]         [description]
     */
    function checkMobile($mobile)
    {
        return preg_match('/^0?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/', $mobile);
    }

    /**
     * 验证身份证号码
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function checkIdCard($id){
        return preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $id);
    }

    /**
     * 计算中文字符串长度
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    function utf8_strlen($string = null) {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        return count($match[0]);
    }

    function strToHex($string)//字符串转十六进制
    { 
        $hex="";
        for ($i = 0;$i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
            $hex = strtoupper($hex);
        }   
        return $hex;
    }


?>