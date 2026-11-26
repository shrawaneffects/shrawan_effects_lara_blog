<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicApiController;

/*
|--------------------------------------------------------------------------
| Public API Routes for Next.js Frontend
|--------------------------------------------------------------------------
*/

Route::get('/settings', [PublicApiController::class, 'settings']);
Route::get('/home', [PublicApiController::class, 'home']);
Route::get('/posts', [PublicApiController::class, 'posts']);
Route::get('/posts/{slug}', [PublicApiController::class, 'post']);
Route::get('/categories', [PublicApiController::class, 'categories']);
Route::get('/tags', [PublicApiController::class, 'tags']);
Route::get('/author/{id}', [PublicApiController::class, 'author']);

Route::post('/contact', [PublicApiController::class, 'contact']);
Route::post('/newsletter', [PublicApiController::class, 'newsletter']);
Route::post('/posts/{slug}/comments', [PublicApiController::class, 'comment']);
