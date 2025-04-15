<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use App\Models\PlannedExpense;
use Illuminate\Console\Command;

class GeneratePlannedExpenseSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-planned-expense-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing planned expenses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (PlannedExpense::select(['id', 'name'])->get() as $expense) {
            $expense->update(['slug' => Str::slug($expense->name)]);
        }
    }
}
