<?php

Route::group(['prefix' => 'bankaccountoperations'], function () {

    Route::group(['prefix' => 'deposit'], function () {
        Route::post('/', 'DepositController@create');
        Route::patch('/{id}/approve', 'DepositController@approve');
    });
});
