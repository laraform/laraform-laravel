<?php

Route::group(['middleware' => 'web'], function () {
    $method = strtolower(config('laraform.method'));

    Route::$method(config('laraform.endpoint'), '\Laraform\Controllers\FormController@process');
});