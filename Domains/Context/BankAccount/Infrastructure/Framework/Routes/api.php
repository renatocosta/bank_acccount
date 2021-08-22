<?php

Route::group(['prefix' => 'bankaccount'], function () {
    Route::group(['prefix' => 'account'], function () {
        Route::post('/', 'AccountController@create');
        Route::get('/{account_id}/transactions', 'AccountController@allTransactions');
    });
    Route::group(['prefix' => 'balance'], function () {
        Route::patch('/{account_id}/operation/{operation}/amount/{amount}', 'BalanceController@update');
    });
});
