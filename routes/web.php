<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - API Documentation Page
|--------------------------------------------------------------------------
|
| This route serves the API documentation landing page.
| All API functionality is in routes/api.php
|
*/

Route::get('/', function () {
    return view('api');
});
