<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\CorsMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentifikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Ambil Profile dan Hapus Task Yang ada
Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);
Route::middleware('auth:sanctum')->delete('tasks/{task}', [TaskController::class, 'destroy']);

// Task Controller
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('tasks', TaskController::class);
// });
// Notfication DB
Route::middleware('auth:sanctum')->get('/notifications', function (Request $request) {
    return $request->user()->notifications;
});

// Controller Up
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks',[App\Http\Controllers\TaskController::class,'index'])->name('listtask');
    Route::post('/task', [App\Http\Controllers\TaskController::class,'store'])->name('tasksimpan');
    Route::put('/tasks/{id}',[App\Http\Controllers\TaskController::class,'update'])->name('updatetugas');
});
// Swagger
Route::get('/docs', function() {
    return response()->json(json_decode(file_get_contents(public_path('docs/api-docs.json'))));
});

Route::middleware([CorsMiddleware::class])->group(function () {
    Route::get('/data', function () {
        return response()->json(['message' => 'CORS enabled']);
    });
});
