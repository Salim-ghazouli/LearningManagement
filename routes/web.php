
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resend'])
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