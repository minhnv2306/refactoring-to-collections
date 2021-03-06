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

Route::prefix('collection')->group(function () {
    Route::get('practice-1', 'CollectionController@practice1');
    Route::get('practice-3', 'CollectionController@practice3');
    Route::get('practice-4', 'CollectionController@practice4');
    Route::get('practice-5', 'CollectionController@practice5');
});

Route::prefix('refactor-collection')->group(function () {
    Route::get('practice-1', 'RefactorCollectionController@practice1');
    Route::get('practice-3', 'RefactorCollectionController@practice3');
    Route::get('practice-4', 'RefactorCollectionController@practice4');
    Route::get('practice-13', 'RefactorCollectionController@practice13');
    Route::get('practice-5', 'RefactorCollectionController@practice5');
});
