<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarManagement\Car\CarController;
use App\Http\Controllers\RoleManagement\Role\RoleController;
use App\Http\Controllers\StepManagement\Step\StepController;
use App\Http\Controllers\UserManagement\User\UserController;
use App\Http\Controllers\UserManagement\Expert\ExpertController;
use App\Http\Controllers\CarManagement\ClientCar\ClientCarController;
use App\Http\Controllers\ProblemManagement\Problem\ProblemController;
use App\Http\Controllers\SolutionManagement\Solution\SolutionController;
use App\Http\Controllers\SolutionManagement\SolutionFeedback\SolutionFeedbackController;

// Auth Routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/unathorize', [AuthController::class, 'unauthorize'])->name('login');
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook']);

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
            Route::post('/client-problems/request', 'clientRequestProblem')->middleware('can:Add Problem');
            Route::post('/pay-for-request', 'payForClientProblem')->middleware('can:Pay for Request');
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
            Route::get('/{id}/problems', 'getCarProblems')->middleware('can:View Problem');
        });
    });

    // Client cars Routes
    Route::prefix('client-cars')->group(function () {
        Route::controller(ClientCarController::class)->group(function () {
            Route::get('/list', 'index')->middleware('can:View Client Car');
            Route::post('/store', 'store')->middleware('can:Add Client Car');
            Route::delete('/delete/{id}', 'destroy');
        });
    });

    // Problems Routes
    Route::prefix('problems')->group(function () {
    Route::controller(ProblemController::class)->group(function () {
        Route::get('/list', 'index')->middleware('can:View Problem');
        Route::post('/store', 'store')->middleware('can:Add Problem');
        Route::get('/edit/{id}', 'show')->middleware('can:View Problem');
        Route::post('/update/{id}', 'update')->middleware('can:Edit Problem');
        Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete Problem');
        Route::post('client-problems/assign', 'assignProblemToExpert')->middleware('can:Assign Problem');
        });

    // Nested: Get solutions for a problem
    Route::get('{problemId}/solutions', [SolutionController::class, 'index'])->middleware('can:View Solution');
    });

    Route::prefix('solutions')->group(function () {
        Route::controller(SolutionController::class)->group(function () {
            Route::post('/store', 'store')->middleware('can:Add Solution');
            Route::post('/update/{id}', 'update')->middleware('can:Edit Solution');
            Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete Solution');
            Route::get('{solutionId}/steps', [StepController::class, 'index'])->middleware('can:View Step');
        });
    });
    //feedback Routes
    Route::prefix('feedback')->group(function () {
        Route::controller(SolutionFeedbackController::class)->group(function () {
            Route::post('/submit', 'store')->middleware('can:Add feedback');
            Route::get('/solution/{solutionId}', 'getBySolution')->middleware('can:View feedback');
            Route::put('/update/{id}', 'update')->middleware('can:Edit feedback');
            Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete feedback');
        });
    });


    Route::prefix('steps')->group(function () {
        Route::controller(StepController::class)->group(function () {
            Route::post('/store', 'store')->middleware('can:Add Step');
            Route::post('/update/{id}', 'update')->middleware('can:Edit Step');
            Route::delete('/delete/{id}', 'destroy')->middleware('can:Delete Step');
        });
    });
});