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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');  //Мои молитвы

Route::get('/prayerslist', 'HomeController@prayersList')->name('list');

//Маршруты к разделу личный кабинет
Route::get('/personal', 'PersonalController@index')->name('personal');

Route::get('/personal/prayers_end', 'PersonalController@prayersEnd')->name('prayersEnd');

Route::get('/personal/invite_friends', 'PersonalController@generateCode')->name('generateCode');

Route::get('/personal/about', 'PersonalController@about')->name('about');

Route::post('/personal/generate', 'PersonalController@generate');

Route::post('/personal/createGroup', 'PersonalController@createGroup');

Route::post('/personal/getGroups', 'PersonalController@getGroups');

Route::post('/personal/addUser', 'PersonalController@addUser');

Route::post('/personal/leaveGroup', 'PersonalController@leaveGroup');

Route::post('/personal/delGroup', 'PersonalController@delGroup');

Route::post('/personal/addAdmin', 'PersonalController@addAdmin');

Route::post('/personal/delAdmin', 'PersonalController@delAdmin');

Route::post('/personal/changeGroupName', 'PersonalController@changeGroupName');

Route::get('/personal/group/{id}', 'PersonalController@showGroup')->name('showGroup');

Route::get('/personal/usersList/{count?}', 'PersonalController@usersList')->name('usersList');

//Маршрут обработки ajax запросов
Route::post('/ajax/saveMN', 'AjaxController@saveMN');

Route::post('/ajax/editMN', 'AjaxController@editMN');

Route::post('/ajax/doneMN', 'AjaxController@doneMN');

Route::post('/ajax/deleteMN', 'AjaxController@deleteMN');

Route::post('/ajax/getTable', 'AjaxController@getTable');

Route::post('/ajax/getUsers', 'AjaxController@getUsers');

Route::post('/ajax/getPrayersList', 'AjaxController@getPrayersList');

//Маршруты к административным страницам
Route::get('/admin', 'AdminController@index');

Route::get('/admin/import', 'AdminController@import');

Route::post('/admin/getTable', 'AdminController@getTable');

Route::post('/admin/delTableRow', 'AdminController@deleteRowInTable');
