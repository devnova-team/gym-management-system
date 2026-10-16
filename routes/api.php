<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\MemberSegmentationController;

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});


// Member routes
Route::middleware('auth:api')->group(function () {

    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);


    Route::get(
    '/members/segmented',
    [MemberSegmentationController::class, 'index']
)->middleware('role:owner,receptionist');

    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);
});



Route::middleware('auth:api')->group(function () {

    // Plan routes
    Route::get('/plans', [PlanController::class, 'index']);
    Route::post('/plans', [PlanController::class, 'store'])
    ->middleware('owner');
    Route::get('/plans/{id}', [PlanController::class, 'show']);
    Route::put('/plans/{id}', [PlanController::class, 'update']);
    Route::delete('/plans/{id}', [PlanController::class, 'destroy']);


    // Subscription routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::post('/subscriptions/renew', [SubscriptionController::class, 'renew']);
});


 // Attendance routes
Route::middleware('auth:api')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);

      Route::post('/attendance/sync', [ AttendanceController::class,'sync']);


    Route::post('/attendance/{id}/check-out', [AttendanceController::class, 'checkOut'])
        ->whereNumber('id');

    Route::get('/attendance/today', [AttendanceController::class, 'today']);

    Route::get('/attendance/currently-inside', [AttendanceController::class, 'currentlyInside']);
});


// Message Template routes
Route::middleware('auth:api')->group(function () {

    // Get all message templates for the authenticated gym
    Route::get('/message-templates', [
        MessageTemplateController::class,
        'index'
    ]);

    // Update a message template (Owner only)
    Route::put('/message-templates', [
        MessageTemplateController::class,
        'update'
    ])->middleware('owner');

    
});
