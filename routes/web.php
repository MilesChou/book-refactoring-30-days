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

Route::get('/admin', function () {
    ob_start();
    require_once __DIR__ . '/../admin.php';
    return ob_get_clean();
});

Route::get('/', function () {
    ob_start();
    require_once __DIR__ . '/../index.php';
    return ob_get_clean();
});
