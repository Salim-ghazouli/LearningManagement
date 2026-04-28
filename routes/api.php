<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

Route::post('/admin/assign-role', [AdminController::class, 'assignRole'])->middleware('auth:sanctum', 'role:admin');
