<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\InvitationController;
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
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\ExperienceController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/register', [Controller::class, 'register']);
Route::apiResource('projects', ProjectController::class)->only(["index", "show"])->parameters(['projects' => 'slug']);
Route::apiResource('roles', RoleController::class);
Route::apiResource('domains', DomainController::class);
Route::apiResource('skills', SkillController::class);




// ------------------- ROUTES PROTÉGÉES ------------------- //
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('experiences', ExperienceController::class);

    Route::get('users/projects', [ProjectController::class, 'myproject']);
    Route::get('skill/user', [SkillController::class, 'myskill']);
    Route::get('users/projects/participed', [ProjectController::class, 'projectparticiped']);
    Route::apiResource('projects', ProjectController::class)->except(["index", "show"])->parameters(['projects' => 'slug']);
    Route::post('/project-roles/{id}/apply', [CandidacyController::class, 'store']);
    Route::get('/projects/{id}/candidacies', [CandidacyController::class, 'index']);
    Route::get('/invitations/candidacies', [InvitationController::class, 'index']);
    Route::post('/invitations/candidacies/{id}', [InvitationController::class, 'validate']);
    Route::post('/project-roles/{id}/invite', [CandidacyController::class, 'invite']);
    Route::get('/projects/{id}/pending-invitations', [CandidacyController::class, 'pendingInvitations']);
    Route::delete('/invitations/{invitationId}/cancel', [CandidacyController::class, 'cancelInvitation']);


    //valider ou rejeter la candidature (proprietaire du projet)
    Route::put('/candidacies/{id}/validate', [CandidacyController::class, 'validateCandidacy']);



    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{chatId}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    //-------------------------------------------------------------------------------------//

    Route::apiResource('projects', ProjectController::class)->except(["index", "show"]);
    Route::apiResource('portfolios', PortfolioController::class);
    Route::get('myPortfolio', [PortfolioController::class, 'myPortfolio'])->name('portfolio.me');
    Route::get('/notification/{id}', [NotificationController::class, 'show']);
    Route::delete('/notification/{id}/destroy', [NotificationController::class, 'destroy']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/last-notification', [NotificationController::class, 'lastNotification']);
    Route::post('/mark-as-read/notification', [NotificationController::class, 'markAllAsRead']);

    Route::post('/users/{user}/follow', [FollowController::class, 'follow']);
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/users/{user}/followers', [FollowController::class, 'followers']);
    Route::get('/users/{user}/following', [FollowController::class, 'following']);
    Route::get('/users/{user}/is-following', [FollowController::class, 'isFollowing']);
    Route::get('/users/{user}/follow-counts', [FollowController::class, 'followCounts']);
    //-----------------------------------------------------------------------------------------//

    // ----------------------ROUTES POUR SUGGESTIONS -------------------------------------------//
    Route::get('/users/suggestions', [FollowController::class, 'suggestions']);


    Route::get('/profile', [ProfileController::class, 'myProfile'])->name('profile.me');
    Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');


    // ----------------------ROUTES POUR ADMINISTRATION -------------------------------------------//
    Route::get('/users', [AdminController::class, 'index']);
    Route::post('/users/{user}/state', [AdminController::class, 'toggleState']);
    Route::post('projects/destroy/{id}', [AdminController::class, 'destroyProject']);
});



require __DIR__ . '/api-auth.php';
