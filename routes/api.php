<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;

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

// Route::get('{any}', function () {
//     return view('welcome');
// })->where('any', '.*');

Auth::routes();

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('companies', CompanyController::class);
Route::resource('articles', ArticleController::class);
Route::resource('users', UserController::class);
Route::post('/companies/{id}', [CompanyController::class, 'update'])->where('id', '[0-9]+');
Route::post('/articles/{id}', [ArticleController::class, 'update'])->where('id', '[0-9]+');
Route::get('/articles/user/{id}', [ArticleController::class, 'getUserArticles'])->where('id', '[0-9]+');
Route::post('/users/{id}', [UserController::class, 'update'])->where('id', '[0-9]+');
