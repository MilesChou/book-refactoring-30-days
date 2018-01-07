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
defined('DEBUG_MODE') or define('DEBUG_MODE', env('APP_DEBUG'));
if (DEBUG_MODE) {
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 'Off');
}

/**
 * 定義資料庫常數
 */
defined('DB_CHARSET') or define('DB_CHARSET', config('database.connections.mysql.charset'));
defined('DB_HOST') or define('DB_HOST', config('database.connections.mysql.host'));
defined('DB_USER') or define('DB_USER', config('database.connections.mysql.username'));
defined('DB_PASS') or define('DB_PASS', config('database.connections.mysql.password'));
defined('DB_NAME') or define('DB_NAME', config('database.connections.mysql.database'));

/**
 * 定義商品一頁幾個項目
 */
defined('PER_PAGE') or define('PER_PAGE', 5);

/**
 * 定義後台首頁排行項目數量
 */
defined('PER_TOP_LIST') or define('PER_TOP_LIST', 5);
