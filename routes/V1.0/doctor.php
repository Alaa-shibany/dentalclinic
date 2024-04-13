<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
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
Route::post('signup/',[AuthController::class,'signup']);
Route::post('login',[AuthController::class,'doctorLogin']);
Route::post('submitCode/',[AuthController::class,'verifyCode'])
->middleware(['auth:sanctum','ability:doctor']);
Route::middleware(['auth:sanctum','ability:doctor,admin,superAdmin','verify.mobile'])->group(function(){
    Route::post('logout',[AuthController::class,'logout']);
    Route::apiResource('galleryPieces',GalleryPieceController::class)
    ->only('index');
    Route::post('orders/',[OrderController::class,'store']);
    Route::get('viewProfile/',[DoctorController::class,'viewProfile']);
    Route::post('editProfile/',[DoctorController::class,'editProfile']);
    Route::get('orders/{order}',[OrderController::class,'show']);
    Route::get('orders/',[OrderController::class,'showDoctorsOrders']);
});

