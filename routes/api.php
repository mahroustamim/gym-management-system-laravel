<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\GymRoleController;
use App\Http\Controllers\GymSubscriptionPlanController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SaasSubscriptionPlanController;
use App\Http\Controllers\SystemRoleController;
use App\Http\Controllers\SystemUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function () {

    // auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('saas-plans', SaasSubscriptionPlanController::class);
        Route::get('/saas-logs', [LogController::class, 'saas']);
        Route::get('/gym-logs', [LogController::class, 'gym']);
        Route::apiResource('gyms', GymController::class);
        Route::apiResource('gym-plans', GymSubscriptionPlanController::class);
        Route::apiResource('system-roles', SystemRoleController::class);
        Route::get('system-permissions', [SystemRoleController::class, 'permissions']);
        Route::apiResource('gym-roles', GymRoleController::class);
        Route::get('gym-permissions', [GymRoleController::class, 'permissions']);
        Route::apiResource('system-employees', SystemUserController::class);
        Route::get('system-employees-roles', [SystemUserController::class, 'roles']);
    });
});
