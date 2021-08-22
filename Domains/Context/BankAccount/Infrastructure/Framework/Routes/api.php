<?php

Route::group(['prefix' => 'bankaccount'], function () {
    Route::group(['prefix' => 'account'], function () {
        Route::post('/', 'AccountController@create');
    });
    Route::group(['prefix' => 'balance'], function () {
        Route::patch('/{account_id}', 'BalanceController@update');
    });
});
