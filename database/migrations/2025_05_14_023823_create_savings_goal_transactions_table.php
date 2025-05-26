<?php

use App\Models\SavingsGoal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('savings_goal_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SavingsGoal::class)->constrained()->cascadeOnDelete();
            $table->float('contribution_amount')->nullable();
            $table->float('withdrawal_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_goal_transactions');
    }
};
