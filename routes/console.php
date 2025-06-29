<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessRecurringTransactionsJob;

Schedule::job(new ProcessRecurringTransactionsJob)->daily();
Schedule::command('send-bill-alerts')->everyThirtyMinutes();
