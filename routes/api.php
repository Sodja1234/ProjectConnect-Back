<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\CandidacyController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\ExperienceController;

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
    Route::post('/project-roles/{id}/apply', [CandidacyController::class, 'store']);
    Route::get('/projects/{id}/candidacies', [CandidacyController::class, 'index']);


    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);

    Route::get('/chats/{chatId}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);

    Route::apiResource('projects', ProjectController::class)->except(["index", "show"]);
    Route::apiResource('portfolios', PortfolioController::class);
    Route::get('myPortfolio', [PortfolioController::class, 'myPortfolio']);
     Route::apiResource('experiences', ExperienceController::class);
    Route::get('/notification/{id}', [NotificationController::class, 'show']);
    Route::delete('/notification/{id}/destroy', [NotificationController::class, 'destroy']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/last-notification', [NotificationController::class, 'lastNotification']);
});

require __DIR__ . '/api-auth.php';
