<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;


Route::get('/links', [LinkController::class, 'getUserLinks']);
Route::post('/fetch', [LinkController::class, 'fetchMail']);
