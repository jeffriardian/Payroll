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

Route::prefix('company')->group(function() {
    Route::group(['prefix' => 'api/v1'], function () {
        Route::get('find-position', 'AjaxGetPositionController@getPositions')
            ->name('company.ajax.find.position');

        Route::get('find-departemen', 'AjaxGetWorkingAreaController@getWorkingAreas')
            ->name('company.ajax.find.departemen');

        /*Route::get('employee-options', 'AjaxGetEmployeeOptions')
            ->name('employee.ajax.employee.options');

        Route::get('employee-detail', 'AjaxGetEmployeeDetail')
            ->name('ajax.employee.get.detail');*/

    });
});
