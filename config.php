<?php
	session_start();
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
	 * 定義工作目錄(PATH)與虛擬目錄(URL)
	 */
	//$_ROOT_PATH_ADDTION = (PHP_OS == 'Linux')?'/':Null;
	define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . '/');
	define('ROOT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/');
	/**
	 * 定義其他目錄
	 */
	define('CLASS_PATH', ROOT_PATH . 'class/');

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
	define('DB_TYPE', 'mysql');
	define('DB_CHARSET', 'utf8');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'shopcart');
	define('DB_PASS', 'shopcart');
	define('DB_NAME', 'shopcart');

	/**
	 * 設定樣版
	 */
	include(CLASS_PATH . "Smarty/Smarty.class.php");
	$tpl = new Smarty;
	$tpl->template_dir = ROOT_PATH . '/templates/';
	$tpl->compile_dir = ROOT_PATH . '/templates/compile/';
	$tpl->config_dir = ROOT_PATH . '/templates/configs/';
	$tpl->cache_dir = ROOT_PATH . '/templates/cache/';
	$tpl->caching = False;
	$tpl->auto_literal = False;
	$tpl->left_delimiter = '<%';
	$tpl->right_delimiter = '%>';

	/**
	 * 引用資料庫
	 */
	include(CLASS_PATH . "mysql.class.php");

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
		
	
?>