<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryPieceController;
use App\Http\Controllers\OrderController;
use App\Models\GalleryPiece;
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
Route::post('login',[AuthController::class,'adminLogin']);
Route::middleware(['auth:sanctum','ability:admin,superAdmin'])->group(function(){
    Route::post('logout',[AuthController::class,'logout']);
    Route::apiResource('galleryPieces',GalleryPieceController::class)
    ->except('index','update');
    Route::post('galleryPieces/{galleryPiece}/update',[GalleryPieceController::class,'update']);
    Route::post('orders/{order}/attachments',[AttachmentController::class,'upload']);
    Route::delete('orders/{order}/attachments/{attachment}',[AttachmentController::class,'delete']);
    Route::post('orders/{order}/changeState',[OrderController::class,'updateStatus']);
    Route::get('orders/',[OrderController::class,'index']);

    Route::post('orders/{order}/accept',[OrderController::class,'makeStatusAccepted']);
    Route::post('orders/{order}/deny',[OrderController::class,'makeStatusDenied']);
    Route::post('orders/{order}/declareDone',[OrderController::class,'makeStatusDone']);
    Route::delete('orders/{order}/',[OrderController::class,'destroy']);
});

