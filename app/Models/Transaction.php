<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'course_id', 'stripe_session_id', 'amount', 'currency', 'status'];
}
