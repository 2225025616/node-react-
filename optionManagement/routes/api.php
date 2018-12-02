<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/get_all_modules', 'Api\ModuleClass@getAllModules');

Route::group(['middleware' => 'user_api_access'], function(){
    /**
     * 登录模块接口
     */
    //发送短信
    Route::post('/send_code','Api\SmsClass@send_code');
    //验证短信
    Route::post('/valid_code','Api\SmsClass@valid_code');
    
    //登录
    Route::post('/login','Api\UserClass@login');
    //注册
    Route::post('/register','Api\UserClass@register');
    //找回密码
    Route::post('/forgitPwd','Api\UserClass@forgitPwd');

    //判断手机号是否存在
    Route::post('/checkMobile','Api\UserClass@checkMobile');
    //用户所有信息
    Route::post('/getAll','Api\UserClass@getAll');
    /**
     * 账户管理
     */
    //实名认证
    Route::post('/user_truename','Api\TrueNameClass@user_truename');
    //展示实名信息
    Route::post('/show_user_truename','Api\TrueNameClass@show_user_truename');
    //银行卡绑定
    Route::post('/BankCard','Api\BankCardClass@bankcard');
    //展示银行卡信息
    Route::post('/show_bankcard','Api\BankCardClass@show_bankcard');
    //地址绑定
    Route::post('/bindAddress','Api\BindAddressClass@bindAddress');
    //展示地址信息
    Route::post('/show_Address','Api\BindAddressClass@show_Address');
    //充值接口
    Route::post('/balance_recharge','Api\UserAccountClass@balanceRecharge');
    //查看保全
    Route::post('/getBaoquan','Api\UserAccountClass@getOrderBaoquan');
    //查看bdc信息
    Route::post('/getBdcMessage','Api\UserAccountClass@getBdcMessage');
    
     /**
     * 密码管理
     */
     //设置交易密码
    Route::post('/tradePassword','Api\TradePasswordClass@tradePassword');
    //是否设置密码
   // Route::post('/judgeSetPassword','Api\TradePasswordClass@judgeSetPassword');
    //修改登录密码
    Route::post('/changeLoginPassword','Api\TradePasswordClass@changeLoginPassword');
    //展示交易密码信息
    Route::post('/judgeSetPassword','Api\TradePasswordClass@show_trade_code');
    
    //资金流水
    Route::post('/userCapital','Api\UserCapitalClass@UserCapital');
    Route::post('/userCapitalList','Api\UserCapitalClass@UserCapitalList');
    
     /**
     * 消息
     */
    //消息列表
    Route::post('/MessageList','Api\MessageClass@MessageList');
    //内容
    Route::post('/Messagecontent','Api\MessageClass@Messagecontent');
    //已读
    Route::post('/isRead','Api\MessageClass@isRead');


    /**
     * 用户期权
    */
    Route::post('/user_stock_list','Api\UserStockClass@getList');
    Route::post('/do_exercise','Api\UserStockClass@doExercise');
   
   
    
});

