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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Mysql::class, function () {
            require base_path('config.php');

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
