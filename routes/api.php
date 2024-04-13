<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProjectController;
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

// GALLERY
Route::get('gallery', [GalleryController::class, 'index']);
Route::get('gallery/{galleryId}', [GalleryController::class, 'show']);

// PROJECT
Route::get('project', [ProjectController::class, 'index']);
Route::get('project/{projectId}', [ProjectController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

});

