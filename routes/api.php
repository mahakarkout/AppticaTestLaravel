<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AppTopCategoryController;

Route::apiResource('apps', AppController::class);
//Route::get('/appTopCategory', [AppTopCategoryController::class, 'index']);
Route::middleware('throttle:5,1')->get('/appTopCategory', [AppTopCategoryController::class, 'index'])->name('appTopCategory');
