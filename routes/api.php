<?php

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
        Route::get('/base',[App\Http\Controllers\Api\Site\v1\BasicController::class,'base']);
        Route::get('/sidebar',[App\Http\Controllers\Api\Site\v1\BasicController::class,'sidebar']);
        Route::middleware('auth:sanctum')->get('/user',[App\Http\Controllers\Api\Site\v1\BasicController::class,'user']);
    });

    Route::prefix('/notifications')->group(function (){
        Route::get('/public',[\App\Http\Controllers\Api\Site\v1\NotificationController::class,'siteNotification']);
        Route::middleware('auth')->get('/user',[\App\Http\Controllers\Api\Site\v1\NotificationController::class,'userNotification']);
    });

    Route::prefix('/orders')->group(function (){
        Route::get('/{order_id}',[App\Http\Controllers\Api\Site\v1\OrderController::class,'show']);
        Route::middleware(['auth:sanctum','userAuth'])
            ->post('/start-transaction/{order_id}',[App\Http\Controllers\Api\Site\v1\OrderController::class,'startTransaction']);
    });

    Route::prefix('/articles')->group(function (){
        Route::get('',[App\Http\Controllers\Api\Site\v1\ArticleController::class,'index']);
        Route::get('/{slug}',[App\Http\Controllers\Api\Site\v1\ArticleController::class,'show']);
        Route::middleware(['auth:sanctum','userAuth'])
            ->post('/new-comment/{slug}',[App\Http\Controllers\Api\Site\v1\ArticleController::class,'storeComment']);
    });

    Route::prefix('/auth')->group(function (){
        Route::post('/login',[App\Http\Controllers\Api\Site\v1\AuthController::class,'login']);
        Route::post('/register',[App\Http\Controllers\Api\Site\v1\AuthController::class,'register']);
        Route::post('/send-verification-code',[App\Http\Controllers\Api\Site\v1\AuthController::class,'sendSMS']);
    });

    Route::prefix('/users')->group(function (){
        Route::get('/{user}',App\Http\Controllers\Api\Site\v1\UserController::class);
        Route::middleware(['auth:sanctum','userAuth'])
            ->post('/offend/{user}',[App\Http\Controllers\Api\Site\v1\UserController::class,'sendOffend']);
    });

    Route::prefix('home')->group(function (){
        Route::get('/',App\Http\Controllers\Api\Site\v1\HomeController::class);
        Route::get('/categories',[App\Http\Controllers\Api\Site\v1\HomeController::class,'categories']);
    });

    Route::get('/about-us',[App\Http\Controllers\Api\Site\v1\FagController::class,'about']);

    Route::get('/contact-us',[App\Http\Controllers\Api\Site\v1\FagController::class,'contact']);

    Route::get('/laws',[App\Http\Controllers\Api\Site\v1\FagController::class,'law']);
    Route::get('/chat-laws',[App\Http\Controllers\Api\Site\v1\FagController::class,'chatLaw']);
    Route::get('/phone-laws',[App\Http\Controllers\Api\Site\v1\FagController::class,'phoneLaw']);
    Route::get('/transaction-laws',[App\Http\Controllers\Api\Site\v1\FagController::class,'transactionLaw']);

    Route::get('/fag',[App\Http\Controllers\Api\Site\v1\FagController::class,'fag']);

    Route::middleware('auth:sanctum')->prefix('client')->group(function (){
        Route::middleware('userAuth')->group(function (){
            Route::get('cards/details',[App\Http\Controllers\Api\Site\v1\Panel\CardController::class,'details']);
            Route::apiResource('cards',App\Http\Controllers\Api\Site\v1\Panel\CardController::class);

            Route::get('tickets/details',[App\Http\Controllers\Api\Site\v1\Panel\TicketController::class,'details']);
            Route::apiResource('tickets',App\Http\Controllers\Api\Site\v1\Panel\TicketController::class);

            Route::get('orders/details',[App\Http\Controllers\Api\Site\v1\Panel\OrderController::class,'details']);
            Route::post('orders/calculator',[App\Http\Controllers\Api\Site\v1\Panel\OrderController::class,'calculate']);
            Route::delete('orders/delete-image/{order_id}',[App\Http\Controllers\Api\Site\v1\Panel\OrderController::class,'deleteImage']);
            Route::apiResource('orders',App\Http\Controllers\Api\Site\v1\Panel\OrderController::class);

            Route::prefix('chat')->group(function (){
                Route::get('/list',[App\Http\Controllers\Api\Site\v1\Panel\ChatController::class,'list']);
                Route::get('/list/{group_id}',[App\Http\Controllers\Api\Site\v1\Panel\ChatController::class,'open']);
                Route::post('/list/{group_id}',[App\Http\Controllers\Api\Site\v1\Panel\ChatController::class,'send']);
                Route::post('/new',[App\Http\Controllers\Api\Site\v1\Panel\ChatController::class,'startChat']);
            });

            Route::apiResource('bookmarks',\App\Http\Controllers\Api\Site\v1\Panel\BookmarkController::class)->only(['index','store','destroy']);

            Route::prefix('accounting')->group(function (){
                Route::get('',[App\Http\Controllers\Api\Site\v1\Panel\AccountingController::class,'index']);
                Route::post('',[App\Http\Controllers\Api\Site\v1\Panel\AccountingController::class,'request']);
                Route::get('/show/{id}',[App\Http\Controllers\Api\Site\v1\Panel\AccountingController::class,'show']);
                Route::get('/details',[App\Http\Controllers\Api\Site\v1\Panel\AccountingController::class,'details']);
                Route::post('/charge',[App\Http\Controllers\Api\Site\v1\Panel\AccountingController::class,'charge']);
            });

            Route::get('transactions',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'index']);
            Route::get('transactions/{id}',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'show']);
            Route::post('transactions/{id}',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'update']);
            Route::delete('transactions/{id}',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'cancel']);
            Route::post('transaction-options/refund/{id}',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'requestToReturn']);
            Route::post('transaction-options/receive/{id}',[App\Http\Controllers\Api\Site\v1\Panel\TransactionController::class,'no_receive']);
        });

        Route::get('/dashboard',App\Http\Controllers\Api\Site\v1\Panel\DashboardController::class);

        Route::get('/auth',App\Http\Controllers\Api\Site\v1\Panel\AuthController::class);
        Route::post('/auth',[App\Http\Controllers\Api\Site\v1\Panel\AuthController::class,'auth']);

        Route::get('/profile',App\Http\Controllers\Api\Site\v1\Panel\ProfileController::class);
        Route::post('/profile',[App\Http\Controllers\Api\Site\v1\Panel\ProfileController::class,'update']);

        Route::get('/logout',[App\Http\Controllers\Api\Site\v1\AuthController::class,'logout']);
    });
});
