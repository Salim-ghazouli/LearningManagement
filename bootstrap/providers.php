<?php

use App\Providers\AppServiceProvider;
use Kreait\LaravelFirebase\ServiceProvider;

return [
    AppServiceProvider::class,
    
    'Kreait\LaravelFirebase\ServiceProvider',
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
];
