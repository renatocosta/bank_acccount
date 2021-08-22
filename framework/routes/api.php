<?php

use App\Http\Controllers\TestController;

Route::prefix('api/v1')->group(function () {
    Route::post('user-login', array(
        'uses' => 'AuthController@doLogin'
    ));
});
