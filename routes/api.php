<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SubscriptionPlanController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Middleware\IsAdmin;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // Authentic user routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Admin-only routes
        Route::middleware(IsAdmin::class)->group(function () {
            Route::get('admin/dashboard', [AdminController::class, 'dashboard']);

            // Plans
            Route::post('plans', [AdminController::class, 'store']);
            Route::put('plans/{id}', [AdminController::class, 'update']);
            Route::delete('plans/{id}', [AdminController::class, 'destroy']);
        });

        // Subscription routes
        Route::prefix('subscription')->group(function () {
            Route::post('/', [SubscriptionController::class, 'subscribe']);
            Route::post('/cancel', [SubscriptionController::class, 'cancel']);
            Route::get('/active', [SubscriptionController::class, 'active']);
        });

        // Reporting routes (admins)
        Route::prefix('reports')->group(function () {
            Route::get('/total-users', [ReportController::class, 'totalUsersPerPlan']);
            Route::get('/active-subscriptions', [ReportController::class, 'activeSubscriptionsPerPlan']);
            Route::get('/monthly-new-subscriptions', [ReportController::class, 'monthlyNewSubscriptions']);
            Route::get('/churn-rate', [ReportController::class, 'planChurnRate']);
        });
    });
});
