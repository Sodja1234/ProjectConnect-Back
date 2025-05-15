<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/register', [Controller::class, 'register']);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('roles', RoleController::class);