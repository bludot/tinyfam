<?php

use Core\Route;

Route::get('/test', 'MainController');
Route::get('/bloop', 'CategoriesController');
Route::get('/rest_test', 'RestController');

Route::get('/rest_test/{value_}/{test}', 'RestController');
Route::get('/rest_test2/{value_}/{test}', 'RestController@test');
Route::get('/rest_test2/test/{value_}/{test}', 'RestController@test');


