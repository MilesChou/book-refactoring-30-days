<?php

Route::get('/admin/login', 'AdminController@login');
Route::post('/admin/login', 'AdminController@postLogin');
Route::get('/admin', 'AdminController@index');
Route::get('/contact', 'ContactController@index');

Route::any('/admin.php', 'AdminController@index');
Route::any('/', 'ShopController@index');
