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

Route::get('/home', 'HomeController@index')->name('home');

//Проверочный маршрут к тестовой странице
Route::match(["get", "post"], '/test', 'TestController@start')->name('test');

//Маршрут обработки ajax запросов
Route::post('/ajax/saveMN', 'AjaxController@saveMN');
