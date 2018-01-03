<?php

require_once __DIR__ . '/workaround.php';

	/**
	 * 定義管理員帳號密碼
	 */
	$user = 'shopcart';
	$pass = md5('000000');
	/**
	 * 除錯模式開啟與關閉
	 * 使用布林
	 */
	define('DEBUG_MODE', True);
	if (DEBUG_MODE){
		ini_set('display_errors', 'On');
	} else {
		ini_set('display_errors', 'Off');
	}

	/**
	 * 定義其他目錄
	 */
	define('CLASS_PATH', base_path('/class/'));

//	$include_path[] = get_include_path();
//	$include_path[] = CLASS_PATH;
//	set_include_path(join(PATH_SEPARATOR, $include_path));
	
//	function __autoload($class_name) {
//		$class = str_replace('_','/',$class_name);
//		require_once $class . '.php';
//	}
	/**
	 * 定義資料庫常數
	 */
	defined('DB_CHARSET') or define('DB_CHARSET', config('database.connections.mysql.charset'));
	defined('DB_HOST') or define('DB_HOST', config('database.connections.mysql.host'));
	defined('DB_USER') or define('DB_USER', config('database.connections.mysql.username'));
	defined('DB_PASS') or define('DB_PASS', config('database.connections.mysql.password'));
	defined('DB_NAME') or define('DB_NAME', config('database.connections.mysql.database'));

	/**
	 * 設定樣版
	 */
	include(CLASS_PATH . "Smarty/Smarty.class.php");
	$tpl = new Smarty;
	$tpl->template_dir = base_path('/templates/');
	$tpl->compile_dir = base_path('/templates/compile/');
	$tpl->config_dir = base_path('/templates/configs/');
	$tpl->cache_dir = base_path('/templates/cache/');
	$tpl->caching = False;
	$tpl->auto_literal = False;
	$tpl->left_delimiter = '<%';
	$tpl->right_delimiter = '%>';

	/**
	 * 引用資料庫
	 */
	require_once CLASS_PATH . "mysql.class.php";

	/**
	 * 定義商品一頁幾個項目
	 */
	define('PER_PAGE', 5);
	/**
	 * 定義後台首頁排行項目數量
	 */
	define('PER_TOP_LIST', 5);
	/**
	 * 輸出設定至樣版config變數
	 */
	$tpl->assign('config', array(
		'debug' => DEBUG_MODE,
		'per_page' => PER_PAGE,
		'per_top_list' => PER_TOP_LIST));
