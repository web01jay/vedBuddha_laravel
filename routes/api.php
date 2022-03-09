<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PioneerController;
use App\Http\Controllers\ContactController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('banner', BannerController::class);
Route::resource('product-category', ProductCategoryController::class);
Route::resource('product-sub-category', ProductSubCategoryController::class);
Route::resource('product', ProductController::class);
Route::resource('pioneer', PioneerController::class);
Route::resource('contact', ContactController::class)->except(['edit', 'update']);
