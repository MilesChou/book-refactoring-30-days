<?php

Route::get('/admin.php', 'AdminController@index');
Route::get('/admin/login', 'AdminController@login');
Route::get('/contact', 'ContactController@index');
Route::get('/', 'ShopController@index');
