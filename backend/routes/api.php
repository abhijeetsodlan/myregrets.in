<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SocioliteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// CSRF Cookie
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Authentication
Route::post('register', [RegisterController::class, 'register']);
Route::post('userLogin', [RegisterController::class, 'userLogin']);
// Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'userLogout']); 

// Users
Route::get('users', [RegisterController::class, 'getAllUsers'])->name('users');

// Questions (PROTECTED)
Route::middleware('auth:sanctum')->post('question', [QuestionController::class, 'store'])->name('question');
Route::get('questions', [QuestionController::class, 'getAllQuestions']);
Route::get('/questions/{id}', [QuestionController::class, 'getQuestionById']);
Route::get('/questions/category/{categoryId}', [QuestionController::class, 'getQuestionsByCategory']);
Route::get('/categories', [CategoryController::class, 'index']);

// Toggle like/unlike on a question
Route::middleware('auth:sanctum')->post('/questions/{question_id}/like', [LikesController::class, 'toggleLike']);
Route::middleware('auth:sanctum')->post('/logout', [SocioliteController::class, 'logout']);