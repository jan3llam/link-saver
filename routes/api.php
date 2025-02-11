<?php

use App\Http\Controllers\LinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/links', [LinkController::class, 'getUserLinks']);

Route::post('/fetch', [LinkController::class, 'fetchMail']);
