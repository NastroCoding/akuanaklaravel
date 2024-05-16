<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameVersionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/v1/auth/signup', [AuthController::class, 'signUp']);
Route::post('/v1/auth/signin', [AuthController::class, 'signIn']);

Route::middleware('auth:sanctum')->group(function(){

    Route::controller(AuthController::class)->group(function(){
        Route::post('/v1/auth/signout', 'signOut');
    });

    Route::middleware('isAdmin')->group(function(){
        
        Route::controller(AdministratorController::class)->group(function(){
            Route::get('/v1/admins', 'index');
        });
        
        Route::controller(UserController::class)->group(function(){
            Route::get('/v1/users', 'index');
            Route::post('/v1/users', 'store');
            Route::put('/v1/users/{id}', 'update');
            Route::delete('/v1/users/{id}', 'destroy');
            Route::get('/v1/users/{username}', 'showUser');
        });

        Route::controller(GameController::class)->group(function(){
            Route::get('/v1/games', 'index');
            Route::post('/v1/games', 'store');
            Route::get('/v1/games/{slug}', 'show');
        });

        Route::controller(GameVersionController::class)->group(function(){
            Route::post('/v1/games/{slug}/upload', 'store');
            Route::get('');
            Route::put('/v1/games/{slug}', 'update');
            Route::delete('/v1/games/{slug}', 'destroy');
        });

    });

});
