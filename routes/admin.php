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
define('PAGINATION_COUNT', 10);
Route::group(['namespace' => 'Admin', 'middleware' => 'auth:admin'], function () {
    Route::get('/', 'DashboardController@index')->name('admin.dashboard');
    ######################### Begin Languages Route ########################
    Route::group(['prefix' => 'languages'], function () {
        Route::get('/', 'LanguagesController@index')->name('admin.languages');
        Route::get('create', 'LanguagesController@create')->name('admin.languages.create');
        Route::post('store', 'LanguagesController@store')->name('admin.languages.store');
        Route::get('edit/{id}', 'LanguagesController@edit')->name('admin.languages.edit');
        Route::post('update/{id}', 'LanguagesController@update')->name('admin.languages.update');

        Route::get('delete/{id}', 'LanguagesController@destroy')->name('admin.languages.delete');

    });
    ######################### End Languages Route ########################


    ######################### Begin Main Categories Routes ########################
    Route::group(['prefix' => 'main-categories'], function () {
        Route::get('/','MainCategoriesController@index') -> name('admin.mainCategories');
        Route::get('create','MainCategoriesController@create') -> name('admin.mainCategories.create');
        Route::post('store','MainCategoriesController@store') -> name('admin.mainCategories.store');
        Route::get('edit/{id}','MainCategoriesController@edit') -> name('admin.mainCategories.edit');
        Route::post('update/{id}','MainCategoriesController@update') -> name('admin.mainCategories.update');
        Route::get('delete/{id}','MainCategoriesController@destroy') -> name('admin.mainCategories.delete');
        Route::get('changeStatus/{id}','MainCategoriesController@changeStatus') -> name('admin.mainCategories.status');

    });
    ######################### End  Main Categories Routes  ########################
    ######################### Begin vendors Routes ########################
    Route::group(['prefix' => 'vendors'], function () {
        Route::get('/','VendorsController@index') -> name('admin.vendors');
        Route::get('create','VendorsController@create') -> name('admin.vendors.create');
        Route::post('store','VendorsController@store') -> name('admin.vendors.store');
        Route::get('edit/{id}','VendorsController@edit') -> name('admin.vendors.edit');
        Route::post('update/{id}','VendorsController@update') -> name('admin.vendors.update');
        Route::get('delete/{id}','VendorsController@destroy') -> name('admin.vendors.delete');
        Route::get('changeStatus/{id}','VendorsController@changeStatus') -> name('admin.vendor.status');
    });
    ######################### End  vendors Routes  ########################

});
//prefix ('admin')->RouteService
Route::group(['namespace' => 'Admin', 'middleware' => 'guest:admin'], function () {
    Route::get('login', 'LoginController@getLogin')->name('get.admin.login');
    Route::post('login', 'LoginController@login')->name('admin.login');
});
