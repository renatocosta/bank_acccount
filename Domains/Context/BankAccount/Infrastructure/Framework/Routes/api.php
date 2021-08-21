<?php

Route::group(['prefix' => 'bankaccount'], function () {
    Route::post('/account', 'AccountController@create');
});