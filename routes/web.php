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

// Route::get('/home', 'HomeController@index')->name('home');
Route::get("/welcome", function(){
   return view("welcome");
});

Route::group(['middleware' => ['admin']], function () {
// cars management
   
Route::get('/cars', 'CarController@index');
Route::post('/add-car/{type_request}', 'CarController@create');
Route::post('/car/delete/{id}', 'CarController@showModalToDelete');
Route::post('/destroy-car/{id}', 'CarController@delete');
Route::post('/car/edit/{id}', 'CarController@showModalToUpdate');
Route::post('/edit-car', 'CarController@update');

Route::post('/car/statistics/{id}', 'CarController@showStatisticsModal');

// account management
Route::get('/users', 'UserController@index');
Route::post('/add-driver/{type_request}', 'UserController@create');
Route::post('/user/delete/{id}', 'UserController@showModalToDelete');
Route::post('/destroy-user/{id}', 'UserController@delete');
Route::post('/user/edit/{id}', 'UserController@showModalToUpdate');
Route::post('/edit-user', 'UserController@update');


//reparation management

Route::get('/reparations', 'ReparationController@index');
Route::post('/add-reparation/{type_request}', 'ReparationController@create');
Route::post('/reparation/delete/{id}', 'ReparationController@showModalToDelete');
Route::post('/destroy-reparation/{id}', 'ReparationController@delete');
Route::post('/reparation/edit/{id}', 'ReparationController@showModalToUpdate');
Route::post('/edit-reparation', 'ReparationController@create');

Route::post('/reparation/end/{id}', 'ReparationController@showModalToEnd');
Route::post('/end-reparation', 'ReparationController@updateDone');

});
// activity management
Route::group(['middleware' => ['driver']], function () {
 Route::get("/home", function(){
   return view("home");
});

Route::get('/activities', 'ActivityController@index');
Route::post('/add-activity/{type_request}', 'ActivityController@create');
Route::post('/activity/details/{id}', 'ActivityController@showModalDetails');
Route::post('/activity/delete/{id}', 'ActivityController@showModalToDelete');
Route::post('/activity/edit/{id}', 'ActivityController@showModalToUpdate');
Route::post('/edit-activity', 'ActivityController@update');
Route::post('/activity/end/{id}', 'ActivityController@showModalToEnd');
Route::post('/end-activity', 'ActivityController@updateDone');
Route::post('/destroy-activity/{id}', 'ActivityController@delete');

Route::get('/car/{type}/{id}', 'ActivityController@getSelectedActivity');
Route::get('/user/{type}/{id}', 'ActivityController@getSelectedActivity');
Route::post('/activities/delete', 'ActivityController@deleteMass');
});