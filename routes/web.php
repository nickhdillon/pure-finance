<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use App\Livewire\Reports;
use App\Livewire\Accounts;
use App\Livewire\TagTable;
use App\Models\Transaction;
use App\Livewire\ReportForm;
use App\Livewire\ReportView;
use App\Livewire\BillCalendar;
use App\Livewire\SavingsGoals;
use App\Livewire\CategoryTable;
use App\Livewire\AccountOverview;
use App\Livewire\PlannedSpending;
use App\Livewire\SavingsGoalForm;
use App\Livewire\SavingsGoalView;
use App\Livewire\TransactionForm;
use App\Livewire\TransactionTable;
use App\Livewire\PlannedExpenseView;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintReportController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('accounts', Accounts::class)->name('accounts');

    Route::get('account-overview/{account:slug}', AccountOverview::class)
        ->can('update', 'account')
        ->name('account-overview');

    Route::get('account/{account:slug}/transaction-form', TransactionForm::class)
        ->can('update', 'account')
        ->name('account.transaction-form');

    Route::get('planned-spending', PlannedSpending::class)->name('planned-spending');

    Route::get('planned-expense/{expense:slug}', PlannedExpenseView::class)
        ->name('planned-expense-view');

    Route::get('savings-goals', SavingsGoals::class)->name('savings-goals');

    Route::get('savings-goal/{savings_goal:slug}', SavingsGoalView::class)
        ->can('view', 'savings_goal')
        ->name('savings-goal-view');

    Route::get('savings-goal-form', SavingsGoalForm::class)
        ->name('create-savings-goal');

    Route::get('savings-goal-form/{savings_goal:slug}', SavingsGoalForm::class)
        ->can('update', 'savings_goal')
        ->name('edit-savings-goal');

    Route::get('bill-calendar/{bill?}', BillCalendar::class)->name('bill-calendar');

    Route::get('transactions', TransactionTable::class)->name('transactions');

    Route::get('transaction-form', TransactionForm::class)
        ->can('create', Transaction::class)
        ->name('create-transaction');

    Route::get('transaction-form/{transaction:slug}', TransactionForm::class)
        ->can('update', 'transaction')
        ->name('edit-transaction');

    Route::get('reports', Reports::class)->name('reports');

    Route::get('report-form', ReportForm::class)->name('report-form');

    Route::get('report-view/{report:slug}', ReportView::class)->name('report-view');

    Route::get('/reports/{report:slug}/print', PrintReportController::class)
        ->name('print-report');

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

Route::view('privacy-policy', 'legal.privacy-policy')->name('privacy-policy');

Route::view('terms-and-conditions', 'legal.terms-and-conditions')->name('terms-and-conditions');

require __DIR__ . '/auth.php';
