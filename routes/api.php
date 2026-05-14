<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseMediaController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
Route::post('/courses/ShowCourses', [CourseController::class, 'Show_courses'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    //Roles
    Route::post('/admin/assign-role/{user_id}', [AdminController::class, 'assignRole']);
    Route::post('/admin/revoke-role/{user_id}', [AdminController::class, 'revokeRole']);
    Route::post('/admin/update-role/{user_id}', [AdminController::class, 'updateRole']);
    //courses
    Route::post('/courses/create', [CourseController::class, 'create_course']);
    Route::post('/courses/update/{course_id}', [CourseController::class, 'update']);
    Route::delete('/courses/delete/{course_id}', [CourseController::class, 'delete']);
    //media
    Route::post('/courses/media/upload/{course_id}', [CourseMediaController::class, 'upload']);
    Route::post('/courses/media/update/{course_id}', [CourseMediaController::class, 'updateMedia']);
    Route::delete('/courses/media/delete/{media_id}', [CourseMediaController::class, 'destroyMedia']);
});
Route::middleware(['auth:sanctum', 'role:Instructor'])->group(function () {
    Route::post('/courses/myCourses', [CourseController::class, 'getMyCourses']);
});
