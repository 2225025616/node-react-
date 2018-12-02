<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', 'TestController@index');


/**
 * 其他路由
 * @author      mozarlee
 * @time        2017-03-24 08:55:15
 * @created by  Sublime Text 3
 */


/**
 * 后台管理路由设置
 * @author      
 * @time        2017-08-31 09:10:40
 * @created by  Sublime Text 2
 */

// 登录
Route::get('/', 'Admin\LoginController@login');

Route::get('/admin/login', 'Admin\LoginController@login');
Route::post('/admin/login', 'Admin\LoginController@doLogin');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function(){
	Route::get('/', 'Admin\IndexController@index');
	Route::get('/home', 'Admin\IndexController@index');


/************************************************************************/
/*  							公司管理							*/
/************************************************************************/

	// 公司列表
	Route::get('/company/category', 'Admin\Company\CompanyController@showCompanyList');
	// 新增、更新
	Route::get('/company/category/save', 'Admin\Company\CompanyController@showSave');
	Route::post('/company/category/save', 'Admin\Company\CompanyController@save');
	// 删除
	Route::get('/company/category/delete', 'Admin\Company\CompanyController@delete');
	

/************************************************************************/
/*  							期权管理							        */
/************************************************************************/
	Route::get('/stock/list','Admin\Stock\StockController@showStockList');


	// 新增、更新
	Route::get('/stock/list/save', 'Admin\Stock\StockController@showSave');
	Route::post('/stock/list/save', 'Admin\Stock\StockController@save');
	// 删除
	Route::get('/stock/list/delete', 'Admin\Stock\StockController@delete');

	//行权分配
	Route::get('/stock/exercise/save', 'Admin\Stock\StockController@showExercise');
	Route::post('/stock/exercise/save', 'Admin\Stock\StockController@doExercise');
	Route::get('/stock/exercise/delete', 'Admin\Stock\StockController@deleteExercise');
	
	//发行
	Route::get('/stock/publish/save', 'Admin\Stock\StockController@showPublish');
	Route::post('/stock/publish/save', 'Admin\Stock\StockController@doPublish');
	Route::get('/stock/publish/delete', 'Admin\Stock\StockController@deletePublish');
	Route::get('/stock/publish/update', 'Admin\Stock\StockController@publish');
	
	//行权列表
	Route::get('/stock/exerciselist', 'Admin\Stock\ExerciseController@showExerciseList');
	Route::get('/stock/exerciselist/search', 'Admin\Stock\ExerciseController@search');
	//查看明细
	Route::get('/stock/exerciselist/details', 'Admin\Stock\ExerciseController@showDetails');

    /**
	 * 人民币充值提现
	 * @author      张哲
	 * @time        2017-09-12
	 * @created by  Sublime Text 3
	 */
	
	// 充值列表
	Route::get('/yen/recharge', 'Admin\Yen\RechargeController@index');
	Route::get('/yen/recharge/search', 'Admin\Yen\RechargeController@search');
	// 提现列表
	Route::get('/yen/withdraw', 'Admin\Yen\WithdrawController@index');
	Route::get('/yen/withdraw/search', 'Admin\Yen\WithdrawController@search');
	// 提现审核
	Route::get('/yen/withdraw/check/{status}', 'Admin\Yen\WithdrawController@check');
	// 打款完成操作
	Route::get('/yen/withdraw/transferdone', 'Admin\Yen\WithdrawController@transferDone');
	// 取消体系那
	Route::get('/yen/withdraw/cancel', 'Admin\Yen\WithdrawController@cancel');



	/**
	 * 人民币对账
	 * @author      张哲
	 * @time        2017-09-13
	 * @created by  Sublime Text 3
	 */
	// 人民币统计
	Route::get('/statistics/yuanstat', 'Admin\Statistics\YenstatController@index');
	Route::get('/statistics/yuanstat/search/{status}', 'Admin\Statistics\YenstatController@search');
	// 用户账户人民币
	Route::get('/statistics/yuancash', 'Admin\Statistics\YencashController@index');
	Route::get('/statistics/yuancash/search', 'Admin\Statistics\YencashController@search');
	

	/**
	 * 股权统计
	 * @author      张哲
	 * @time        2017-09-13
	 * @created by  Sublime Text 3
	 */
	// 股权统计
	Route::get('/stockmanagement/stock', 'Admin\Stockmanagement\StockManagementController@index');
	Route::get('/stockmanagement/stock/search/{status}', 'Admin\Stockmanagement\StockManagementController@search');



    /**
	 * 用户基本信息
	 * @author      张哲
	 * @time        2017-09-12
	 * @created by  Sublime Text 3
	 */

	Route::get('/usermsg', 'Admin\User\UserController@index');
	Route::get('/usermsg/search', 'Admin\User\UserController@search');
	// 禁止、解禁登陆
	Route::get('/usermsg/forbid_login/{status}', 'Admin\User\UserController@forbidLogin');
	// 禁止、解禁提现
	Route::get('/usermsg/forbid_withdraw/{status}', 'Admin\User\UserController@forbidWithdraw');
	// 禁止、解禁交易
	Route::get('/usermsg/forbid_trade/{status}', 'Admin\User\UserController@forbidTrade');
	// 查看资产
	Route::get('/usermsg/asset', 'Admin\User\UserController@asset');
	// 回购
	Route::get('/usermsg/back', 'Admin\User\UserController@back');
	Route::post('/usermsg/back', 'Admin\User\UserController@doBack');
	




	// 用户认证资料
	Route::get('/user_verify', 'Admin\User\UserVerifyController@index');
	// 查询
	Route::get('/user_verify/search', 'Admin\User\UserVerifyController@search');
	// 更新认证状态
	Route::get('/user_verify/updatestatus/{status}', 'Admin\User\UserVerifyController@updateStatus');


	// 用户绑定银行卡
	Route::get('/user_bindcard', 'Admin\User\UserBindcardController@index');
	Route::get('/user_bindcard/search', 'Admin\User\UserBindcardController@search');
	// 验证银行卡
	Route::get('/user_bindcard/check/{status}', 'Admin\User\UserBindcardController@check');

	/**
	 * 账户权限管理
	 * @author      张哲
	 * @time        2017-09-12
	 * @created by  Sublime Text 3
	 */
	// 角色管理
	Route::get('/admin_roles', 'Admin\Account\AccountRolesController@index');
	// 编辑
	Route::get('/admin_roles_update', 'Admin\Account\AccountRolesController@update');
	Route::post('/admin_roles_update', 'Admin\Account\AccountRolesController@doUpdate');
	// 新增
	Route::get('/admin_roles_create', 'Admin\Account\AccountRolesController@create');
	Route::post('/admin_roles_create', 'Admin\Account\AccountRolesController@doCreate');
	// 启用角色
	Route::get('/admin_roles_enable', 'Admin\Account\AccountRolesController@enableRole');
	// 禁用角色
	Route::get('/admin_roles_disable', 'Admin\Account\AccountRolesController@disableRole');

	// 账号管理
	Route::get('/admin_account', 'Admin\Account\AccountController@index');
	// 编辑
	Route::get('/admin_account_update', 'Admin\Account\AccountController@update');
	Route::post('/admin_account_update', 'Admin\Account\AccountController@doUpdate');
	// 新增
	Route::get('/admin_account_create', 'Admin\Account\AccountController@create');
	Route::post('/admin_account_create', 'Admin\Account\AccountController@doCreate');
	// 启用
	Route::get('/admin_account_enable', 'Admin\Account\AccountController@enableRole');
	// 禁用
	Route::get('/admin_account_disable', 'Admin\Account\AccountController@disableRole');


});
// 自定义ueditor后端配置
Route::get('/upload', 'Upload\UploadImgController@mainGet');
Route::post('/upload', 'Upload\UploadImgController@mainPost');
Route::get('/get_token', 'Crontab\TokenClass@index');


