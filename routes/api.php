<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarManagement\Car\CarController;
use App\Http\Controllers\RoleManagement\Role\RoleController;
use App\Http\Controllers\UserManagement\User\UserController;
use App\Http\Controllers\UserManagement\Expert\ExpertController;

// Auth Routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/unathorize', [AuthController::class, 'unauthorize'])->name('login');

Route::get('/user', function (Request $request) {
    return $request->user()->load('roles.permissions');
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    // Roles Routes
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/list', 'index')->middleware('can:View Role');
            Route::get('/edit/{id}', 'show')->middleware('can:View Role');
            Route::get('/permissions', 'getPermissions')->middleware('can:View Role');
            Route::post('/store', 'store')->middleware('can:Add Role');
            Route::post('/update/{id}', 'update')->middleware('can:Edit Role');
            Route::get('/delete/{id}', 'destroy')->middleware('can:Delete Role');
        });
    });

    // User Routes
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/list', 'index')->middleware('can:View User');
            Route::post('/store', 'store')->middleware('can:Add User');
            Route::get('/edit/{id}', 'show')->middleware('can:View User');
            Route::post('/update/{id}', 'update')->middleware('can:Edit User');
            Route::get('/delete/{id}', 'destroy')->middleware('can:Delete User');
        });
    });

    // experts Routes
    Route::prefix('experts')->group(function () {
        Route::controller(ExpertController::class)->group(function () {
            Route::get('/list', 'index')->middleware('can:View Expert');
            Route::post('/store', 'store')->middleware('can:Add Expert');
            Route::get('/edit/{id}', 'show')->middleware('can:View Expert');
            Route::post('/update/{id}', 'update')->middleware('can:Edit Expert');
            Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete Expert');
        });
    });

    // cars Routes
    Route::prefix('cars')->group(function () {
        Route::controller(CarController::class)->group(function () {
            Route::get('/list', 'index')->middleware('can:View Car');
            Route::post('/store', 'store')->middleware('can:Add Car');
            Route::get('/edit/{id}', 'show')->middleware('can:View Car');
            Route::post('/update/{id}', 'update')->middleware('can:Edit Car');
            Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete Car');
        });
    });
});
