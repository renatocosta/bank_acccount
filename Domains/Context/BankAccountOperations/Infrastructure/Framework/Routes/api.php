<?php

Route::group(['prefix' => 'bankaccountoperations'], function () {
   
    Route::group(['prefix' => 'transaction'], function () {

    Route::post('/', 'TransactionController@create');
    Route::patch('/approve/{id}', 'TransactionController@approve');
    });

});
