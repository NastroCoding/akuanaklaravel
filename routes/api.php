<?php

use App\Http\Controllers\AuthController;
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

});