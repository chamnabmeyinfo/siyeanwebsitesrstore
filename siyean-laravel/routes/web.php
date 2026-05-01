<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/p/{page}', [PageController::class, 'display'])
    ->name('pages.display');

Route::resource('pages', PageController::class);
