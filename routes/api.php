<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Guest routes
Route::prefix('v1')->group(function() {
    Route::post('sign-in', [UserController::class, 'register'])->name('register');
    Route::post('log-in', [UserController::class, 'logIn'])->name('log-in');
    Route::post('forgotten', [UserController::class, 'forgotPassword'])->name('forgotten.pass');
    Route::post('forgotten/{token}', [UserController::class, 'resetForgottenPassword'])->name('password.reset');

    // Protected routes
    Route::group(['middleware' => ['auth.api']], function () {
        Route::post('change-password', [UserController::class, 'changePassword'])->name('password.change');
        Route::post('company', [TaskController::class, 'requestCompanyData'])->name('company.data');
        Route::post('getTaskData', [TaskController::class, 'getTaskData'])->name('get.taskData');
        Route::post('getTaskProgress', [TaskController::class, 'getTaskProgress'])->name('get.taskProgress');
    });
});
