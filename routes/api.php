<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseMediaController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
Route::get('/lessons/{id}', [LessonController::class, 'show']);
Route::middleware(['auth:sanctum'])->group(
    function () {

        Route::post('/devices/register/token', [DeviceController::class, 'storeToken']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('/courses/ShowCourses', [CourseController::class, 'Show_courses'])->middleware('auth:sanctum');
        Route::get('/lessons/get/{lesson_id}', [LessonController::class, 'show']);
        Route::post('/lessons/byCourse/{course_id}', [LessonController::class, 'getByCourse']);
    }
);

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
    //news
    Route::post('/news/create', [NewsController::class, 'store']);
    Route::post('/news/update/{new_id}', [NewsController::class, 'update']);
    Route::delete('/news/destroy/{new_id}', [NewsController::class, 'destroy']);
});
Route::get('/news/latestnews', [NewsController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Instructor'])->group(function () {
    Route::post('/courses/myCourses', [CourseController::class, 'getMyCourses']);
});
Route::middleware(['auth:sanctum', 'role:Admin|Instructor'])->group(function () {
    Route::post('/lessons/create', [LessonController::class, 'create']);
    Route::post('/lessons/update/{lesson_id}', [LessonController::class, 'update']);
    Route::delete('/lessons/delete/{lesson_id}', [LessonController::class, 'destroy']);
});
Route::middleware(['role:Admin|Instructor|Student', 'auth:sanctum'])->group(function () {
    Route::post('/reviews/create', [ReviewController::class, 'create']);
    Route::post('/reviews/update/{review_id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/destroy/{review_id}', [ReviewController::class, 'destroy']);
});
Route::get('/courses/reviews/{course_id}', [ReviewController::class, 'show_review_ByCourse']);
Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:Admin|Instructor|Student'])->group(function () {
        Route::post('/enrollments', [EnrollmentController::class, 'enroll']);
        Route::post('/myErolledCurses', [EnrollmentController::class, 'myCourses']);
    });

    Route::middleware(['role:Admin|Instructor'])->group(function () {
        Route::get('/courses/students/{courseId}', [EnrollmentController::class, 'courseStudents']);
        Route::post('/enrollments/updateSatus', [EnrollmentController::class, 'updateStatus']);
    });
});
Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/coupons', [CouponController::class, 'store']);
    });

    Route::middleware(['role:Admin|Instructor|Student'])->group(function () {
        Route::post('/coupons/calculatePrice', [CouponController::class, 'calculate']);
    });
});



Route::middleware(['auth:sanctum', 'role:Student|Admin'])->group(function () {
    Route::post('/payment/checkout', [PaymentController::class, 'checkout']); //4242  4242  4242  4242
});

Route::post('/stripe/webhook', [PaymentController::class, 'webhook']);
Route::get('/payment/success', [PaymentController::class, 'success']);
Route::get('/payment/cancel', [PaymentController::class, 'cancel']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/conversation', [ChatController::class, 'startConversation']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/conversation/messages', [ChatController::class, 'getMessages']);
});
