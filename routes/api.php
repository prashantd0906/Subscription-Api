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

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes (requires JWT auth)
    Route::middleware('jwt.auth')->group(function () {
        // Authenticated user routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // User Activity (User can view own, Admin can view all)
        Route::get('user/activity', [UserActivityController::class, 'myActivity']); // Authenticated user only

        Route::prefix('admin')->middleware(IsAdmin::class)->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard']);

            // Plans management
            Route::prefix('plans')->group(function () {
                Route::get('/', [AdminController::class, 'index']);
                Route::post('/', [AdminController::class, 'store']);
                Route::put('/{id}', [AdminController::class, 'update']);
                Route::delete('/{id}', [AdminController::class, 'destroy']);
            });

            // Admin can view all user activities
            Route::get('user-activity', [UserActivityController::class, 'allActivities']);
        });

        // Subscription routes
        Route::prefix('subscription')->group(function () {
            Route::post('/', [SubscriptionController::class, 'subscribe']);
            Route::post('/cancel', [SubscriptionController::class, 'cancel']);
            Route::get('/active', [SubscriptionController::class, 'active']);
        });

        // Reporting routes (admins only)
        Route::prefix('reports')->middleware(IsAdmin::class)->group(function () {
            Route::get('/total-users', [ReportController::class, 'totalUsersPerPlan']);
            Route::get('/active-subscriptions', [ReportController::class, 'activeSubscriptionsPerPlan']);
            Route::get('/monthly-new-subscriptions', [ReportController::class, 'monthlyNewSubscriptions']);
            Route::get('/churn-rate', [ReportController::class, 'planChurnRate']);
        });
    });
});

// V2 ROUTES (Promo Codes & Subscription Promo Codes)
Route::prefix('v2')->middleware(['jwt.auth', IsAdmin::class])->group(function () {
    // Promo Code CRUD
    Route::post('/promo-codes', [PromoCodeController::class, 'store']);
    Route::get('/promo-codes', [PromoCodeController::class, 'index']);
    Route::get('/promo-codes/{id}', [PromoCodeController::class, 'show']);
    Route::put('/promo-codes/{id}', [PromoCodeController::class, 'update']);
    Route::delete('/promo-codes/{id}', [PromoCodeController::class, 'destroy']);

    // Assign promocode to subscription
    Route::post('/subscription-promo', [SubscriptionPromoCodeController::class, 'assign']);
});
