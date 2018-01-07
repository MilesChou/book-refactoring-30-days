<?php

Route::get('/admin.php', 'AdminController@index');
Route::get('/contact', 'ContactController@index');
Route::get('/', 'ShopController@index');
