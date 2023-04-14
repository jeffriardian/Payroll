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

Route::prefix('recruitment')->group(function() {
    // Item PPH Route Group
    Route::group(['prefix' => 'pph'], function () {
        Route::get('/', 'PPH\PPHController@index')
            ->name('recruitment.pph.index');
        Route::post('/store', 'PPH\PPHController@store')
            ->name('recruitment.pph.store');
        Route::put('/update/{id}', 'PPH\PPHController@update')
            ->name('recruitment.pph.update');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'PPH\AjaxGetPPH')
                ->name('ajax.recruitment.get.pph');
            Route::get('find-bulan', 'PPH\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'PPH\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.years');
            Route::get('get-gaji', 'PPH\AjaxGetGaji@getGajis')
                ->name('recruitment.ajax.find.gaji');
            Route::get('get-jaminan', 'PPH\AjaxGetJaminan@getJaminans')
                ->name('recruitment.ajax.find.jaminan');
            Route::get('get-potongan', 'PPH\AjaxGetPotongan@getPotongans')
                ->name('recruitment.ajax.find.potongan');
            Route::get('dashboard', 'Dashboard\DashboardController@index')
                ->name('recruitment.dashboard.index');
        });
    });

    // Item SPT Route Group
    Route::group(['prefix' => 'spt'], function () {
        Route::get('/', 'SPT\SPTController@index')
            ->name('recruitment.spt.index');
        Route::post('/store', 'SPT\SPTController@store')
            ->name('recruitment.spt.store');
        Route::put('/update/{id}', 'SPT\SPTController@update')
            ->name('recruitment.spt.update');
        Route::get('/spt', 'SPT\SPTController@downloadSPT')
            ->name('recruitment.download.spt');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'SPT\AjaxGetSPT')
                ->name('ajax.recruitment.get.spt');
            Route::get('find-bulan', 'SPT\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'SPT\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.years');
            Route::get('get-gaji', 'SPT\AjaxGetGaji@getGajis')
                ->name('recruitment.ajax.find.gaji');
            Route::get('get-jaminan', 'SPT\AjaxGetJaminan@getJaminans')
                ->name('recruitment.ajax.find.jaminan');
            Route::get('get-potongan', 'SPT\AjaxGetPotongan@getPotongans')
                ->name('recruitment.ajax.find.potongan');
            Route::get('print-spt', 'SPT\SPTController@printSPT')
                ->name('recruitment.print.spt');
        });
    });

    // Item Payroll Route Group
    Route::group(['prefix' => 'payroll'], function () {
        Route::get('/', 'Payroll\PayrollController@index')
            ->name('recruitment.payroll.index');
        Route::post('/store', 'Payroll\PayrollController@store')
            ->name('recruitment.payroll.store');
        Route::post('/import', 'Payroll\PayrollController@import')
            ->name('recruitment.import.payroll');

        Route::get('/rekapan-smm', 'Payroll\PayrollController@downloadRekapanSmm')
            ->name('recruitment.download.rekapan.smm');
        Route::get('/rekapan-atm', 'Payroll\PayrollController@downloadRekapanAtm')
            ->name('recruitment.download.rekapan.atm');

        Route::get('/permata-smm', 'Payroll\PayrollController@downloadPermataSmm')
            ->name('recruitment.download.permata.smm');
        Route::get('/permata-atm', 'Payroll\PayrollController@downloadPermataAtm')
            ->name('recruitment.download.permata.atm');

        Route::get('/excell-smm', 'Payroll\PayrollController@downloadExcellSmm')
            ->name('recruitment.download.excell.smm');
        Route::get('/excell-atm', 'Payroll\PayrollController@downloadExcellAtm')
            ->name('recruitment.download.excell.atm');

        Route::get('/full-smm', 'Payroll\PayrollController@downloadFullSmm')
            ->name('recruitment.download.full.smm');
        Route::get('/full-atm', 'Payroll\PayrollController@downloadFullAtm')
            ->name('recruitment.download.full.atm');

        Route::get('/pph-smm', 'Payroll\PayrollController@downloadPPHSmm')
            ->name('recruitment.download.pph.smm');
        Route::get('/pph-atm', 'Payroll\PayrollController@downloadPPHAtm')
            ->name('recruitment.download.pph.atm');

        Route::get('/deletePayroll', 'Payroll\PayrollController@deletePayroll')
            ->name('recruitment.delete.payroll');
        Route::get('/cetak', 'Payroll\PayrollController@printSlip')
            ->name('recruitment.print.slip');
        // Route::put('/update/{id}', 'Payroll\PayrollController@update')
        //     ->name('recruitment.payroll.update');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'Payroll\AjaxGetPayroll')
                ->name('ajax.recruitment.get.payroll');
            Route::get('find-company', 'Payroll\AjaxGetCompany@getCompanies')
                ->name('ajax.recruitment.get.company');
            Route::get('find-bulan', 'Payroll\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'Payroll\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.months');
        });
    });

    // Item THR Route Group
    Route::group(['prefix' => 'thr'], function () {
        Route::get('/', 'THR\THRController@index')
            ->name('recruitment.thr.index');
        Route::post('/store', 'THR\THRController@store')
            ->name('recruitment.thr.store');
        Route::post('/import', 'THR\THRController@import')
            ->name('recruitment.import.thr');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'THR\AjaxGetTHR')
                ->name('ajax.recruitment.get.thr');
            Route::get('find-company', 'THR\AjaxGetCompany@getCompanies')
                ->name('ajax.recruitment.get.company');
            Route::get('find-bulan', 'THR\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'THR\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.months');
        });
    });

    // Item Bonus Route Group
    Route::group(['prefix' => 'bonus'], function () {
        Route::get('/', 'Bonus\BonusController@index')
            ->name('recruitment.bonus.index');
        Route::post('/store', 'Bonus\BonusController@store')
            ->name('recruitment.bonus.store');
        Route::post('/import', 'Bonus\BonusController@import')
            ->name('recruitment.import.bonus');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'Bonus\AjaxGetBonus')
                ->name('ajax.recruitment.get.bonus');
            Route::get('find-company', 'Bonus\AjaxGetCompany@getCompanies')
                ->name('ajax.recruitment.get.company');
            Route::get('find-bulan', 'Bonus\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'Bonus\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.months');
        });
    });

    // Item Pesangon Route Group
    Route::group(['prefix' => 'pesangon'], function () {
        Route::get('/', 'Pesangon\PesangonController@index')
            ->name('recruitment.pesangon.index');
        Route::post('/store', 'Pesangon\PesangonController@store')
            ->name('recruitment.pesangon.store');
        Route::post('/import', 'Pesangon\PesangonController@import')
            ->name('recruitment.import.pesangon');

        Route::group(['prefix' => 'api/v1'], function () {
            Route::get('list', 'Pesangon\AjaxGetPesangon')
                ->name('ajax.recruitment.get.pesangon');
            Route::get('find-company', 'Pesangon\AjaxGetCompany@getCompanies')
                ->name('ajax.recruitment.get.company');
            Route::get('find-bulan', 'Pesangon\AjaxGetMonth@getMonths')
                ->name('ajax.recruitment.get.months');
            Route::get('find-tahun', 'Pesangon\AjaxGetYear@getYears')
                ->name('ajax.recruitment.get.months');
        });
    });
});
