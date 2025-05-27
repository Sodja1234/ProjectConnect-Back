<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;


Route::post('/login', AuthController::class);
Route::post('/register', RegisterController::class);
Route::post('/forgot-password', PasswordResetLinkController::class);
Route::post('/reset-password', NewPasswordController::class);
