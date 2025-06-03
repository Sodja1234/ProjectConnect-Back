<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\DomainController;
use \App\Http\Controllers\CandidacyController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/register', [Controller::class, 'register']);
Route::apiResource('projects', ProjectController::class)->only(["index", "show"]);
Route::apiResource('roles', RoleController::class);
Route::apiResource('domains', DomainController::class);
Route::apiResource('skills', SkillController::class);



// ------------------- ROUTES PROTÉGÉES ------------------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('projects', ProjectController::class)->except(["index", "show"]);
    Route::post('/project-roles/{id}/apply',[CandidacyController::class, 'store']);
    Route::get('/projects/{id}/candidacies', [CandidacyController::class, 'index']);

});

require __DIR__ . '/api-auth.php';
