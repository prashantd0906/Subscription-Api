<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\UserActivityController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Api\V2\PromoCodeController;
use App\Http\Controllers\Api\V2\SubscriptionPromoCodeController;

// V1 
Route::prefix('v1')->group(function () {

    // Auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {

        // Authenticated user routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Subscription routes
        Route::prefix('subscription')->group(function () {
            Route::post('/', [SubscriptionController::class, 'subscribe']);
            Route::post('/cancel', [SubscriptionController::class, 'cancel']);
            Route::get('/active', [SubscriptionController::class, 'active']);
            Route::get('/plans', [SubscriptionController::class, 'allPlans']);
        });

        // User activity routes
        Route::prefix('user')->group(function () {
            Route::get('activity', [UserActivityController::class, 'myActivity']); // user own activity
        });

        // Admin routes
        Route::prefix('admin')->middleware(IsAdmin::class)->group(function () {

            // Dashboard & notifications
            Route::get('dashboard', [AdminController::class, 'dashboard']);
            Route::get('notifications', [AdminController::class, 'notifications']);

            // Plans
            Route::prefix('plans')->group(function () {
                Route::get('/', [AdminController::class, 'index']);
                Route::post('/', [AdminController::class, 'store']);
                Route::put('/{id}', [AdminController::class, 'update']);
                Route::delete('/{id}', [AdminController::class, 'destroy']);
            });

            // Admin view all user activities
            Route::get('user-activity', [UserActivityController::class, 'allActivities']);
        });

        // Admin Reports
        Route::prefix('reports')->middleware(IsAdmin::class)->group(function () {
            Route::get('/summary', [ReportController::class, 'summary']);
        });
    });
});

// V2
Route::prefix('v2')->middleware('jwt.auth')->group(function () {

    // Accessed by both users & admins
    Route::get('/promo-codes', [PromoCodeController::class, 'index']);

    // Admin-only
    Route::middleware(IsAdmin::class)->group(function () {
        Route::post('/promo-codes', [PromoCodeController::class, 'store']);
        Route::put('/promo-codes/{id}', [PromoCodeController::class, 'update']);
        Route::delete('/promo-codes/{id}', [PromoCodeController::class, 'destroy']);
        Route::post('/subscription-promo', [SubscriptionPromoCodeController::class, 'assign']);
    });
});
