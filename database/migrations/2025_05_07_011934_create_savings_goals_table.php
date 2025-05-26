<?php

use App\Models\Account;
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
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)->constrained();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->float('goal_amount');
            $table->float('amount_saved')->nullable();
            $table->float('monthly_contribution');
            $table->dateTime('last_contributed')->nullable();
            $table->boolean('target')->default(true);
            $table->string('target_month')->nullable();
            $table->string('target_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_goals');
    }
};
