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

Route::post('/personal/generate', 'PersonalController@generate');

//Маршрут обработки ajax запросов
Route::post('/ajax/saveMN', 'AjaxController@saveMN');

Route::post('/ajax/editMN', 'AjaxController@editMN');

Route::post('/ajax/doneMN', 'AjaxController@doneMN');

Route::post('/ajax/getTable', 'AjaxController@getTable');

Route::post('/ajax/getUsers', 'AjaxController@getUsers');

//Маршруты к административным страницам
Route::get('/admin', 'AdminController@index');

Route::post('/admin/getTable', 'AdminController@getTable');

Route::post('/admin/delTableRow', 'AdminController@deleteRowInTable');
