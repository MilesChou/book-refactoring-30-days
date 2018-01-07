<?php

Route::get('/admin/login', 'AdminController@login');
Route::get('/contact', 'ContactController@index');

Route::any('/admin.php', 'AdminController@index');
Route::any('/', 'ShopController@index');
