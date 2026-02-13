<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->group(function () {

        require __DIR__.'/api/front.php';
        require __DIR__.'/api/admin.php';

    });
