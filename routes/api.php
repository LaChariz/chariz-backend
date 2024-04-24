<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// AUTHENTICATION
Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

// GALLERY ADMIN
Route::post('gallery', [GalleryController::class, 'store']);
Route::put('gallery/{galleryId}', [GalleryController::class, 'update']);
Route::delete('gallery/{galleryId}', [GalleryController::class, 'destroy']);

// PROJECT ADMIN
Route::post('project', [ProjectController::class, 'store']);
Route::put('project/{projectId}', [ProjectController::class, 'update']);
Route::delete('project/{projectId}', [ProjectController::class, 'destroy']);

// PRODUCT CATEGORY ADMIN
Route::post('product-category', [CategoryController::class, 'store']);
Route::put('product-category/{productCategoryId}', [CategoryController::class, 'update']);
Route::delete('product-category/{productCategoryId}', [CategoryController::class, 'destroy']);

// PRODUCT TAG ADMIN
Route::post('product-tag', [TagController::class, 'store']);
Route::put('product-tag/{productTagId}', [TagController::class, 'update']);
Route::delete('product-tag/{productTagId}', [TagController::class, 'destroy']);

// PRODUCTS ADMIN
Route::post('product', [ProductController::class, 'store']);
Route::put('product/{productId}', [ProductController::class, 'update']);
Route::delete('product/{productId}', [ProductController::class, 'destroy']);

// GALLERY
Route::get('gallery', [GalleryController::class, 'index']);
Route::get('gallery/{galleryId}', [GalleryController::class, 'show']);

// PROJECT
Route::get('projects', [ProjectController::class, 'index']);
Route::get('project/{projectId}', [ProjectController::class, 'show']);

// PRODUCT CATEGORY
Route::get('product-categories', [CategoryController::class, 'index']);
Route::get('product-category/{productCategoryId}', [CategoryController::class, 'show']);

// PRODUCT TAG
Route::get('product-tags', [TagController::class, 'index']);
Route::get('product-tag/{productTagId}', [TagController::class, 'show']);

// PRODUCTS
Route::get('products', [ProductController::class, 'index']);
Route::get('product/{productId}', [ProductController::class, 'show']);

// CART
Route::post('cart', [CartController::class, 'addToCart']);
Route::get('cart', [CartController::class, 'viewCart']);
Route::post('cart/{cartItemId}', [CartController::class, 'removeFromCart']);
Route::put('cart', [CartController::class, 'updateCart']);


Route::middleware('auth:sanctum')->group(function () {

});

