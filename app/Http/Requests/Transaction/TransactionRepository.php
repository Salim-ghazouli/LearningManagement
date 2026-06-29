<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\Course;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{

    public function createTransaction(array $data)
    {
        return Transaction::create($data);
    }

    public function findBySessionId($sessionId)
    {
        return Transaction::where('stripe_session_id', $sessionId)->first();
    }

    
    public function completePaymentAndEnroll($sessionId)
    {
        DB::transaction(function () use ($sessionId) {
            $transaction = Transaction::where('stripe_session_id', $sessionId)->firstOrFail();

            if ($transaction->status !== 'completed') {
                $transaction->update(['status' => 'completed']);

                $course = Course::findOrFail($transaction->course_id);
                $course->students()->attach($transaction->user_id, ['status' => 'active']);
            }
        });
    }
}
