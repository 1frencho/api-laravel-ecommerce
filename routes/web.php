<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API Laravel Ecommerce!',
        'version' => '1.0.0',
        // 'documentation_url' => 'http://127.0.0.1:8000/docs',
    ]);
});
