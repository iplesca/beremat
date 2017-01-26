<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', [
    'uses' => 'DashboardController@homepage',
    'as'   => 'home'
]);

Route::get('/random', [
    'uses' => 'DashboardController@randomBeer',
    'as'   => 'randomBeer'
]);

Route::get('/sameBrewery', [
    'uses' => 'DashboardController@sameBrewery',
    'as'   => 'sameBrewery'
]);
Route::post('/search', [
    'uses' => 'DashboardController@searchForm',
    'as'   => 'search'
]);
