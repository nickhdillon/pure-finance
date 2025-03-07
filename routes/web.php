<?php

use Livewire\Volt\Volt;
use App\Livewire\CategoryTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('categories', CategoryTable::class)->name('categories');

    # ----- Settings -----
    Route::prefix('settings')->group(function () {
        Route::redirect('/', 'settings.profile');

        Route::name('settings.')->group(function () {
            Volt::route('profile', 'settings.profile')->name('profile');
            Volt::route('password', 'settings.password')->name('password');
            Volt::route('appearance', 'settings.appearance')->name('appearance');
        });
    });
});

require __DIR__ . '/auth.php';
