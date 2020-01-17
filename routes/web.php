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

Route::get('/prayers_end', 'HomeController@prayersEnd')->name('prayersEnd');

//Проверочный маршрут к тестовой странице
Route::match(["get", "post"], '/test', 'TestController@start')->name('test');

//Маршрут обработки ajax запросов
Route::post('/ajax/saveMN', 'AjaxController@saveMN');

Route::post('/ajax/editMN', 'AjaxController@editMN');

Route::post('/ajax/doneMN', 'AjaxController@doneMN');

Route::post('/ajax/getTable', 'AjaxController@getTable');

Route::post('/ajax/getUsers', 'AjaxController@getUsers');

//Маршруты к административным страницам
Route::get('/admin', 'AdminController@index');

Route::post('/admin/getTable', 'AdminController@getTable');
