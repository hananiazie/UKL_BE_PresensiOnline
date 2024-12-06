<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/createusers', [UserController::class, 'createusers']);
Route::put('users/{id}', [UserController::class, 'updateusers']); // Pastikan menggunakan metode PUT dan ID pada URL
Route::delete('/users/{id}', [UserController::class, 'deleteusers']);

use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
use App\Http\Controllers\Api\LoginController;

Route::post('login', LoginController::class);  // POST request to /api/login
use App\Http\Controllers\Api\PresenceController;

// Rute untuk mencatat presensi
Route::post('/attendance', [PresenceController::class, 'attendance']);

// Rute untuk melihat riwayat presensi
Route::get('/attendance/history/{user_id}', [PresenceController::class, 'history']);

// Rute untuk melihat rekap bulanan
Route::get('/attendance/summary/{user_id}', [PresenceController::class, 'summary']);

// Rute untuk analisis presensi
Route::post('/presence/analysis', [presenceController::class, 'analysis']); // Analisis tingkat kehadiran

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

Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');

