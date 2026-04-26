
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
Route::get('/password/reset/{token}', function (string $token) {
    return response()->json([
        'message' => 'The token has been generated. Please use this token to reset your password via the POST method.',
        'token' => $token
    ]);
})->name('password.reset');

/*Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/profile', function () {
        return auth()->user();
    });
});
*/