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
     * 設定樣版
     */
    $tpl = new Smarty;
    $tpl->template_dir = base_path('/templates/');
    $tpl->compile_dir = base_path('/templates/compile/');
    $tpl->config_dir = base_path('/templates/configs/');
    $tpl->cache_dir = base_path('/templates/cache/');
    $tpl->caching = false;
    $tpl->auto_literal = false;
    $tpl->left_delimiter = '<%';
    $tpl->right_delimiter = '%>';

    /**
     * 定義商品一頁幾個項目
     */
    defined('PER_PAGE') or define('PER_PAGE', 5);
    /**
     * 定義後台首頁排行項目數量
     */
    defined('PER_TOP_LIST') or define('PER_TOP_LIST', 5);
    /**
     * 輸出設定至樣版config變數
     */
    $tpl->assign('config', array(
        'debug' => DEBUG_MODE,
        'per_page' => PER_PAGE,
        'per_top_list' => PER_TOP_LIST));
