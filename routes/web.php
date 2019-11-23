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

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

# 用户注册
Route::get('/signup', 'UsersController@create')->name('signup');

Route::resource('users', 'UsersController');

# 用户登录
Route::get('login', 'SessionController@create')->name('login');
Route::post('login', 'SessionController@store')->name('login');
Route::delete('logout', 'SessionController@destroy')->name('logout');

# 用户编辑
Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');

# 验证邮箱
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

# 重设密码
Route::get('password/reset', 'Auth/ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth/ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}','Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

# 粉丝与关注列表
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

# 关注与取关按钮
Route::post('/users/followers/{user}', 'FollowerController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowerController@destroy')->name('followers.destroy');