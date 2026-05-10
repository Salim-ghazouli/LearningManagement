<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::post('/admin/assign-role', [AdminController::class, 'assignRole']);
    Route::post('/admin/revoke-role', [AdminController::class, 'revokeRole']);
    Route::post('/admin/update-role', [AdminController::class, 'updateRole']);
});
Route::middleware(['auth:sanctum', 'role:Instructor'])->group(function () {
    Route::post('/courses/create', [CourseController::class, 'create_course']);
    Route::post('/courses/update', [CourseController::class, 'update']);
    Route::post('/courses/delete', [CourseController::class, 'delete'])->middleware('role:Admin|Instructor');
    Route::post('/courses/myCourses', [CourseController::class, 'getMyCourses']);
});
Route::post('/courses/ShowCourses', [CourseController::class, 'Show_courses'])->middleware('auth:sanctum');


