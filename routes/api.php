<?php

use Illuminate\Http\Request;
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

Route::prefix('v1')->group(function (){

    Route::prefix('/basic')->group(function (){
        Route::get('/base',[\App\Http\Controllers\Api\Site\v1\BasicController::class,'base']);
        Route::get('/sidebar',[\App\Http\Controllers\Api\Site\v1\BasicController::class,'sidebar']);
        Route::middleware('auth:sanctum')->
        get('/user-sidebar',[\App\Http\Controllers\Api\Site\v1\BasicController::class,'userSidebar']);
    });

    Route::prefix('/orders')->group(function (){
        Route::get('/{order_id}',[\App\Http\Controllers\Api\Site\v1\OrderController::class,'show']);
        Route::get('/head/{order_id}',[\App\Http\Controllers\Api\Site\v1\OrderController::class,'head']);

        Route::middleware(['auth:sanctum','userAuth'])
            ->post('/start/{order_id}',[\App\Http\Controllers\Api\Site\v1\OrderController::class,'startTransaction']);

        Route::middleware(['auth:sanctum','userAuth'])
            ->post('/chat/{user_id}',[\App\Http\Controllers\Api\Site\v1\OrderController::class,'startChat']);
    });

    Route::prefix('/auth')->group(function (){
        Route::get('/head',[\App\Http\Controllers\Api\Site\v1\AuthController::class,'head']);

        Route::post('/login',[\App\Http\Controllers\Api\Site\v1\AuthController::class,'login']);

        Route::post('/register',[\App\Http\Controllers\Api\Site\v1\AuthController::class,'register']);

        Route::post('/send-verification-code',[\App\Http\Controllers\Api\Site\v1\AuthController::class,'sendSMS']);
    });

    Route::get('/home',\App\Http\Controllers\Api\Site\v1\HomeController::class);

    Route::get('/about-us',[\App\Http\Controllers\Api\Site\v1\FagController::class,'about']);

    Route::get('/contact-us',[\App\Http\Controllers\Api\Site\v1\FagController::class,'contact']);

    Route::get('/law',[\App\Http\Controllers\Api\Site\v1\FagController::class,'law']);

    Route::get('/users/{user}',\App\Http\Controllers\Api\Site\v1\UserController::class);

    Route::middleware('auth:sanctum')->group(function (){
        Route::get('/user',function (){
            return auth()->user();
        });
        Route::get('/logout',[\App\Http\Controllers\Api\Site\v1\AuthController::class,'logout']);
    });
});
