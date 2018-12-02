<?php
exit('非法操作');
$conn = mysql_connect('rm-bp1283j831yzo056t.mysql.rds.aliyuncs.com', 'slb', 'slb_basic_2016');
mysql_query('set names utf8');
mysql_select_db('baoquan_blockchain_rz');

 // $conn = mysql_connect('127.0.0.1', 'root', 'root');
 // mysql_query('set names utf8');
 // mysql_select_db('baoquan_blockchain_rz');

$bq_score_data_hash = 'bq_score_data_hash';
$bq_company_hash = 'bq_company_hash';
$bq_certificate_hash = 'bq_certificate_hash';
$bq_account_certify_hash = 'bq_account_certify_hash';
$bq_score_data_validate_record = 'bq_score_data_validate_record';
$count = 1;

$htime = null;
while (true) {
    if($count){
        $sql = "SELECT * FROM `bq_blockchain` ORDER BY `id` DESC limit 1";
        $res = mysql_fetch_array(mysql_query($sql));
        $prehash = $res['hash'];
        $htime = $res['timestamp'];
        unset($sql);
        unset($res);
    }

    if($htime == null){
        $htime = 1;
    }

    $sql[] = "SELECT * FROM `{$bq_score_data_hash}` WHERE `timestamp`>$htime";
    $sql[] = "SELECT * FROM `{$bq_certificate_hash}` WHERE `timestamp`>$htime";
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    $sql[] = "SELECT * FROM `{$bq_company_hash}` WHERE  `timestamp`>$htime";
    $sql[] = "SELECT * FROM `{$bq_account_certify_hash}` WHERE  `timestamp`>$htime";
    $sql[] = "SELECT * FROM `{$bq_score_data_validate_record}` WHERE  `timestamp`>$htime";
    $hash = array();
    foreach ($sql as $k => $v) { // 运行sql语句并读取数据
        $result = mysql_query($v);
        while($row = mysql_fetch_array($result)){
            $hash[] = $row['hash'];
        }
    }

    $time = time();

    $datahash = $hash;
    $size = count($hash);
    $hash = implode('',$hash);
    if(isset($prehash)){
        $hash = $hash . $prehash;
    }

    $hash .= $time . rand(0, 1000);
    $hash = hash('sha256', $hash);
    if(!isset($prehash)){
        $prehash = 0;
    }

    $sql = "SELECT * FROM `bq_blockchain` ORDER BY `id` DESC limit 1";
    $height = mysql_fetch_array(mysql_query($sql))['height'];
    if(empty($height)){
        $height = 0;
    }
    $height++;
    $sql = "INSERT INTO `bq_blockchain` values(null,'{$hash}',{$time},'{$size}','{$height}','{$prehash}')";
    $res=mysql_query($sql);
    $block_id = mysql_fetch_row(mysql_query("select last_insert_id()"))[0];
    foreach ($datahash as $n=>$m){
        $sql = "INSERT INTO `bq_blockchain_data` values(null,{$block_id},'{$m}',{$time})";
        mysql_query($sql);
    }
    unset($hash);
    $count++;
    break;
}