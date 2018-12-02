<?php
/**
 * 获取代币列表的定时任务
 * @author      何明飞
 * @time        2019-05-23
 * @created by  Sublime Text 3
 */

namespace App\Http\Controllers\Crontab;

// define('HOST', "http://192.168.3.87:3335/");
define( 'HOST', 'http://192.168.3.108:3000');
use App\Models\Token;
use DB;
class TokenClass
{


    public function index()
    {
        //token信息
        $token_data = curlGet(HOST.'/option/getContractAddress');  

        $token_object = new Token();
        $filename = base_path().'/storage/logs/lock1.html';
       
        if (file_exists($filename)) {
            echo 1;
        }else{
            $fh  =  fopen ( $filename ,  'a' );
            fclose ( $fh );
            
            DB::beginTransaction();
            try{
                foreach ($token_data as $key => $value) {
                   $token_object->saveData($value);
                }
                unlink ( $filename );
                echo "成功";             
                DB::commit();
            } catch (\Exception $e){
                DB::rollback();//事务回滚
                echo $e->getMessage();
            }
   
        }

       
    }

}