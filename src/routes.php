<?php

Route::group(['middleware' => 'web'], function () {
    Route::post('/laraform/process', '\Laraform\Controllers\FormController@process');
});