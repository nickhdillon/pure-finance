<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use App\Livewire\TagTable;
use App\Livewire\Accounts;
use App\Models\Transaction;
use App\Livewire\CategoryTable;
use App\Livewire\TransactionForm;
use App\Livewire\AccountOverview;
use App\Livewire\PlannedSpending;
use App\Livewire\TransactionTable;
use App\Livewire\PlannedExpenseView;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('accounts', Accounts::class)->name('accounts');

    Route::get('account-overview/{account}', AccountOverview::class)
        ->can('update', 'account')
        ->name('account-overview');

    Route::get('account/{account}/transaction-form', TransactionForm::class)
        ->can('update', 'account')
        ->name('account.transaction-form');

    Route::get('planned-spending', PlannedSpending::class)->name('planned-spending');

    Route::get('planned-expense/{expense}', PlannedExpenseView::class)
        ->name('planned-expense-view');

    Route::get('transactions', TransactionTable::class)->name('transactions');

    Route::get('transaction-form', TransactionForm::class)
        ->can('create', Transaction::class)
        ->name('create-transaction');

    Route::get('transaction-form/{transaction?}', TransactionForm::class)
        ->can('update', 'transaction')
        ->name('edit-transaction');

    Route::get('categories', CategoryTable::class)->name('categories');

    Route::get('tags', TagTable::class)->name('tags');

    // ----- Settings -----
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
