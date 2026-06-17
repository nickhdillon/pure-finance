<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PlannedExpense;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planned_expense_months', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PlannedExpense::class)->constrained();
            $table->date('month');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->unique(['planned_expense_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_expense_months');
    }
};
