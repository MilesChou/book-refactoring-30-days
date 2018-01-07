<?php

namespace App\Providers;

use App\Shop\Mysql;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Smarty;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // 定義管理員帳號密碼
        defined('ADMIN_USER') or define('ADMIN_USER', env('ADMIN_USER', 'shopcart'));
        defined('ADMIN_PASS') or define('ADMIN_PASS', env('ADMIN_PASS', md5('000000')));

        // 除錯模式
        defined('DEBUG_MODE') or define('DEBUG_MODE', env('APP_DEBUG'));

        // 定義資料庫常數
        defined('DB_CHARSET') or define('DB_CHARSET', config('database.connections.mysql.charset'));
        defined('DB_HOST') or define('DB_HOST', config('database.connections.mysql.host'));
        defined('DB_USER') or define('DB_USER', config('database.connections.mysql.username'));
        defined('DB_PASS') or define('DB_PASS', config('database.connections.mysql.password'));
        defined('DB_NAME') or define('DB_NAME', config('database.connections.mysql.database'));

        // 定義商品一頁幾個項目
        defined('PER_PAGE') or define('PER_PAGE', 5);

        // 定義後台首頁排行項目數量
        defined('PER_TOP_LIST') or define('PER_TOP_LIST', 5);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Mysql::class, function () {
            return new Mysql(env('APP_DEBUG', false));
        });

        $this->app->singleton(Smarty::class, function () {
            $instance = new Smarty;
            $instance->template_dir = base_path('/templates/');
            $instance->compile_dir = base_path('/templates/compile/');
            $instance->config_dir = base_path('/templates/configs/');
            $instance->cache_dir = base_path('/templates/cache/');
            $instance->caching = false;
            $instance->auto_literal = false;
            $instance->left_delimiter = '<%';
            $instance->right_delimiter = '%>';

            $instance->assign('config', [
                'debug' => DEBUG_MODE,
                'per_page' => PER_PAGE,
                'per_top_list' => PER_TOP_LIST
            ]);

            return $instance;
        });
    }
}
