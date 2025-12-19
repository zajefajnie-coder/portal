<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PublicProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/portfolios', [PortfolioController::class, 'index']);
Route::get('/portfolios/{id}', [PortfolioController::class, 'show']);
Route::get('/profiles', [PublicProfileController::class, 'index']);
Route::get('/profiles/{id}', [PublicProfileController::class, 'show']);

// Auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Portfolio management
    Route::get('/my-portfolios', [PortfolioController::class, 'myPortfolios']);
    Route::post('/portfolios', [PortfolioController::class, 'store']);
    Route::put('/portfolios/{id}', [PortfolioController::class, 'update']);
    Route::patch('/portfolios/{id}', [PortfolioController::class, 'update']); // Support PATCH as well
    Route::delete('/portfolios/{id}', [PortfolioController::class, 'destroy']);
});

// Admin routes (require admin or moderator role)
Route::middleware(['auth:sanctum', 'role:admin|moderator'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // User management
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users/{id}/approve', [AdminController::class, 'approveUser']);
    Route::post('/users/{id}/deny', [AdminController::class, 'denyUser']);
    Route::post('/users/{id}/ban', [AdminController::class, 'banUser']);
    Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser']);
    Route::post('/users/{id}/role', [AdminController::class, 'assignRole']);
    
    // Content moderation
    Route::get('/reported-images', [AdminController::class, 'reportedImages']);
    Route::post('/images/{id}/hide', [AdminController::class, 'hideImage']);
    Route::post('/images/{id}/unhide', [AdminController::class, 'unhideImage']);
    Route::delete('/images/{id}', [AdminController::class, 'deleteImage']);
    
    // Tag management
    Route::get('/tags', [AdminController::class, 'tags']);
    Route::delete('/tags/{id}', [AdminController::class, 'deleteTag']);
});

