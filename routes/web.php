<?php

use Illuminate\Support\Facades\Route;

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


// cars management
Route::get('/cars', 'CarController@index');
Route::post('/add-car/{type_request}', 'CarController@create');
Route::post('/car/delete/{id}', 'CarController@showModalToDelete');
Route::post('/destroy-car/{id}', 'CarController@delete');
Route::post('/car/edit/{id}', 'CarController@showModalToUpdate');
Route::post('/edit-car', 'CarController@update');

// account management
Route::get('/users', 'UserController@index');
Route::post('/add-driver/{type_request}', 'UserController@create');
Route::post('/user/delete/{id}', 'UserController@showModalToDelete');
Route::post('/destroy-user/{id}', 'UserController@delete');
Route::post('/user/edit/{id}', 'UserController@showModalToUpdate');
Route::post('/edit-user', 'UserController@update');

// activity management
Route::get('/activities', 'ActivityController@index');
