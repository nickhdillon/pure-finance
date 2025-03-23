<?php

use Livewire\Volt\Volt;
use App\Livewire\TagTable;
use App\Livewire\CategoryTable;
use App\Livewire\TransactionForm;
use App\Livewire\TransactionTable;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('transactions', TransactionTable::class)->name('transactions');

    Route::get('account/{account}/transaction-form/{transaction?}', TransactionForm::class)->name('account.transaction-form');

    Route::get('transaction-form/{transaction?}', TransactionForm::class)->name('transaction-form');

    Route::get('categories', CategoryTable::class)->name('categories');

    Route::get('tags', TagTable::class)->name('tags');

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
