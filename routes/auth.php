<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('register', 'auth.register')
        ->name('register');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
